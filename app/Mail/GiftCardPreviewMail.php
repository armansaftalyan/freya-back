<?php

declare(strict_types=1);

namespace App\Mail;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
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
                'qrImageUrl' => $this->qrImageUrl($this->token),
                'qrImagePng' => $this->qrImagePng($this->token),
                'logoImageUrl' => $this->logoImageUrl(),
                'logoImagePng' => $this->logoImagePng(),
            ]);
    }

    private function qrImageUrl(string $payload): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return $baseUrl.'/mail/qr/'.urlencode($payload).'.png';
    }

    private function logoImageUrl(): string
    {
        $baseUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');
        return $baseUrl.'/logo.png';
    }

    private function logoImagePng(): ?string
    {
        $logoPath = public_path('logo.png');
        if (! is_file($logoPath)) {
            return null;
        }

        $content = file_get_contents($logoPath);
        return $content !== false ? $content : null;
    }

    private function qrImagePng(string $payload): string
    {
        $result = (new Builder(
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))->build();

        return $result->getString();
    }
}
