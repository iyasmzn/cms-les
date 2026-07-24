<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Institution;
use App\Models\RegistrationWave;
use App\Models\Setting;
use App\Models\SpmbRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class SpmbRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private Institution $institution;

    private AcademicYear $year;

    private RegistrationWave $openWave;

    private AdmissionPath $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = Institution::factory()->create(['slug' => 'smp']);
        $this->year = AcademicYear::factory()->active()->create();
        $this->openWave = RegistrationWave::factory()->open()->create([
            'academic_year_id' => $this->year->id,
            'institution_id' => $this->institution->id,
        ]);
        $this->path = AdmissionPath::firstOrCreate(
            ['slug' => 'zonasi'],
            ['name' => 'Zonasi', 'is_active' => true],
        );
        Setting::set('spmb_form_enabled', '1');
    }

    public function test_ppdb_index_redirects_to_the_only_active_jenjang(): void
    {
        $response = $this->get(route('ppdb.index'));

        $response->assertRedirect(route('ppdb.show', $this->institution));
    }

    public function test_ppdb_index_lists_jenjang_when_several_are_active(): void
    {
        Institution::factory()->create(['slug' => 'sma']);

        $response = $this->get(route('ppdb.index'));

        $response->assertStatus(200);
        $response->assertSee('Pilih Jenjang');
    }

    public function test_jenjang_ppdb_page_is_accessible(): void
    {
        $response = $this->get(route('ppdb.show', $this->institution));

        $response->assertStatus(200);
        $response->assertSee('PPDB');
    }

    public function test_homepage_spmb_section_links_to_open_jenjang(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Pilih Jenjang');
        $response->assertSee(route('ppdb.show', $this->institution), false);
    }

    public function test_external_link_jenjang_shows_cta_and_no_internal_form(): void
    {
        $jenjang = Institution::factory()->externalLink('https://ppdb.contoh.test/daftar')->create(['slug' => 'sma']);

        $response = $this->get(route('ppdb.show', $jenjang));

        $response->assertStatus(200);
        $response->assertSee('https://ppdb.contoh.test/daftar');
        $response->assertDontSee('name="nik"', false);
    }

    public function test_embed_jenjang_renders_iframe(): void
    {
        $jenjang = Institution::factory()->embed('https://forms.contoh.test/embed')->create(['slug' => 'sma']);

        $response = $this->get(route('ppdb.show', $jenjang));

        $response->assertStatus(200);
        $response->assertSee('<iframe', false);
        $response->assertSee('https://forms.contoh.test/embed');
        $response->assertDontSee('name="nik"', false);
    }

    public function test_store_is_rejected_for_a_non_internal_jenjang(): void
    {
        $jenjang = Institution::factory()->externalLink('https://ppdb.contoh.test/daftar')->create(['slug' => 'sma']);

        $response = $this->post(route('ppdb.store', $jenjang), [
            'full_name' => 'Tidak Tersimpan',
            'nik' => '3273010101080007',
            'phone' => '081234567890',
            'previous_school' => 'SMP Negeri 9',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect(route('ppdb.show', $jenjang));

        $this->assertDatabaseMissing('spmb_registrations', ['full_name' => 'Tidak Tersimpan']);
    }

    public function test_registration_form_submission_creates_record_and_assigns_active_wave(): void
    {
        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Budi Santoso',
            'nik' => '3273010101080001',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'previous_school' => 'SMP Negeri 1 Bandung',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect(route('ppdb.show', $this->institution));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('spmb_registrations', [
            'full_name' => 'Budi Santoso',
            'nik' => '3273010101080001',
            'phone' => '081234567890',
            'admission_path_id' => $this->path->id,
            'institution_id' => $this->institution->id,
            'academic_year_id' => $this->year->id,
            'registration_wave_id' => $this->openWave->id,
            'status' => 'pending',
        ]);
    }

    public function test_registration_fails_when_no_wave_is_open(): void
    {
        $this->openWave->update([
            'start_date' => now()->subMonths(2),
            'end_date' => now()->subMonth(),
        ]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Ani Lestari',
            'phone' => '089876543210',
            'previous_school' => 'SMP Swasta',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('spmb_registrations', ['full_name' => 'Ani Lestari']);
    }

    public function test_registration_fails_for_a_jenjang_without_an_open_wave(): void
    {
        // A different jenjang has no open wave of its own, even though this one does.
        $otherJenjang = Institution::factory()->create(['slug' => 'sma']);

        $response = $this->post(route('ppdb.store', $otherJenjang), [
            'full_name' => 'Salah Jenjang',
            'nik' => '3273010101080009',
            'phone' => '081234567890',
            'previous_school' => 'SMP Negeri 5',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('spmb_registrations', ['full_name' => 'Salah Jenjang']);
    }

    public function test_registration_fails_when_form_is_disabled(): void
    {
        Setting::set('spmb_form_enabled', '0');

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Dewi Rahayu',
            'phone' => '08111111111',
            'previous_school' => 'SMP Negeri 2',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('spmb_registrations', ['full_name' => 'Dewi Rahayu']);
    }

    public function test_registration_validates_required_fields(): void
    {
        $response = $this->post(route('ppdb.store', $this->institution), []);

        $response->assertSessionHasErrors(['full_name', 'nik', 'phone', 'previous_school', 'admission_path_id']);
    }

    public function test_registration_rejects_duplicate_nik(): void
    {
        SpmbRegistration::factory()->create(['nik' => '3273010101080001']);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Calon Kedua',
            'nik' => '3273010101080001',
            'phone' => '081234567890',
            'previous_school' => 'SMP Negeri 3',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertSessionHasErrors(['nik']);
        $this->assertDatabaseMissing('spmb_registrations', ['full_name' => 'Calon Kedua']);
    }

    public function test_registration_rejects_invalid_nik_length(): void
    {
        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'NIK Pendek',
            'nik' => '12345',
            'phone' => '081234567890',
            'previous_school' => 'SMP Negeri 4',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertSessionHasErrors(['nik']);
    }

    public function test_registration_rejects_inactive_admission_path(): void
    {
        $inactive = AdmissionPath::factory()->create(['is_active' => false]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Test User',
            'nik' => '3273010101080002',
            'phone' => '081234567890',
            'previous_school' => 'SMP Test',
            'admission_path_id' => $inactive->id,
        ]);

        $response->assertSessionHasErrors(['admission_path_id']);
    }

    public function test_admission_path_can_be_shared_by_several_jenjang(): void
    {
        $sd = Institution::factory()->create(['slug' => 'sd']);
        $sma = Institution::factory()->create(['slug' => 'sma']);

        // No jenjang attached => shared with everyone.
        AdmissionPath::factory()->create(['slug' => 'jalur-bersama']);

        // Attached to SMP (setUp) + SD only.
        $khusus = AdmissionPath::factory()->create(['slug' => 'jalur-khusus']);
        $khusus->institutions()->attach([$this->institution->id, $sd->id]);

        $smp = AdmissionPath::query()->forInstitution($this->institution)->pluck('slug');
        $this->assertTrue($smp->contains('jalur-bersama'));
        $this->assertTrue($smp->contains('jalur-khusus'));

        $this->assertTrue(AdmissionPath::query()->forInstitution($sd)->pluck('slug')->contains('jalur-khusus'));

        // SMA only gets the shared one, not the SMP/SD-specific path.
        $smaPaths = AdmissionPath::query()->forInstitution($sma)->pluck('slug');
        $this->assertTrue($smaPaths->contains('jalur-bersama'));
        $this->assertFalse($smaPaths->contains('jalur-khusus'));
    }

    public function test_dynamic_form_renders_configured_fields(): void
    {
        $this->institution->ppdbFields()->createMany([
            ['key' => 'full_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['key' => 'hobby', 'label' => 'Hobi Favorit', 'type' => 'text', 'is_required' => false, 'sort_order' => 2],
        ]);

        $response = $this->get(route('ppdb.show', $this->institution));

        $response->assertStatus(200);
        $response->assertSee('Hobi Favorit');
        $response->assertSee('name="hobby"', false);
    }

    public function test_dynamic_submission_stores_known_columns_and_custom_data(): void
    {
        $this->institution->ppdbFields()->createMany([
            ['key' => 'full_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text', 'is_required' => true, 'sort_order' => 2],
            ['key' => 'phone', 'label' => 'No. HP', 'type' => 'tel', 'is_required' => true, 'sort_order' => 3],
            ['key' => 'previous_school', 'label' => 'Asal Sekolah', 'type' => 'text', 'is_required' => true, 'sort_order' => 4],
            ['key' => 'hobby', 'label' => 'Hobi', 'type' => 'text', 'is_required' => false, 'sort_order' => 5],
            ['key' => 'blood_type', 'label' => 'Golongan Darah', 'type' => 'select', 'options' => [['value' => 'A'], ['value' => 'B'], ['value' => 'O']], 'is_required' => true, 'sort_order' => 6],
        ]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Dinamis Satu',
            'nik' => '3273010101080011',
            'phone' => '081234567890',
            'previous_school' => 'SD Negeri 1',
            'hobby' => 'Membaca',
            'blood_type' => 'B',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect(route('ppdb.show', $this->institution));
        $response->assertSessionHas('success');

        $registration = SpmbRegistration::query()->where('nik', '3273010101080011')->first();

        $this->assertNotNull($registration);
        $this->assertSame('Dinamis Satu', $registration->full_name);
        $this->assertSame($this->institution->id, $registration->institution_id);
        $this->assertSame('Membaca', $registration->data['hobby']);
        $this->assertSame('B', $registration->data['blood_type']);
        $this->assertArrayNotHasKey('full_name', $registration->data ?? []);
    }

    public function test_dynamic_required_custom_field_is_enforced(): void
    {
        $this->institution->ppdbFields()->createMany([
            ['key' => 'full_name', 'label' => 'Nama', 'type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text', 'is_required' => true, 'sort_order' => 2],
            ['key' => 'phone', 'label' => 'HP', 'type' => 'tel', 'is_required' => true, 'sort_order' => 3],
            ['key' => 'previous_school', 'label' => 'Asal', 'type' => 'text', 'is_required' => true, 'sort_order' => 4],
            ['key' => 'guardian_job', 'label' => 'Pekerjaan Wali', 'type' => 'text', 'is_required' => true, 'sort_order' => 5],
        ]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Tanpa Wali',
            'nik' => '3273010101080012',
            'phone' => '081234567890',
            'previous_school' => 'SD Negeri 2',
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertSessionHasErrors(['guardian_job']);
        $this->assertDatabaseMissing('spmb_registrations', ['nik' => '3273010101080012']);
    }

    public function test_dynamic_file_field_stores_upload(): void
    {
        Storage::fake('local');

        $this->institution->ppdbFields()->createMany([
            ['key' => 'full_name', 'label' => 'Nama', 'type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text', 'is_required' => true, 'sort_order' => 2],
            ['key' => 'phone', 'label' => 'HP', 'type' => 'tel', 'is_required' => true, 'sort_order' => 3],
            ['key' => 'previous_school', 'label' => 'Asal', 'type' => 'text', 'is_required' => true, 'sort_order' => 4],
            ['key' => 'ijazah', 'label' => 'Ijazah', 'type' => 'file', 'is_required' => true, 'sort_order' => 5],
        ]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Berkas Satu',
            'nik' => '3273010101080021',
            'phone' => '081234567890',
            'previous_school' => 'SD Negeri 3',
            'ijazah' => UploadedFile::fake()->create('ijazah.pdf', 100, 'application/pdf'),
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertRedirect(route('ppdb.show', $this->institution));

        $registration = SpmbRegistration::query()->where('nik', '3273010101080021')->first();
        $this->assertNotNull($registration);

        $path = $registration->data['ijazah'] ?? null;
        $this->assertNotNull($path);
        Storage::disk('local')->assertExists($path);
    }

    public function test_file_field_rejects_disallowed_type(): void
    {
        Storage::fake('local');

        $this->institution->ppdbFields()->createMany([
            ['key' => 'full_name', 'label' => 'Nama', 'type' => 'text', 'is_required' => true, 'sort_order' => 1],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text', 'is_required' => true, 'sort_order' => 2],
            ['key' => 'phone', 'label' => 'HP', 'type' => 'tel', 'is_required' => true, 'sort_order' => 3],
            ['key' => 'previous_school', 'label' => 'Asal', 'type' => 'text', 'is_required' => true, 'sort_order' => 4],
            ['key' => 'ijazah', 'label' => 'Ijazah', 'type' => 'file', 'is_required' => true, 'sort_order' => 5],
        ]);

        $response = $this->post(route('ppdb.store', $this->institution), [
            'full_name' => 'Berkas Dua',
            'nik' => '3273010101080022',
            'phone' => '081234567890',
            'previous_school' => 'SD Negeri 4',
            'ijazah' => UploadedFile::fake()->create('data.txt', 100, 'text/plain'),
            'admission_path_id' => $this->path->id,
        ]);

        $response->assertSessionHasErrors(['ijazah']);
        $this->assertDatabaseMissing('spmb_registrations', ['nik' => '3273010101080022']);
    }

    public function test_berkas_download_is_guarded_and_streams_the_file(): void
    {
        Storage::fake('local');

        $this->institution->ppdbFields()->create([
            'key' => 'ijazah', 'label' => 'Ijazah', 'type' => 'file', 'is_required' => false, 'sort_order' => 1,
        ]);

        $path = "ppdb-berkas/{$this->institution->id}/ijazah.pdf";
        Storage::disk('local')->put($path, 'dummy-content');

        $registration = SpmbRegistration::factory()->create([
            'institution_id' => $this->institution->id,
            'data' => ['ijazah' => $path],
        ]);

        // Guests are bounced to login.
        $this->get(route('ppdb.berkas', [$registration, 'ijazah']))
            ->assertRedirect(route('login'));

        // Authorised panel users get the file streamed back.
        $admin = User::factory()->create();
        $admin->givePermissionTo(Permission::findOrCreate('View:SpmbRegistration', 'web'));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->actingAs($admin)
            ->get(route('ppdb.berkas', [$registration, 'ijazah']))
            ->assertOk();
    }

    public function test_per_jenjang_content_overrides_global_settings(): void
    {
        Setting::set('spmb_form_title', 'Judul Global');

        $this->institution->update([
            'form_title' => 'Judul Khusus SMP',
            'procedures' => [['icon' => '📝', 'title' => 'Langkah Khusus SMP', 'description' => 'Deskripsi langkah.']],
        ]);

        $response = $this->get(route('ppdb.show', $this->institution));

        $response->assertStatus(200);
        $response->assertSee('Judul Khusus SMP');
        $response->assertSee('Langkah Khusus SMP');
        $response->assertDontSee('Judul Global');
    }

    public function test_registration_number_is_generated_on_creation(): void
    {
        $registration = SpmbRegistration::factory()->create([
            'institution_id' => $this->institution->id,
            'academic_year_id' => $this->year->id,
        ]);

        $registration->refresh();

        $this->assertNotNull($registration->registration_number);
        $this->assertStringContainsString((string) $this->year->year_start, $registration->registration_number);
    }

    public function test_is_open_reflects_wave_dates_and_form_toggle(): void
    {
        $this->assertTrue(SpmbRegistration::isOpen($this->institution));

        Setting::set('spmb_form_enabled', '0');
        $this->assertFalse(SpmbRegistration::isOpen($this->institution));

        Setting::set('spmb_form_enabled', '1');
        $this->openWave->update(['is_active' => false]);
        $this->assertFalse(SpmbRegistration::isOpen($this->institution));
    }

    public function test_is_open_is_scoped_per_jenjang(): void
    {
        $otherJenjang = Institution::factory()->create(['slug' => 'sma']);

        $this->assertTrue(SpmbRegistration::isOpen($this->institution));
        $this->assertFalse(SpmbRegistration::isOpen($otherJenjang));
    }

    public function test_only_one_academic_year_stays_active(): void
    {
        $newest = AcademicYear::factory()->active()->create();

        $this->assertTrue($newest->fresh()->is_active);
        $this->assertFalse($this->year->fresh()->is_active);
        $this->assertSame($newest->id, AcademicYear::active()?->id);
    }

    public function test_current_open_only_considers_active_year(): void
    {
        $this->year->update(['is_active' => false]);

        $this->assertNull(RegistrationWave::currentOpen($this->institution));
    }
}
