<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Salon\Models\Appointment;
use App\Observers\AppointmentObserver;
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
        Gate::define('view-admin', fn ($user): bool => $user->hasAnyRole(['admin', 'manager', 'master']));
    }
}
