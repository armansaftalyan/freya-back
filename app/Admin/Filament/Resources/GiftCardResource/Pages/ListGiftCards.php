<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\GiftCardResource\Pages;

use App\Admin\Filament\Resources\GiftCardResource;
use Filament\Resources\Pages\ListRecords;

class ListGiftCards extends ListRecords
{
    protected static string $resource = GiftCardResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
