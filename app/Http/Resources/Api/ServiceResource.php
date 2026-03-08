<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Support\Localization\Localizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => Localizer::string($this->name_i18n, $this->name),
            'slug' => $this->slug,
            'description' => Localizer::string($this->description_i18n, $this->description),
            'duration_minutes' => $this->duration_minutes,
            'price_from' => $this->price_from,
            'price_to' => $this->price_to,
            'is_active' => $this->is_active,
        ];
    }
}
