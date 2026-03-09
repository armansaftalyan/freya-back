<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\MasterResource;
use App\Http\Resources\Api\ServiceResource;
use App\Support\Cache\CatalogCache;
use App\Support\Salon\ServiceIds;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class CatalogController extends Controller
{
    public function __construct(private readonly CatalogCache $catalogCache)
    {
    }

    public function categories(): AnonymousResourceCollection
    {
        return CategoryResource::collection(
            $this->catalogCache->rememberCategories()
        );
    }

    public function services(Request $request): AnonymousResourceCollection
    {
        $categoryId = $request->integer('category_id');

        return ServiceResource::collection(
            $this->catalogCache->rememberServices($categoryId > 0 ? $categoryId : null)
        );
    }

    public function masters(Request $request): AnonymousResourceCollection
    {
        $serviceIds = ServiceIds::fromPayload($request->all(), 'service_ids', 'service_id');

        return MasterResource::collection(
            $this->catalogCache->rememberMasters($serviceIds->all())
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
