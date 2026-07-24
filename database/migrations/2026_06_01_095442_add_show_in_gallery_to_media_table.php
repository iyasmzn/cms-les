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
        Schema::table('media', function (Blueprint $table): void {
            $table->boolean('show_in_gallery')->default(false)->after('description');
            $table->string('album', 100)->nullable()->after('show_in_gallery');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropColumn(['show_in_gallery', 'album']);
        });
    }
};
