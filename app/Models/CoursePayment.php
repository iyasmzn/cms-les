<?php

namespace App\Models;

use Database\Factories\CoursePaymentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
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

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'unpaid' => 'Unpaid',
            'paid' => 'Paid',
            'waived' => 'Waived',
        ];
    }

    /** @return array<string, string> */
    public static function methodOptions(): array
    {
        return [
            'cash' => 'Cash',
            'transfer' => 'Bank Transfer',
            'other' => 'Other',
        ];
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
        ]);
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
}
