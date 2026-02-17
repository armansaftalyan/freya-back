<?php

declare(strict_types=1);

namespace App\Admin\Filament\Resources\AppointmentResource\Pages;

use App\Admin\Filament\Resources\AppointmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;
}
