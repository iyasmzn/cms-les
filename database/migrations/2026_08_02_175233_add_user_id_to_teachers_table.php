<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a coach profile to a login account so instructors can manage the
     * groups they coach. Nullable — most teachers never get a panel account.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('institution_id')
                ->unique()
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
