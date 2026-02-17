<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\MasterResource\Pages;

use App\Admin\Filament\Resources\MasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasters extends ListRecords
{
    protected static string $resource = MasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
