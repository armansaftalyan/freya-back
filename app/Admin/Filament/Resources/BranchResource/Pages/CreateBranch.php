<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\BranchResource\Pages;

use App\Admin\Filament\Resources\BranchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBranch extends CreateRecord
{
    protected static string $resource = BranchResource::class;
}
