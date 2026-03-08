<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Support\Localization\Localizer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => Localizer::string($this->name_i18n, $this->name),
            'slug' => $this->slug,
            'bio' => Localizer::string($this->bio_i18n, $this->bio),
            'avatar' => $this->avatar,
            'experience_years' => $this->experience_years,
            'specialties' => Localizer::array($this->specialties_i18n, $this->specialties ?? []),
            'languages' => $this->languages ?? [],
            'certificates' => Localizer::array($this->certificates_i18n, $this->certificates ?? []),
            'instagram' => $this->instagram,
            'is_active' => $this->is_active,
            'sort' => $this->sort,
            'services' => $this->whenLoaded('services', function (): array {
                return $this->services->map(fn ($service): array => [
                    'id' => $service->id,
                    'category_id' => $service->category_id,
                    'category_name' => $service->category
                        ? Localizer::string($service->category->name_i18n, $service->category->name)
                        : null,
                    'name' => Localizer::string($service->name_i18n, $service->name),
                    'duration_minutes' => $service->pivot?->duration_minutes ?? $service->duration_minutes,
                    'price' => $service->pivot?->price ?? $service->price_from,
                ])->values()->all();
            }),
        ];
    }
}
