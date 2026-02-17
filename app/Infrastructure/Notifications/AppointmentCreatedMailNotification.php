<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Salon\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreatedMailNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('New appointment created')
            ->line('Your appointment has been created.')
            ->line('Service: '.$this->appointment->service->name)
            ->line('Start: '.$this->appointment->start_at?->toDateTimeString())
            ->line('Status: '.$this->appointment->status->value);
    }
}
