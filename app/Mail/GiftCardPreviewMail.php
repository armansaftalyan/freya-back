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
        $appUrl = rtrim((string) config('app.url', 'http://localhost'), '/');

        return $this
            ->subject('Gift Card Purchase Preview')
            ->view('mail.gift-card-preview')
            ->with([
                'recipientName' => $this->recipientName,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'code' => $this->code,
                'token' => $this->token,
                'qrImageUrl' => $this->qrImageUrl($appUrl),
                'logoUrl' => $appUrl.'/logo.png',
            ]);
    }

    private function qrImageUrl(string $appUrl): string
    {
        return $appUrl.'/mail/qr/'.urlencode((string) $this->token).'.png';
    }
}
