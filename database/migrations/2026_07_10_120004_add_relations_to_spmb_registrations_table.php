<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Default jalur seeded inline so existing registrations can be backfilled
     * before the legacy `jalur` string column is dropped.
     *
     * @var array<int, array<string, mixed>>
     */
    private array $defaultPaths = [
        ['slug' => 'zonasi', 'name' => 'Zonasi', 'icon' => '🏡', 'color' => 'info', 'description' => 'Berdasarkan jarak domisili ke sekolah.', 'sort_order' => 1],
        ['slug' => 'prestasi', 'name' => 'Prestasi', 'icon' => '🏆', 'color' => 'success', 'description' => 'Berdasarkan nilai rapor atau prestasi akademik/non-akademik.', 'sort_order' => 2],
        ['slug' => 'afirmasi', 'name' => 'Afirmasi', 'icon' => '💚', 'color' => 'warning', 'description' => 'Untuk peserta didik dari keluarga tidak mampu.', 'sort_order' => 3],
        ['slug' => 'mutasi', 'name' => 'Mutasi', 'icon' => '🔄', 'color' => 'gray', 'description' => 'Untuk anak guru/tenaga kependidikan atau pindah tugas orang tua.', 'sort_order' => 4],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('registration_wave_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
            $table->foreignId('admission_path_id')->nullable()->after('address')->constrained()->nullOnDelete();
        });

        if (Schema::hasColumn('spmb_registrations', 'jalur')) {
            $this->backfillAdmissionPaths();
        }

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->dropColumn('jalur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->string('jalur')->default('zonasi')->after('address');
        });

        $paths = DB::table('admission_paths')->pluck('slug', 'id');

        foreach ($paths as $id => $slug) {
            DB::table('spmb_registrations')
                ->where('admission_path_id', $id)
                ->update(['jalur' => $slug]);
        }

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropConstrainedForeignId('registration_wave_id');
            $table->dropConstrainedForeignId('admission_path_id');
        });
    }

    /**
     * Ensure the default jalur exist, then map legacy jalur slugs to FKs.
     */
    private function backfillAdmissionPaths(): void
    {
        $now = now();

        foreach ($this->defaultPaths as $path) {
            DB::table('admission_paths')->updateOrInsert(
                ['slug' => $path['slug']],
                array_merge($path, ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]),
            );
        }

        $paths = DB::table('admission_paths')->pluck('id', 'slug');

        foreach ($paths as $slug => $id) {
            DB::table('spmb_registrations')
                ->where('jalur', $slug)
                ->update(['admission_path_id' => $id]);
        }
    }
};
