<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Salon\Models\Branch;
use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\Service;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BranchResource;
use App\Http\Resources\Api\CategoryResource;
use App\Http\Resources\Api\MasterResource;
use App\Http\Resources\Api\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        return MasterResource::collection(
            Master::query()
                ->where('is_active', true)
                ->when($request->integer('service_id'), function ($query, $serviceId): void {
                    $query->whereHas('services', fn ($q) => $q->where('services.id', $serviceId));
                })
                ->orderBy('sort')
                ->get()
        );
    }

    public function branches(): AnonymousResourceCollection
    {
        return BranchResource::collection(
            Branch::query()->where('is_active', true)->orderBy('name')->get()
        );
    }
}
