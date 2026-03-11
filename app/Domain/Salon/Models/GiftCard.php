<?php

declare(strict_types=1);

namespace App\Domain\Salon\Models;

use App\Domain\Salon\Enums\GiftCardStatus;
use App\Domain\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'qr_token',
        'owner_user_id',
        'gift_card_order_id',
        'initial_amount',
        'balance',
        'currency',
        'status',
        'expires_at',
        'activated_at',
        'last_used_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'status' => GiftCardStatus::class,
            'initial_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'last_used_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(GiftCardOrder::class, 'gift_card_order_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(GiftCardTransaction::class)->latest('id');
    }
}
