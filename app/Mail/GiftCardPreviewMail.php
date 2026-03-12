<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
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
                'qrPngData' => $this->qrPngData(),
            ]);
    }

    private function qrPngData(): ?string
    {
        $qrUrl = 'https://quickchart.io/qr?size=220&format=png&text='.urlencode((string) $this->token);

        try {
            $response = Http::timeout(10)->get($qrUrl);
            if (! $response->successful()) {
                return null;
            }

            return (string) $response->body();
        } catch (\Throwable) {
            return null;
        }
    }
}
