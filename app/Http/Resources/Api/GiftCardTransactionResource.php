<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'amount' => (float) $this->amount,
            'balance_after' => (float) $this->balance_after,
            'performed_by_user_id' => $this->performed_by_user_id,
            'booking_order_id' => $this->booking_order_id,
            'appointment_id' => $this->appointment_id,
            'meta' => $this->meta,
            'created_at' => $this->created_at,
        ];
    }
}
