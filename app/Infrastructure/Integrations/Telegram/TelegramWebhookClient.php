<?php

declare(strict_types=1);

namespace App\Infrastructure\Integrations\Telegram;

use Illuminate\Support\Facades\Http;

class TelegramWebhookClient
{
    public function send(string $message): void
    {
        $url = config('services.telegram.webhook_url');

        if (! $url) {
            return;
        }

        Http::timeout(3)->post($url, [
            'text' => $message,
        ]);
    }
}
