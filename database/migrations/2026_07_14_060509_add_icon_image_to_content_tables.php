<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that carry an emoji `icon` column and now also accept an uploaded
     * icon image that takes precedence on the public site.
     *
     * @var list<string>
     */
    private array $tables = [
        'programs',
        'floating_buttons',
        'stats',
        'contact_items',
        'admission_paths',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->string('icon_image')->nullable()->after('icon');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table): void {
                $table->dropColumn('icon_image');
            });
        }
    }
};
