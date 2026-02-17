<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\ServiceResource\Pages;

use App\Admin\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;
}
