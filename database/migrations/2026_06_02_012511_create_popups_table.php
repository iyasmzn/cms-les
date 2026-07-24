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
        Schema::create('popups', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('button_label', 100)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->smallInteger('delay_seconds')->unsigned()->default(0);
            $table->smallInteger('show_every_days')->unsigned()->default(0);
            $table->string('width', 10)->default('md');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->smallInteger('sort_order')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('popups');
    }
};
