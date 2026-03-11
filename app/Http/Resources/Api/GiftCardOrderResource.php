<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'payment_provider' => $this->payment_provider,
            'provider_payment_id' => $this->provider_payment_id,
            'status' => $this->status->value,
            'recipient_name' => $this->recipient_name,
            'recipient_email' => $this->recipient_email,
            'recipient_phone' => $this->recipient_phone,
            'paid_at' => $this->paid_at,
            'gift_card' => new GiftCardResource($this->whenLoaded('giftCard')),
            'created_at' => $this->created_at,
        ];
    }
}
