<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Questions now require a logged-in user. Store who asked, and whether they
     * chose to have their name hidden (shown as "Anonim") on public pages.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->after('post_id')
                ->constrained('users')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false)->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('is_anonymous');
        });
    }
};
