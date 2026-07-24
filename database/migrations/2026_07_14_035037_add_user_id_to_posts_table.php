<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        // Backfill existing posts to the first super_admin so they remain
        // visible and manageable after ownership scoping is enforced.
        $ownerId = User::whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->value('id')
            ?? User::min('id');

        if ($ownerId) {
            DB::table('posts')->whereNull('user_id')->update(['user_id' => $ownerId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
