<?php

declare(strict_types=1);

namespace App\Admin\Filament\Widgets;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Users\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SalonStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $startWeek = Carbon::now()->startOfWeek();
        $endWeek = Carbon::now()->endOfWeek();

        $appointmentsToday = Appointment::query()->whereDate('start_at', $today)->count();
        $appointmentsWeek = Appointment::query()->whereBetween('start_at', [$startWeek, $endWeek])->count();
        $newClientsWeek = User::role('client')->whereBetween('created_at', [$startWeek, $endWeek])->count();

        $topService = Appointment::query()
            ->select('services.name', DB::raw('count(*) as total'))
            ->join('services', 'services.id', '=', 'appointments.service_id')
            ->groupBy('services.name')
            ->orderByDesc('total')
            ->first();

        return [
            Stat::make('Appointments Today', (string) $appointmentsToday),
            Stat::make('Appointments This Week', (string) $appointmentsWeek),
            Stat::make('New Clients This Week', (string) $newClientsWeek),
            Stat::make('Top Service', $topService ? $topService->name.' ('.$topService->total.')' : 'N/A'),
        ];
    }
}
