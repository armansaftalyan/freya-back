<?php

declare(strict_types=1);

namespace App\Observers;

use App\Support\Cache\CatalogCache;

class CatalogCacheObserver
{
    public function saved(object $model): void
    {
        app(CatalogCache::class)->invalidateCatalog();
    }

    public function deleted(object $model): void
    {
        app(CatalogCache::class)->invalidateCatalog();
    }

    public function restored(object $model): void
    {
        app(CatalogCache::class)->invalidateCatalog();
    }
}
