<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'qr_token' => $this->qr_token,
            'owner_user_id' => $this->owner_user_id,
            'gift_card_order_id' => $this->gift_card_order_id,
            'initial_amount' => (float) $this->initial_amount,
            'balance' => (float) $this->balance,
            'currency' => $this->currency,
            'status' => $this->status->value,
            'expires_at' => $this->expires_at,
            'activated_at' => $this->activated_at,
            'last_used_at' => $this->last_used_at,
            'meta' => $this->meta,
            'transactions' => GiftCardTransactionResource::collection($this->whenLoaded('transactions')),
            'created_at' => $this->created_at,
        ];
    }
}
