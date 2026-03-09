<?php

declare(strict_types=1);

namespace App\Support\Cache;

use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use Illuminate\Support\Facades\Cache;

class CatalogCache
{
    private const VERSION_KEY = 'catalog:version';

    public function rememberCategories(): mixed
    {
        return Cache::remember(
            $this->cacheKey('categories'),
            now()->addMinutes(10),
            fn () => Category::query()->where('is_active', true)->orderBy('sort')->get()
        );
    }

    public function rememberServices(?int $categoryId): mixed
    {
        $segment = $categoryId !== null ? (string) $categoryId : 'all';

        return Cache::remember(
            $this->cacheKey("services:{$segment}"),
            now()->addMinutes(10),
            fn () => Service::query()
                ->where('is_active', true)
                ->when($categoryId, fn ($query, int $id) => $query->where('category_id', $id))
                ->orderBy('sort')
                ->get()
        );
    }

    /**
     * @param array<int, int> $serviceIds
     */
    public function rememberMasters(array $serviceIds): mixed
    {
        $normalizedServiceIds = array_values(array_unique(array_filter($serviceIds, fn (int $id): bool => $id > 0)));
        sort($normalizedServiceIds);

        $segment = $normalizedServiceIds === [] ? 'all' : implode('-', $normalizedServiceIds);

        return Cache::remember(
            $this->cacheKey("masters:{$segment}"),
            now()->addMinutes(5),
            function () use ($normalizedServiceIds) {
                return Master::query()
                    ->where('is_active', true)
                    ->when($normalizedServiceIds !== [], function ($query) use ($normalizedServiceIds): void {
                        foreach ($normalizedServiceIds as $serviceId) {
                            $query->whereHas('services', fn ($serviceQuery) => $serviceQuery->where('services.id', $serviceId));
                        }
                    })
                    ->orderBy('sort')
                    ->get();
            }
        );
    }

    public function invalidateCatalog(): void
    {
        $current = (int) Cache::get(self::VERSION_KEY, 1);
        $next = max(2, $current + 1);
        Cache::forever(self::VERSION_KEY, $next);
    }

    private function cacheKey(string $suffix): string
    {
        return 'catalog:v'.$this->catalogVersion().':'.$suffix;
    }

    private function catalogVersion(): int
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        $version = max(1, $version);
        Cache::forever(self::VERSION_KEY, $version);

        return $version;
    }
}
