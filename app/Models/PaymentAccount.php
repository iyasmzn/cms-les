<?php

namespace App\Models;

use Database\Factories\PaymentAccountFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PaymentAccount extends Model
{
    /** @use HasFactory<PaymentAccountFactory> */
    use HasFactory;

    protected $fillable = [
        'type',
        'label',
        'bank_name',
        'account_number',
        'account_holder',
        'qris_image',
        'instructions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Destination kinds a member can be shown. Cash needs no account record —
     * it is handled by the payment channel list, not here.
     *
     * @return array<string, string>
     */
    public static function typeOptions(): array
    {
        return [
            'bank' => 'Bank Transfer',
            'qris' => 'QRIS',
        ];
    }

    /**
     * A display name for this destination, falling back to the bank name or
     * the type when no explicit label was set.
     */
    public function displayName(): string
    {
        if (filled($this->label)) {
            return $this->label;
        }

        if ($this->type === 'bank') {
            return $this->bank_name ?: 'Bank Transfer';
        }

        return self::typeOptions()[$this->type] ?? $this->type;
    }

    /**
     * Public URL of the QRIS image, or null when none is uploaded.
     */
    public function qrisUrl(): ?string
    {
        return filled($this->qris_image)
            ? Storage::disk('public')->url($this->qris_image)
            : null;
    }

    /**
     * Whether this destination is complete enough to show to a member.
     */
    public function isPresentable(): bool
    {
        return match ($this->type) {
            'bank' => filled($this->account_number),
            'qris' => filled($this->qris_image),
            default => false,
        };
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
