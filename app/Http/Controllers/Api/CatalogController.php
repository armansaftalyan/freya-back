<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\MasterResource;
use App\Http\Resources\Api\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogController extends Controller
{
    public function categories(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::query()->where('is_active', true)->orderBy('sort')->get()
        );
    }

    public function services(Request $request): AnonymousResourceCollection
    {
        return ServiceResource::collection(
            Service::query()
                ->where('is_active', true)
                ->when($request->integer('category_id'), fn ($q, $categoryId) => $q->where('category_id', $categoryId))
                ->orderBy('sort')
                ->get()
        );
    }

    public function masters(Request $request): AnonymousResourceCollection
    {
        $serviceIds = collect((array) $request->input('service_ids', []))
            ->map(fn ($value) => (int) $value)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        if ($serviceIds->isEmpty() && $request->integer('service_id')) {
            $serviceIds = collect([$request->integer('service_id')]);
        }

        return MasterResource::collection(
            Master::query()
                ->where('is_active', true)
                ->when($serviceIds->isNotEmpty(), function ($query) use ($serviceIds): void {
                    foreach ($serviceIds as $serviceId) {
                        $query->whereHas('services', fn ($q) => $q->where('services.id', $serviceId));
                    }
                })
                ->orderBy('sort')
                ->get()
        );
    }

    public function master(string $masterKey): JsonResource
    {
        $master = Master::query()
            ->where('is_active', true)
            ->with(['services.category'])
            ->where(function ($query) use ($masterKey): void {
                $query->where('slug', $masterKey);

                if (ctype_digit($masterKey)) {
                    $query->orWhere('id', (int) $masterKey);
                }
            })
            ->firstOrFail();

        return new MasterResource($master);
    }
}
