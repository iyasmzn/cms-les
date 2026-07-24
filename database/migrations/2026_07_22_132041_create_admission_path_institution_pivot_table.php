<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move admission-path scoping from a single `institution_id` column to a
     * many-to-many pivot, so a jalur can be shared by several jenjang. No pivot
     * rows for a path means it is shared with every jenjang.
     */
    public function up(): void
    {
        Schema::create('admission_path_institution', function (Blueprint $table) {
            $table->foreignId('admission_path_id')->constrained()->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->primary(['admission_path_id', 'institution_id']);
        });

        if (Schema::hasColumn('admission_paths', 'institution_id')) {
            DB::table('admission_paths')
                ->whereNotNull('institution_id')
                ->get(['id', 'institution_id'])
                ->each(function (object $path): void {
                    DB::table('admission_path_institution')->insertOrIgnore([
                        'admission_path_id' => $path->id,
                        'institution_id' => $path->institution_id,
                    ]);
                });

            Schema::table('admission_paths', function (Blueprint $table) {
                $table->dropConstrainedForeignId('institution_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_paths', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Best effort: fold the first attached jenjang back into the column.
        DB::table('admission_path_institution')
            ->orderBy('institution_id')
            ->get()
            ->groupBy('admission_path_id')
            ->each(function ($rows, int $pathId): void {
                DB::table('admission_paths')->where('id', $pathId)->update([
                    'institution_id' => $rows->first()->institution_id,
                ]);
            });

        Schema::dropIfExists('admission_path_institution');
    }
};
