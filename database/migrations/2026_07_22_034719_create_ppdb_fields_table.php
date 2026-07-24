<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-jenjang dynamic form field definitions plus the JSON bucket on
     * registrations that stores values for custom fields (fields whose key does
     * not map to a dedicated spmb_registrations column).
     */
    public function up(): void
    {
        Schema::create('ppdb_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('type')->default('text');
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('help_text')->nullable();
            $table->boolean('is_required')->default(false);
            $table->string('width')->default('full');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institution_id', 'key']);
        });

        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->json('data')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spmb_registrations', function (Blueprint $table) {
            $table->dropColumn('data');
        });

        Schema::dropIfExists('ppdb_fields');
    }
};
