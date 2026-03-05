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
            Stat::make(__('messages.filament.widgets.appointments_today'), (string) $appointmentsToday),
            Stat::make(__('messages.filament.widgets.appointments_week'), (string) $appointmentsWeek),
            Stat::make(__('messages.filament.widgets.new_clients_week'), (string) $newClientsWeek),
            Stat::make(__('messages.filament.widgets.top_service'), $topService ? $topService->name.' ('.$topService->total.')' : __('messages.filament.widgets.na')),
        ];
    }
}
