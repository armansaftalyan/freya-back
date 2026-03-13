<?php

declare(strict_types=1);

namespace App\Mail;

use App\Support\Mail\GiftCardImageRenderer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class GiftCardPreviewMail extends Mailable
{
    use Queueable;

    private ?string $cardImagePng = null;

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
        $cardImagePng = $this->cardImagePng();

        return $this
            ->subject('Gift Card Purchase Preview')
            ->view('mail.gift-card-preview')
            ->attachData($cardImagePng, $this->attachmentFileName(), [
                'mime' => 'image/png',
            ])
            ->with([
                'recipientName' => $this->recipientName,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'code' => $this->code,
                'token' => $this->token,
                'cardImagePng' => $cardImagePng,
            ]);
    }

    private function cardImagePng(): string
    {
        if ($this->cardImagePng !== null) {
            return $this->cardImagePng;
        }

        return $this->cardImagePng = app(GiftCardImageRenderer::class)->render(
            amount: $this->amount,
            currency: $this->currency,
            code: $this->code,
            token: $this->token,
        );
    }

    private function attachmentFileName(): string
    {
        $safeCode = preg_replace('/[^A-Za-z0-9_-]+/', '-', $this->code) ?: 'gift-card';
        return $safeCode.'.png';
    }
}
