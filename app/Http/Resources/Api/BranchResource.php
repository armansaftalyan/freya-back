<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'geo_lat' => $this->geo_lat,
            'geo_lng' => $this->geo_lng,
            'working_hours' => $this->working_hours,
            'is_active' => $this->is_active,
        ];
    }
}
