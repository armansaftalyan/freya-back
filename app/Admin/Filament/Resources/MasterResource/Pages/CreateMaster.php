<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\MasterResource\Pages;

use App\Admin\Filament\Resources\MasterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMaster extends CreateRecord
{
    protected static string $resource = MasterResource::class;
}
