<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Salon\Models\Appointment;
use App\Domain\Users\Models\User;

class AppointmentPolicy
{
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['client', 'admin', 'manager']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->hasAnyRole(['admin', 'manager'])) {
            return true;
        }

        if ($user->hasRole('master')) {
            return $appointment->master?->user_id === $user->id;
        }

        return $appointment->client_id === $user->id;
    }

    public function cancel(User $user, Appointment $appointment): bool
    {
        return $appointment->client_id === $user->id || $user->hasAnyRole(['admin', 'manager']);
    }
}
