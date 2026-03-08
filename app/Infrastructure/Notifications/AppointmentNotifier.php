<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifications;

use App\Domain\Salon\Models\Appointment;
use App\Infrastructure\Integrations\Telegram\TelegramWebhookClient;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AppointmentNotifier
{
    public function __construct(private readonly TelegramWebhookClient $telegramWebhookClient)
    {
    }

    public function notifyCreated(Appointment $appointment): void
    {
        $appointment->loadMissing(['client', 'master', 'service', 'services', 'bookingOrder']);

        $serviceNames = $appointment->services->isNotEmpty()
            ? $appointment->services->pluck('name')->filter()->values()->all()
            : [($appointment->service?->name ?? '—')];

        $startAt = $this->formatDateTime($appointment->start_at);
        $endAt = $this->formatDateTime($appointment->end_at);
        $clientName = trim((string) ($appointment->client?->name ?? '')) ?: 'Гость';
        $clientPhone = trim((string) ($appointment->client?->phone ?? '')) ?: '—';
        $masterName = trim((string) ($appointment->master?->name ?? '')) ?: 'Не назначен';
        $orderLabel = $appointment->booking_order_id ? '#'.$appointment->booking_order_id : '—';
        $sourceLabel = $this->sourceLabel((string) $appointment->source);
        $comment = trim((string) ($appointment->comment ?? ''));

        $lines = [
            'Новая онлайн-запись',
            sprintf('Заказ: %s', $orderLabel),
            sprintf('Запись: #%d', (int) $appointment->id),
            sprintf('Клиент: %s', $clientName),
            sprintf('Телефон: %s', $clientPhone),
            sprintf('Мастер: %s', $masterName),
            sprintf('Услуги: %s', implode(', ', $serviceNames)),
            sprintf('Время: %s - %s (Asia/Yerevan)', $startAt, $endAt),
            sprintf('Источник: %s', $sourceLabel),
            sprintf('Статус: %s', $appointment->status->value),
        ];

        if ($comment !== '') {
            $lines[] = 'Комментарий: '.Str::limit($comment, 500);
        }

        $message = implode("\n", $lines);

        Log::channel(config('logging.default'))->info($message);

        try {
            $this->telegramWebhookClient->send($message);
        } catch (Throwable $exception) {
            Log::warning('Failed to send appointment Telegram notification.', [
                'appointment_id' => $appointment->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ((bool) config('services.appointments.send_email_to_client', false)) {
            try {
                $appointment->client->notify(new AppointmentCreatedMailNotification($appointment));
            } catch (Throwable $exception) {
                Log::warning('Failed to send appointment email notification.', [
                    'appointment_id' => $appointment->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }

    private function formatDateTime(mixed $value): string
    {
        if ($value instanceof CarbonInterface) {
            return $value->copy()->timezone('Asia/Yerevan')->format('Y-m-d H:i');
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value)->timezone('Asia/Yerevan')->format('Y-m-d H:i');
            } catch (Throwable) {
                return $value;
            }
        }

        return '—';
    }

    private function sourceLabel(string $source): string
    {
        return match ($source) {
            'site' => 'Сайт',
            'phone' => 'Телефон',
            'instagram' => 'Instagram',
            'yandex_maps' => 'Yandex Maps',
            default => $source,
        };
    }
}
