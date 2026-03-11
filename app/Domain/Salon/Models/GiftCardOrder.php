<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use App\Domain\Salon\Enums\GiftCardOrderStatus;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GiftCardOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_user_id',
        'recipient_name',
        'recipient_email',
        'recipient_phone',
        'amount',
        'currency',
        'payment_provider',
        'provider_payment_id',
        'status',
        'paid_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => GiftCardOrderStatus::class,
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function giftCard(): HasOne
    {
        return $this->hasOne(GiftCard::class);
    }
}
