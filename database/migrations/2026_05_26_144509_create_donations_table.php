<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // Donatur
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Nominal & Metode
            $table->unsignedBigInteger('amount');
            $table->string('payment_method', 50);

            // Pesan
            $table->text('message')->nullable();

            // Status konfirmasi
            $table->string('status', 20)->default('pending');
            $table->timestamp('confirmed_at')->nullable();

            // Catatan admin (internal)
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
