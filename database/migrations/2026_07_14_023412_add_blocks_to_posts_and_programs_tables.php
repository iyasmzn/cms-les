<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->json('blocks')->nullable()->after('content');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->json('blocks')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('blocks');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('blocks');
        });
    }
};
