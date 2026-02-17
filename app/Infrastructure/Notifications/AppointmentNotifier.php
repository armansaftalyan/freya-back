<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Salon\Models\Appointment;
use App\Infrastructure\Integrations\Telegram\TelegramWebhookClient;
use Illuminate\Support\Facades\Log;

class AppointmentNotifier
{
    public function __construct(private readonly TelegramWebhookClient $telegramWebhookClient)
    {
    }

    public function notifyCreated(Appointment $appointment): void
    {
        $appointment->loadMissing(['client', 'master', 'service', 'branch']);

        $message = sprintf(
            'Appointment #%d created | client=%s | master=%s | service=%s | branch=%s | %s - %s | status=%s',
            $appointment->id,
            $appointment->client->email,
            $appointment->master->name,
            $appointment->service->name,
            $appointment->branch->name,
            (string) $appointment->start_at,
            (string) $appointment->end_at,
            $appointment->status->value
        );

        Log::channel(config('logging.default'))->info($message);

        $this->telegramWebhookClient->send($message);

        if ((bool) config('services.appointments.send_email_to_client', false)) {
            $appointment->client->notify(new AppointmentCreatedMailNotification($appointment));
        }
    }
}
