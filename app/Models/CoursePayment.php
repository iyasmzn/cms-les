<?php

namespace App\Models;

use Database\Factories\CoursePaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CoursePayment extends Model
{
    /** @use HasFactory<CoursePaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'group_member_id',
        'group_session_id',
        'amount',
        'status',
        'method',
        'payment_account_id',
        'proof_path',
        'payer_note',
        'paid_at',
        'submitted_at',
        'rejected_at',
        'rejection_reason',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'submitted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /** @return BelongsTo<GroupMember, $this> */
    public function member(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'group_member_id');
    }

    /** @return BelongsTo<GroupSession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(GroupSession::class, 'group_session_id');
    }

    /**
     * The destination the member says they paid to. Null for cash.
     *
     * @return BelongsTo<PaymentAccount, $this>
     */
    public function paymentAccount(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'unpaid' => 'Unpaid',
            'review' => 'Awaiting Verification',
            'paid' => 'Paid',
            'waived' => 'Waived',
        ];
    }

    /**
     * Ways a bill can be settled. `other` stays for legacy records and manual
     * admin entries; members only ever pick one of the first three.
     *
     * @return array<string, string>
     */
    public static function methodOptions(): array
    {
        return [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'qris' => 'QRIS',
            'other' => 'Other',
        ];
    }

    /**
     * Methods a member may choose for themselves in the portal.
     *
     * @return array<string, string>
     */
    public static function memberMethodOptions(): array
    {
        return array_intersect_key(self::methodOptions(), array_flip(['cash', 'transfer', 'qris']));
    }

    /**
     * Record a member's claim that they have settled this bill, moving it into
     * the admin's verification queue. Clears any earlier rejection so a
     * corrected submission starts clean.
     */
    public function submitConfirmation(string $method, ?int $paymentAccountId = null, ?string $proofPath = null, ?string $payerNote = null): void
    {
        $this->update([
            'status' => 'review',
            'method' => $method,
            'payment_account_id' => $method === 'cash' ? null : $paymentAccountId,
            'proof_path' => $proofPath ?? $this->proof_path,
            'payer_note' => $payerNote,
            'submitted_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Mark this payment as settled, stamping the time and method.
     */
    public function markPaid(?string $method = null): void
    {
        $this->update([
            'status' => 'paid',
            'method' => $method ?? $this->method,
            'paid_at' => $this->paid_at ?? now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Send a submitted confirmation back to the member as unpaid, keeping the
     * reason so the portal can explain what to fix.
     */
    public function reject(?string $reason = null): void
    {
        $this->update([
            'status' => 'unpaid',
            'paid_at' => null,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Whether the member has submitted a confirmation that nobody has checked.
     */
    public function isAwaitingVerification(): bool
    {
        return $this->status === 'review';
    }

    /**
     * Whether the member may still submit (or resubmit) a confirmation.
     */
    public function isPayable(): bool
    {
        return $this->status === 'unpaid';
    }

    /**
     * Public URL of the uploaded proof, or null when none was attached.
     */
    public function proofUrl(): ?string
    {
        return filled($this->proof_path)
            ? Storage::disk('public')->url($this->proof_path)
            : null;
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->where('status', 'unpaid');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeAwaitingVerification(Builder $query): Builder
    {
        return $query->where('status', 'review');
    }
}
