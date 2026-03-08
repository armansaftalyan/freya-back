<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Support\Localization\Localizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => Localizer::string($this->name_i18n, $this->name),
            'slug' => $this->slug,
            'booking_group' => $this->booking_group ?: $this->slug,
            'sort' => $this->sort,
            'is_active' => $this->is_active,
        ];
    }
}
