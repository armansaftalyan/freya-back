<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Support\Localization\Localizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_order_id' => $this->booking_order_id,
            'status' => $this->status->value,
            'source' => $this->source,
            'comment' => $this->comment,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'service' => new ServiceResource($this->whenLoaded('service')),
            'services' => $this->whenLoaded('services', function (): array {
                return $this->services->map(fn ($service): array => [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'name' => Localizer::string($service->name_i18n, $service->name),
                    'duration_minutes' => (int) ($service->pivot?->duration_minutes ?? $service->duration_minutes),
                    'price' => $service->pivot?->price ?? $service->price_from,
                    'sort_order' => (int) ($service->pivot?->sort_order ?? 0),
                ])->values()->all();
            }),
            'master' => new MasterResource($this->whenLoaded('master')),
            'client' => new UserResource($this->whenLoaded('client')),
            'created_at' => $this->created_at,
        ];
    }
}
