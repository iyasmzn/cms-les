<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Scope the PPDB tables to institutions (jenjang: SD/SMP/SMA).
     *
     * `admission_paths.institution_id` stays nullable — a null jalur is shared
     * across every jenjang. Waves and registrations always belong to one
     * institution, so existing rows are backfilled to a default institution.
     */
    public function up(): void
    {
        Schema::table('admission_paths', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('registration_waves', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
        });

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('academic_year_id')->constrained()->nullOnDelete();
        });

        $this->backfillDefaultInstitution();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_paths', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });

        Schema::table('registration_waves', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }

    /**
     * Ensure legacy waves and registrations point at an institution. On an
     * existing install a default institution is created (named after the site)
     * and every orphaned row is attached to it. Fresh installs have no legacy
     * data at migrate time, so the InstitutionSeeder seeds SD/SMP/SMA cleanly.
     */
    private function backfillDefaultInstitution(): void
    {
        $hasLegacyData = DB::table('registration_waves')->exists()
            || DB::table('spmb_registrations')->exists();

        if ($hasLegacyData && DB::table('institutions')->doesntExist()) {
            $name = DB::table('settings')->where('key', 'site_name')->value('value') ?: 'Sekolah Kami';

            DB::table('institutions')->insert([
                'name' => $name,
                'slug' => 'sekolah',
                'color' => 'primary',
                'sort_order' => 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $defaultId = DB::table('institutions')->orderBy('id')->value('id');

        if ($defaultId === null) {
            return;
        }

        DB::table('registration_waves')->whereNull('institution_id')->update(['institution_id' => $defaultId]);
        DB::table('spmb_registrations')->whereNull('institution_id')->update(['institution_id' => $defaultId]);
    }
};
