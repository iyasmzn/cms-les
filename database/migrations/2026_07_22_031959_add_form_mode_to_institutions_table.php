<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How each jenjang collects registrations:
     * - internal: dynamic form stored in this system (default)
     * - external_link: a button to an external site (no data stored here)
     * - embed: an embedded (iframe) form from another site (no data stored here)
     */
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('form_mode')->default('internal')->after('is_active');
            $table->string('external_url')->nullable()->after('form_mode');
            $table->string('embed_url')->nullable()->after('external_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['form_mode', 'external_url', 'embed_url']);
        });
    }
};
