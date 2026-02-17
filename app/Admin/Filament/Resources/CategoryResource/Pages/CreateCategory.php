<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\CategoryResource\Pages;

use App\Admin\Filament\Resources\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
