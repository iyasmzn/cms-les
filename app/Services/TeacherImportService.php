<?php

namespace App\Services;

use App\Models\Teacher;
use Illuminate\Support\Facades\Validator;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Throwable;

class TeacherImportService
{
    /**
     * Canonical column order used when generating the import template and export.
     *
     * @var array<string, string>
     */
    public const TEMPLATE_COLUMNS = [
        'name' => 'Nama',
        'nip' => 'NIP',
        'position' => 'Jabatan',
        'subject' => 'Mata Pelajaran',
        'education' => 'Pendidikan',
        'phone' => 'Telepon',
        'email' => 'Email',
        'whatsapp' => 'WhatsApp',
        'is_active' => 'Aktif',
        'sort_order' => 'Urutan',
    ];

    /**
     * Header aliases (normalized) mapped to the canonical attribute name.
     *
     * @var array<string, string>
     */
    private const HEADER_ALIASES = [
        'nama' => 'name',
        'nama lengkap' => 'name',
        'nama guru' => 'name',
        'nip' => 'nip',
        'jabatan' => 'position',
        'posisi' => 'position',
        'mata pelajaran' => 'subject',
        'mapel' => 'subject',
        'pelajaran' => 'subject',
        'pendidikan' => 'education',
        'pendidikan terakhir' => 'education',
        'telepon' => 'phone',
        'telpon' => 'phone',
        'no hp' => 'phone',
        'nomor hp' => 'phone',
        'phone' => 'phone',
        'email' => 'email',
        'surel' => 'email',
        'whatsapp' => 'whatsapp',
        'wa' => 'whatsapp',
        'no wa' => 'whatsapp',
        'aktif' => 'is_active',
        'status' => 'is_active',
        'urutan' => 'sort_order',
        'sort' => 'sort_order',
        'sort order' => 'sort_order',
    ];

    private const TRUTHY = ['ya', 'yes', 'y', 'true', '1', 'benar', 'aktif', 'active'];

    /**
     * Import teachers from an .xlsx file at the given absolute path.
     *
     * Rows are matched on `nip` when present (update), otherwise a new record is
     * created. Boolean cells that Excel may store in inconsistent shapes are
     * normalized before persisting.
     */
    public function import(string $absolutePath): TeacherImportResult
    {
        $result = new TeacherImportResult;

        $reader = new Reader;
        $reader->open($absolutePath);

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                $columnMap = null;

                foreach ($sheet->getRowIterator() as $rowNumber => $row) {
                    $values = $row->toArray();

                    if ($columnMap === null) {
                        $columnMap = $this->mapHeaders($values);

                        continue;
                    }

                    if ($this->isBlankRow($values)) {
                        continue;
                    }

                    $this->importRow($values, $columnMap, $rowNumber, $result);
                }

                break; // Only the first sheet is imported.
            }
        } finally {
            $reader->close();
        }

        return $result;
    }

    /**
     * Write a ready-to-fill .xlsx import template (header row + one example
     * row) to the given absolute path.
     */
    public function writeTemplate(string $absolutePath): void
    {
        $writer = new Writer;
        $writer->openToFile($absolutePath);

        try {
            $writer->addRow(Row::fromValues(array_values(self::TEMPLATE_COLUMNS)));
            $writer->addRow(Row::fromValues([
                'Ahmad Fauzi, S.Pd.', '198501012010011001', 'Guru', 'Matematika',
                'S1 Pendidikan Matematika', '081234567890', 'ahmad.fauzi@example.com',
                '081234567890', 'Ya', 1,
            ]));
        } finally {
            $writer->close();
        }
    }

    /**
     * Write every teacher to an .xlsx export file at the given absolute path.
     */
    public function writeExport(string $absolutePath): void
    {
        $writer = new Writer;
        $writer->openToFile($absolutePath);

        try {
            $writer->addRow(Row::fromValues(array_values(self::TEMPLATE_COLUMNS)));

            Teacher::query()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->each(function (Teacher $teacher) use ($writer): void {
                    $writer->addRow(Row::fromValues([
                        $teacher->name,
                        $teacher->nip,
                        $teacher->position,
                        $teacher->subject,
                        $teacher->education,
                        $teacher->phone,
                        $teacher->email,
                        $teacher->whatsapp,
                        $teacher->is_active ? 'Ya' : 'Tidak',
                        $teacher->sort_order,
                    ]));
                });
        } finally {
            $writer->close();
        }
    }

    /**
     * @param  list<mixed>  $values
     * @param  array<int, string>  $columnMap
     */
    private function importRow(array $values, array $columnMap, int $rowNumber, TeacherImportResult $result): void
    {
        $attributes = $this->extractAttributes($values, $columnMap);

        if (blank($attributes['name'] ?? null)) {
            $this->recordFailure($result, $rowNumber, 'Kolom Nama wajib diisi.', $attributes);

            return;
        }

        $validator = Validator::make($attributes, [
            'name' => ['required', 'string', 'max:150'],
            'nip' => ['nullable', 'string', 'max:30'],
            'position' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            $this->recordFailure($result, $rowNumber, implode(' ', $validator->errors()->all()), $attributes);

            return;
        }

        try {
            $nip = $attributes['nip'] ?? null;

            $existing = filled($nip)
                ? Teacher::query()->where('nip', $nip)->first()
                : null;

            if ($existing !== null) {
                $existing->fill($attributes)->save();
                $result->updated++;
                $result->imported[] = ['row' => $rowNumber, 'action' => 'updated', 'attributes' => $attributes];

                return;
            }

            Teacher::create($attributes);
            $result->created++;
            $result->imported[] = ['row' => $rowNumber, 'action' => 'created', 'attributes' => $attributes];
        } catch (Throwable $e) {
            $this->recordFailure($result, $rowNumber, "Gagal disimpan ({$e->getMessage()}).", $attributes);
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function recordFailure(TeacherImportResult $result, int $rowNumber, string $reason, array $attributes): void
    {
        $result->failures[] = ['row' => $rowNumber, 'reason' => $reason, 'attributes' => $attributes];
    }

    /**
     * @param  list<mixed>  $values
     * @param  array<int, string>  $columnMap
     * @return array<string, mixed>
     */
    private function extractAttributes(array $values, array $columnMap): array
    {
        $attributes = [];

        foreach ($columnMap as $index => $attribute) {
            $attributes[$attribute] = $values[$index] ?? null;
        }

        foreach (['name', 'nip', 'position', 'subject', 'education', 'phone', 'email', 'whatsapp'] as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = $this->toNullableString($attributes[$field]);
            }
        }

        if (array_key_exists('sort_order', $attributes)) {
            $sortOrder = $this->normalizeInteger($attributes['sort_order']);

            // Drop the key when blank so the column default (0) applies on create
            // and an existing value is preserved on update, rather than forcing NULL
            // into a NOT NULL column.
            if ($sortOrder === null) {
                unset($attributes['sort_order']);
            } else {
                $attributes['sort_order'] = $sortOrder;
            }
        }

        $attributes['is_active'] = $this->normalizeBoolean($attributes['is_active'] ?? null, true);

        return $attributes;
    }

    private function normalizeInteger(mixed $value): ?int
    {
        $string = $this->toNullableString($value);

        if ($string === null || ! preg_match('/-?\d+/', $string, $matches)) {
            return null;
        }

        return (int) $matches[0];
    }

    private function normalizeBoolean(mixed $value, bool $default = false): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $string = $this->toNullableString($value);

        if ($string === null) {
            return $default;
        }

        return in_array(mb_strtolower($string), self::TRUTHY, true);
    }

    private function toNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_float($value) && floor($value) === $value) {
            $value = (int) $value;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }

    /**
     * @param  list<mixed>  $headerValues
     * @return array<int, string> Column index => canonical attribute name.
     */
    private function mapHeaders(array $headerValues): array
    {
        $map = [];

        foreach ($headerValues as $index => $header) {
            $normalized = $this->normalizeHeader($header);

            if ($normalized !== null && isset(self::HEADER_ALIASES[$normalized])) {
                $map[$index] = self::HEADER_ALIASES[$normalized];
            }
        }

        return $map;
    }

    private function normalizeHeader(mixed $header): ?string
    {
        if (! is_string($header) && ! is_numeric($header)) {
            return null;
        }

        $normalized = mb_strtolower(trim((string) $header));
        $normalized = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $normalized) ?? '';
        $normalized = trim(preg_replace('/\s+/', ' ', $normalized) ?? '');

        return $normalized === '' ? null : $normalized;
    }

    /**
     * @param  list<mixed>  $values
     */
    private function isBlankRow(array $values): bool
    {
        foreach ($values as $value) {
            if ($this->toNullableString($value) !== null) {
                return false;
            }
        }

        return true;
    }
}
