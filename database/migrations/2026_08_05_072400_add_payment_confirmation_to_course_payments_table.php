<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets a member declare how they settled a bill and attach proof, which an
     * admin then verifies or rejects.
     */
    public function up(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->foreignId('payment_account_id')->nullable()->after('method')->constrained()->nullOnDelete();
            $table->string('proof_path')->nullable()->after('payment_account_id');
            $table->text('payer_note')->nullable()->after('proof_path');
            $table->timestamp('submitted_at')->nullable()->after('paid_at');
            $table->timestamp('rejected_at')->nullable()->after('submitted_at');
            $table->string('rejection_reason')->nullable()->after('rejected_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_account_id');
            $table->dropColumn([
                'proof_path',
                'payer_note',
                'submitted_at',
                'rejected_at',
                'rejection_reason',
            ]);
        });
    }
};
