<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-jenjang PPDB content (falls back to the global Setting values when
     * null) plus an auto-generated registration number on each submission.
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->json('procedures')->nullable()->after('embed_url');
            $table->json('fees')->nullable()->after('procedures');
            $table->string('form_title')->nullable()->after('fees');
            $table->text('form_description')->nullable()->after('form_title');
            $table->text('closed_message')->nullable()->after('form_description');
        });

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->string('registration_number')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['procedures', 'fees', 'form_title', 'form_description', 'closed_message']);
        });

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->dropColumn('registration_number');
        });
    }
};
