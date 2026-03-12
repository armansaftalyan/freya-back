<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class GiftCardPreviewMail extends Mailable
{
    use Queueable;

    public function __construct(
        public readonly string $recipientName,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $code,
        public readonly string $token,
    ) {
    }

    public function build(): self
    {
        $qrPayload = $this->qrPayload();

        return $this
            ->subject('Gift Card Purchase Preview')
            ->view('mail.gift-card-preview')
            ->with([
                'recipientName' => $this->recipientName,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'code' => $this->code,
                'token' => $this->token,
                'qrImageUrl' => $this->qrImageUrl($qrPayload),
                'logoImageUrl' => $this->logoImageUrl(),
            ]);
    }

    private function qrPayload(): string
    {
        $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');

        return $frontendUrl.'/account/gift-cards/scan/'.urlencode((string) $this->token);
    }

    private function qrImageUrl(string $payload): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return $baseUrl.'/mail/qr/'.urlencode($payload).'.png';
    }

    private function logoImageUrl(): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        return $baseUrl.'/logo.png';
    }
}
