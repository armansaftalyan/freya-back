<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\BookingOrderResource\Pages;

use App\Admin\Filament\Resources\BookingOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListBookingOrders extends ListRecords
{
    protected static string $resource = BookingOrderResource::class;
}
