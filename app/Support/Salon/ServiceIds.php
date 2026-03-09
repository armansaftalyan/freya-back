<?php

declare(strict_types=1);

namespace App\Support\Salon;

use Illuminate\Support\Collection;

class ServiceIds
{
    /**
     * @param array<int, mixed> $rawValues
     * @return Collection<int, int>
     */
    public static function fromArray(array $rawValues): Collection
    {
        return collect($rawValues)
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, int>
     */
    public static function fromPayload(array $payload, string $listKey = 'service_ids', ?string $singleKey = 'service_id'): Collection
    {
        $serviceIds = self::fromArray((array) ($payload[$listKey] ?? []));

        if ($serviceIds->isEmpty() && $singleKey !== null && isset($payload[$singleKey])) {
            $serviceIds = self::fromArray([(int) $payload[$singleKey]]);
        }

        return $serviceIds;
    }
}
