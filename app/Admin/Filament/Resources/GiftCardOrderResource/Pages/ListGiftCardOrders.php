<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\GiftCardOrderResource\Pages;

use App\Admin\Filament\Resources\GiftCardOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListGiftCardOrders extends ListRecords
{
    protected static string $resource = GiftCardOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
