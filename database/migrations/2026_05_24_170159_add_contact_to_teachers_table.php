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
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('education');
            $table->string('email', 150)->nullable()->after('phone');
            $table->string('whatsapp', 30)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['phone', 'email', 'whatsapp']);
        });
    }
};
