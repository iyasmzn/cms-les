<?php

namespace App\Models;

use Database\Factories\DonationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    /** @use HasFactory<DonationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'is_anonymous',
        'amount',
        'payment_method',
        'message',
        'status',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'amount' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    // ── Static options ────────────────────────────────────────────

    /** @return array<string, string> */
    public static function paymentMethodOptions(): array
    {
        return [
            'transfer_bank' => 'Transfer Bank',
            'qris' => 'QRIS',
            'tunai' => 'Tunai',
            'lainnya' => 'Lainnya',
        ];
    }

    /** @return array<string, string> */
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'rejected' => 'Ditolak',
        ];
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    // ── Accessors ─────────────────────────────────────────────────

    public function getDisplayNameAttribute(): string
    {
        return $this->is_anonymous ? 'Hamba Allah' : $this->name;
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp '.number_format($this->amount, 0, ',', '.');
    }
}
