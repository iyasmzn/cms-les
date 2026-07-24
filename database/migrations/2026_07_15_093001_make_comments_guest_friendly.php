<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow guest comments: user_id becomes optional and a guest_name column
     * stores the name a visitor types when they are not logged in.
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_name', 150)->nullable()->after('user_id');

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropColumn('guest_name');
        });

        Schema::table('comments', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
