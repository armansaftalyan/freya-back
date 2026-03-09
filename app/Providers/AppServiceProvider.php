<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Salon\Models\Category;
use App\Domain\Salon\Models\Master;
use App\Domain\Salon\Models\MasterService;
use App\Domain\Salon\Models\Service;
use App\Observers\AppointmentObserver;
use App\Observers\CatalogCacheObserver;
use App\Policies\AppointmentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Appointment::observe(AppointmentObserver::class);
        Category::observe(CatalogCacheObserver::class);
        Service::observe(CatalogCacheObserver::class);
        Master::observe(CatalogCacheObserver::class);
        MasterService::observe(CatalogCacheObserver::class);
        Gate::define('view-admin', fn ($user): bool => $user->hasAnyRole(['admin', 'manager', 'master']));
    }
}
