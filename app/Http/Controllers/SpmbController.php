<?php

namespace App\Http\Controllers;

use App\Http\Concerns\ProtectsAgainstSpam;
use App\Models\AcademicYear;
use App\Models\AdmissionPath;
use App\Models\Institution;
use App\Models\PpdbField;
use App\Models\RegistrationWave;
use App\Models\Setting;
use App\Models\SpmbRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpmbController extends Controller
{
    use ProtectsAgainstSpam;

    /**
     * Jenjang selector. Redirects straight to the single jenjang when only one
     * is active, so single-unit schools keep a one-click PPDB flow.
     */
    public function index(): View|RedirectResponse
    {
        $institutions = Institution::query()->active()->ordered()->get();

        if ($institutions->count() === 1) {
            return redirect()->route('ppdb.show', $institutions->first());
        }

        $siteName = setting('site_name', config('app.name'));
        $yearLabel = spmb_year_label();

        $seo = [
            'title' => "PPDB / SPMB {$yearLabel} | {$siteName}",
            'description' => "Informasi Penerimaan Peserta Didik Baru (PPDB) {$siteName}. Pilih jenjang pendidikan untuk melihat prosedur, jadwal, dan formulir pendaftaran.",
            'canonical' => route('ppdb.index'),
        ];

        return view('ppdb.index', compact('institutions', 'seo'));
    }

    /**
     * PPDB page for a single jenjang: procedures, fees, schedule and the
     * registration form scoped to this institution.
     */
    public function show(Institution $institution): View
    {
        $procedures = $institution->resolvedProcedures() ?: $this->defaultProcedures();
        $fees = $institution->resolvedFees();
        $formTitle = $institution->resolvedFormTitle();
        $formDesc = $institution->resolvedFormDescription();
        $closedMessage = $institution->resolvedClosedMessage();
        $paths = AdmissionPath::query()->forInstitution($institution)->active()->ordered()->get();
        $fields = $institution->usesInternalForm()
            ? $institution->ppdbFields()->active()->ordered()->get()
            : collect();
        $activeYear = AcademicYear::active();
        $waves = $activeYear
            ? $institution->waves()
                ->where('academic_year_id', $activeYear->id)
                ->where('is_active', true)
                ->orderBy('start_date')
                ->get()
            : collect();
        $scheduleWave = RegistrationWave::relevant($institution);
        $spmbOpen = match ($institution->form_mode) {
            Institution::FORM_MODE_EXTERNAL_LINK => filled($institution->external_url),
            Institution::FORM_MODE_EMBED => filled($institution->embed_url),
            default => RegistrationWave::currentOpen($institution) !== null,
        };
        $siteName = setting('site_name', config('app.name'));
        $yearLabel = spmb_year_label();

        $seo = [
            'title' => "PPDB {$institution->name} {$yearLabel} | {$siteName}",
            'description' => "Penerimaan Peserta Didik Baru (PPDB) {$institution->name} {$siteName}. Prosedur pendaftaran, biaya, jadwal gelombang, dan formulir online.",
            'canonical' => route('ppdb.show', $institution),
        ];

        return view('ppdb.show', compact('institution', 'procedures', 'fees', 'formTitle', 'formDesc', 'closedMessage', 'paths', 'fields', 'waves', 'scheduleWave', 'spmbOpen', 'seo'));
    }

    public function store(Request $request, Institution $institution): RedirectResponse
    {
        // External-link and embed jenjang collect data elsewhere; never store here.
        if (! $institution->usesInternalForm()) {
            return redirect()->route('ppdb.show', $institution);
        }

        $request->validate($this->spamProtectionRules($request));

        if (! (bool) Setting::get('spmb_form_enabled', true)) {
            return back()->with('error', Setting::get('spmb_closed_message', 'Form pendaftaran saat ini sedang ditutup.'));
        }

        $wave = RegistrationWave::currentOpen($institution);

        if ($wave === null) {
            return back()->with('error', "SPMB {$institution->name} saat ini tidak dalam masa penerimaan.");
        }

        $fields = $institution->ppdbFields()->active()->ordered()->get();
        $hasPaths = AdmissionPath::query()->forInstitution($institution)->active()->exists();

        $payload = $fields->isEmpty()
            ? $this->validateLegacyRegistration($request)
            : $this->validateDynamicRegistration($request, $institution, $fields, $hasPaths);

        $payload['institution_id'] = $institution->id;
        $payload['academic_year_id'] = $wave->academic_year_id;
        $payload['registration_wave_id'] = $wave->id;

        SpmbRegistration::create($payload);

        return redirect()->route('ppdb.show', $institution)
            ->with('success', 'Pendaftaran berhasil dikirim! Kami akan segera menghubungi Anda untuk proses verifikasi.');
    }

    /**
     * Stream a registration's uploaded file to authorised panel users only.
     * Files live on the private disk, so this route is the only way to fetch them.
     */
    public function downloadBerkas(Request $request, SpmbRegistration $registration, string $field): StreamedResponse
    {
        abort_unless($request->user()?->can('View:SpmbRegistration'), 403);

        $isFileField = $registration->institution?->ppdbFields()
            ->where('type', 'file')
            ->where('key', $field)
            ->exists();

        abort_unless($isFileField, 404);

        $path = data_get($registration->data, $field);

        abort_if(blank($path) || ! Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    /**
     * Validate against the admin-defined dynamic fields, routing known keys to
     * their columns and everything else into the `data` JSON bucket.
     *
     * @param  Collection<int, PpdbField>  $fields
     * @return array<string, mixed>
     */
    private function validateDynamicRegistration(Request $request, Institution $institution, Collection $fields, bool $requirePath): array
    {
        $rules = [];
        $attributes = [];

        foreach ($fields as $field) {
            $rules[$field->key] = $this->rulesForField($field);
            $attributes[$field->key] = $field->label;
        }

        if ($requirePath) {
            $rules['admission_path_id'] = ['required', Rule::exists('admission_paths', 'id')->where('is_active', true)];
        }

        $validated = $request->validate($rules, [
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Setiap calon peserta hanya dapat mendaftar satu kali.',
        ], $attributes);

        $knownKeys = SpmbRegistration::dynamicColumnKeys();
        $columns = [];
        $data = [];

        foreach ($fields as $field) {
            if ($field->type === 'file') {
                $data[$field->key] = $request->hasFile($field->key)
                    ? $request->file($field->key)->store("ppdb-berkas/{$institution->id}", 'local')
                    : null;

                continue;
            }

            $value = $validated[$field->key] ?? null;

            if (in_array($field->key, $knownKeys, true)) {
                $columns[$field->key] = $value;
            } else {
                $data[$field->key] = $value;
            }
        }

        if ($requirePath) {
            $columns['admission_path_id'] = $validated['admission_path_id'] ?? null;
        }

        $columns['data'] = $data === [] ? null : $data;

        return $columns;
    }

    /**
     * Build validation rules for a single dynamic field based on its type.
     *
     * @return array<int, mixed>
     */
    private function rulesForField(PpdbField $field): array
    {
        $presence = $field->is_required ? 'required' : 'nullable';

        // NIK keeps its dedicated 16-digit + uniqueness guard regardless of type.
        if ($field->key === 'nik') {
            return [$presence, 'digits:16', Rule::unique('spmb_registrations', 'nik')];
        }

        if ($field->type === 'file') {
            return [$presence, 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'];
        }

        $rules = [$presence];

        $rules[] = match ($field->type) {
            'email' => 'email',
            'number' => 'numeric',
            'date' => 'date',
            'select', 'radio' => Rule::in($field->optionValues()),
            default => 'string',
        };

        if (in_array($field->type, ['text', 'textarea', 'tel', 'email'], true)) {
            $rules[] = $field->type === 'textarea' ? 'max:2000' : 'max:255';
        }

        return $rules;
    }

    /**
     * The classic fixed-field validation, used as a fallback for internal-form
     * jenjang that have no dynamic fields configured.
     *
     * @return array<string, mixed>
     */
    private function validateLegacyRegistration(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'nik' => ['required', 'digits:16', Rule::unique('spmb_registrations', 'nik')],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'previous_school' => ['required', 'string', 'max:100'],
            'previous_school_city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'admission_path_id' => ['required', Rule::exists('admission_paths', 'id')->where('is_active', true)],
            'parent_name' => ['nullable', 'string', 'max:100'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar. Setiap calon peserta hanya dapat mendaftar satu kali.',
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function defaultProcedures(): array
    {
        return [
            ['icon' => '📝', 'title' => 'Isi Formulir Online', 'description' => 'Kunjungi halaman PPDB dan isi formulir pendaftaran secara lengkap dan benar.'],
            ['icon' => '📁', 'title' => 'Siapkan Berkas', 'description' => 'Persiapkan dokumen yang diperlukan: ijazah/SHUN, rapor, dan pas foto terbaru.'],
            ['icon' => '✅', 'title' => 'Verifikasi Berkas', 'description' => 'Datang ke sekolah untuk verifikasi berkas pada tanggal yang telah ditentukan.'],
            ['icon' => '🎉', 'title' => 'Pengumuman Hasil', 'description' => 'Hasil seleksi diumumkan melalui halaman resmi sekolah dan via WhatsApp/email.'],
        ];
    }
}
