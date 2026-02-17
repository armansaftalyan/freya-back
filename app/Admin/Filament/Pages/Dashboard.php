<?php

declare(strict_types=1);

namespace App\Admin\Filament\Pages;

use App\Admin\Filament\Widgets\SalonStatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            SalonStatsOverview::class,
        ];
    }
}
