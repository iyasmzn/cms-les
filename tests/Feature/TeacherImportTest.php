<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Services\TeacherImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Tests\TestCase;

class TeacherImportTest extends TestCase
{
    use RefreshDatabase;

    private TeacherImportService $service;

    /** @var list<string> */
    private array $tempFiles = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TeacherImportService;
    }

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    // ── Full import ─────────────────────────────────────────────────────────

    public function test_imports_valid_rows(): void
    {
        $path = $this->makeXlsx([
            $this->header(),
            ['Ahmad Fauzi', '1990', 'Guru', 'Matematika', 'S1', '0811', 'ahmad@example.com', '628111', 'Ya', 3],
            ['Siti Aminah', '1991', 'Kepala Sekolah', 'Bahasa', 'S2', '0812', 'siti@example.com', '628222', 'Tidak', 1],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(2, $result->created);
        $this->assertSame(0, $result->updated);
        $this->assertFalse($result->hasErrors());

        $ahmad = Teacher::where('nip', '1990')->firstOrFail();
        $this->assertSame('Ahmad Fauzi', $ahmad->name);
        $this->assertTrue($ahmad->is_active);
        $this->assertSame(3, $ahmad->sort_order);

        $siti = Teacher::where('nip', '1991')->firstOrFail();
        $this->assertFalse($siti->is_active);
    }

    public function test_is_active_defaults_to_true_when_blank(): void
    {
        $path = $this->makeXlsx([
            $this->header(),
            ['Tanpa Status', '2001', 'Guru', 'Fisika', 'S1', '', '', '', '', ''],
        ]);

        $this->service->import($path);

        $teacher = Teacher::where('nip', '2001')->firstOrFail();
        $this->assertTrue($teacher->is_active);
        $this->assertSame(0, $teacher->sort_order); // column default applies when blank
    }

    public function test_reports_rows_without_a_name_as_failed(): void
    {
        $path = $this->makeXlsx([
            $this->header(),
            ['', '9009', 'Guru', '', '', '', '', '', 'Ya', ''],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(0, $result->created);
        $this->assertSame(1, $result->failed());
        $this->assertStringContainsString('Nama', $result->failures[0]['reason']);
        $this->assertSame(0, Teacher::count());
    }

    public function test_imports_valid_rows_and_reports_invalid_ones(): void
    {
        $path = $this->makeXlsx([
            $this->header(),
            ['Guru Valid', 'V-1', 'Guru', '', '', '', 'valid@example.com', '', 'Ya', 1],
            ['Email Salah', 'V-2', 'Guru', '', '', '', 'bukan-email', '', 'Ya', 2],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(1, $result->created);
        $this->assertSame(1, $result->failed());
        $this->assertStringContainsString('Baris 3', $result->errorMessages()[0]);
        $this->assertDatabaseHas(Teacher::class, ['nip' => 'V-1']);
        $this->assertDatabaseMissing(Teacher::class, ['nip' => 'V-2']);
    }

    public function test_updates_existing_record_matched_by_nip(): void
    {
        Teacher::factory()->create([
            'name' => 'Nama Lama',
            'nip' => '198501012010011001',
            'position' => 'Guru',
        ]);

        $path = $this->makeXlsx([
            $this->header(),
            ['Nama Baru', '198501012010011001', 'Wakil Kepala Sekolah', '', '', '', '', '', 'Ya', 5],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(0, $result->created);
        $this->assertSame(1, $result->updated);
        $this->assertSame(1, Teacher::count());

        $this->assertDatabaseHas(Teacher::class, [
            'nip' => '198501012010011001',
            'name' => 'Nama Baru',
            'position' => 'Wakil Kepala Sekolah',
        ]);
    }

    public function test_rows_without_nip_always_create_new_records(): void
    {
        $path = $this->makeXlsx([
            $this->header(),
            ['Guru A', '', 'Guru', '', '', '', '', '', 'Ya', 1],
            ['Guru B', '', 'Guru', '', '', '', '', '', 'Ya', 2],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(2, $result->created);
        $this->assertSame(2, Teacher::count());
    }

    public function test_header_aliases_are_recognized(): void
    {
        // Alternate but valid header labels the alias table should map.
        $path = $this->makeXlsx([
            ['Nama Guru', 'NIP', 'Posisi', 'Mapel', 'Pendidikan Terakhir', 'No HP', 'Surel', 'WA', 'Status', 'Sort'],
            ['Guru Alias', '3003', 'Guru', 'Kimia', 'S1', '0813', 'alias@example.com', '628333', 'Ya', 7],
        ]);

        $result = $this->service->import($path);

        $this->assertSame(1, $result->created);
        $teacher = Teacher::where('nip', '3003')->firstOrFail();
        $this->assertSame('Guru Alias', $teacher->name);
        $this->assertSame('Kimia', $teacher->subject);
        $this->assertSame(7, $teacher->sort_order);
    }

    public function test_template_can_be_generated_and_round_trips(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'teacher_tpl_').'.xlsx';
        $this->tempFiles[] = $path;

        $this->service->writeTemplate($path);

        // The generated template must be importable by the same service.
        $result = $this->service->import($path);

        $this->assertSame(1, $result->created);
        $this->assertDatabaseHas(Teacher::class, ['name' => 'Ahmad Fauzi, S.Pd.']);
    }

    public function test_export_writes_all_teachers_with_header(): void
    {
        Teacher::factory()->create(['name' => 'Guru Satu', 'nip' => 'E-1', 'is_active' => true, 'sort_order' => 1]);
        Teacher::factory()->create(['name' => 'Guru Dua', 'nip' => 'E-2', 'is_active' => false, 'sort_order' => 2]);

        $path = tempnam(sys_get_temp_dir(), 'teacher_exp_').'.xlsx';
        $this->tempFiles[] = $path;

        $this->service->writeExport($path);

        $rows = $this->readXlsx($path);

        $this->assertSame(array_values(TeacherImportService::TEMPLATE_COLUMNS), $rows[0]);
        $this->assertCount(3, $rows); // header + 2 teachers
        $names = [$rows[1][0], $rows[2][0]];
        $this->assertContains('Guru Satu', $names);
        $this->assertContains('Guru Dua', $names);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /**
     * @return list<string>
     */
    private function header(): array
    {
        return array_values(TeacherImportService::TEMPLATE_COLUMNS);
    }

    /**
     * @param  list<list<mixed>>  $rows
     */
    private function makeXlsx(array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'teacher_test_').'.xlsx';
        $this->tempFiles[] = $path;

        $writer = new Writer;
        $writer->openToFile($path);

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return $path;
    }

    /**
     * @return list<list<mixed>>
     */
    private function readXlsx(string $path): array
    {
        $reader = new Reader;
        $reader->open($path);

        $rows = [];

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = $row->toArray();
            }

            break;
        }

        $reader->close();

        return $rows;
    }
}
