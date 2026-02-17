<?php

declare(strict_types=1);

namespace App\Domain\Salon\Enums;

enum AppointmentSource: string
{
    case Site = 'site';
    case Phone = 'phone';
    case Instagram = 'instagram';
}
