<?php

namespace Database\Seeders;

use App\Models\Institution;
use Illuminate\Database\Seeder;

class PpdbFieldSeeder extends Seeder
{
    /**
     * Give every internal-form jenjang that has no fields yet the standard set
     * of registration fields, so the dynamic form matches the classic form out
     * of the box. Admins can then tailor each jenjang's fields.
     */
    public function run(): void
    {
        $defaults = $this->defaultFields();

        Institution::query()
            ->where('form_mode', Institution::FORM_MODE_INTERNAL)
            ->get()
            ->each(function (Institution $institution) use ($defaults): void {
                if ($institution->ppdbFields()->exists()) {
                    return;
                }

                foreach ($defaults as $index => $field) {
                    $institution->ppdbFields()->create(array_merge($field, [
                        'sort_order' => $index + 1,
                        'is_active' => true,
                    ]));
                }
            });
    }

    /**
     * The standard registration fields. Keys that match spmb_registrations
     * columns are stored there; the rest would go to the `data` JSON bucket.
     *
     * @return array<int, array<string, mixed>>
     */
    private function defaultFields(): array
    {
        return [
            ['key' => 'full_name', 'label' => 'Nama Lengkap', 'type' => 'text', 'is_required' => true, 'width' => 'full', 'placeholder' => 'Sesuai akta kelahiran'],
            ['key' => 'nik', 'label' => 'NIK', 'type' => 'text', 'is_required' => true, 'width' => 'full', 'placeholder' => '16 digit Nomor Induk Kependudukan', 'help_text' => 'Nomor Induk Kependudukan, 16 digit angka.'],
            ['key' => 'phone', 'label' => 'No. HP / WhatsApp', 'type' => 'tel', 'is_required' => true, 'width' => 'half', 'placeholder' => '08xxxxxxxxxx'],
            ['key' => 'email', 'label' => 'Email', 'type' => 'email', 'is_required' => false, 'width' => 'half', 'placeholder' => 'nama@email.com'],
            ['key' => 'birth_place', 'label' => 'Tempat Lahir', 'type' => 'text', 'is_required' => false, 'width' => 'half', 'placeholder' => 'Kota kelahiran'],
            ['key' => 'birth_date', 'label' => 'Tanggal Lahir', 'type' => 'date', 'is_required' => false, 'width' => 'half'],
            ['key' => 'address', 'label' => 'Alamat Lengkap', 'type' => 'textarea', 'is_required' => false, 'width' => 'full', 'placeholder' => 'Jl. ..., RT/RW ..., Kel./Desa ..., Kec. ..., Kab./Kota ...'],
            ['key' => 'previous_school', 'label' => 'Nama Sekolah Asal', 'type' => 'text', 'is_required' => true, 'width' => 'half', 'placeholder' => 'SD / SMP Negeri / Swasta ...'],
            ['key' => 'previous_school_city', 'label' => 'Kota / Kabupaten Asal', 'type' => 'text', 'is_required' => false, 'width' => 'half', 'placeholder' => 'Kota ...'],
            ['key' => 'parent_name', 'label' => 'Nama Orang Tua / Wali', 'type' => 'text', 'is_required' => false, 'width' => 'half', 'placeholder' => 'Nama lengkap orang tua / wali'],
            ['key' => 'parent_phone', 'label' => 'No. HP Orang Tua / Wali', 'type' => 'tel', 'is_required' => false, 'width' => 'half', 'placeholder' => '08xxxxxxxxxx'],
            ['key' => 'notes', 'label' => 'Catatan Tambahan', 'type' => 'textarea', 'is_required' => false, 'width' => 'full', 'placeholder' => 'Informasi tambahan yang perlu panitia ketahui (opsional)...'],
        ];
    }
}
