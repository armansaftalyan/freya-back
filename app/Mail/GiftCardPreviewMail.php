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
        return $this
            ->subject('Gift Card Purchase Preview')
            ->view('mail.gift-card-preview')
            ->with([
                'recipientName' => $this->recipientName,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'code' => $this->code,
                'token' => $this->token,
                'qrImageUrl' => $this->qrImageUrl(),
            ]);
    }

    private function qrImageUrl(): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data='.urlencode((string) $this->token);
    }
}
