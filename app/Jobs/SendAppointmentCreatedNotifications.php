<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domain\Salon\Models\Appointment;
use App\Infrastructure\Notifications\AppointmentNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentCreatedNotifications implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly int $appointmentId)
    {
    }

    public function handle(AppointmentNotifier $appointmentNotifier): void
    {
        $appointment = Appointment::query()->find($this->appointmentId);
        if ($appointment === null) {
            return;
        }

        $appointmentNotifier->deliverCreated($appointment);
    }
}
