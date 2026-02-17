<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\UserResource\Pages;

use App\Admin\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
