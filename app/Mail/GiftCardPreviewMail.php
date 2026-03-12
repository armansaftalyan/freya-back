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
                'qrImagePng' => $this->qrImagePng($qrPayload),
                'logoImagePng' => $this->logoImagePng(),
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

    private function qrImagePng(string $payload): string
    {
        return (new Builder(
            data: $payload,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))
            ->build()
            ->getString();
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
}
