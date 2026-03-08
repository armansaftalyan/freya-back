<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'status' => $this->status,
            'source' => $this->source,
            'comment' => $this->comment,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'total_price' => (float) ($this->total_price ?? 0),
            'appointments_count' => $this->whenCounted('appointments'),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'client' => new UserResource($this->whenLoaded('client')),
            'created_at' => $this->created_at,
        ];
    }
}
