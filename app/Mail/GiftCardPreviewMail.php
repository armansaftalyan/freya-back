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
        public readonly string $cardTheme = 'gold',
        public readonly string $mailLocale = 'en',
    ) {
    }

    public function build(): self
    {
        $cardImagePng = $this->cardImagePng();
        $locale = $this->normalizeLocale($this->mailLocale);
        $copy = $this->copy($locale);

        return $this
            ->subject($copy['subject'])
            ->view('mail.gift-card-preview')
            ->attachData($cardImagePng, $this->attachmentFileName(), [
                'mime' => 'image/png',
            ])
            ->with([
                'locale' => $locale,
                'copy' => $copy,
                'recipientName' => $this->recipientName,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'code' => $this->code,
                'token' => $this->token,
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
            theme: $this->cardTheme,
        );
    }

    private function attachmentFileName(): string
    {
        $safeCode = preg_replace('/[^A-Za-z0-9_-]+/', '-', $this->code) ?: 'gift-card';
        return $safeCode.'.png';
    }

    private function normalizeLocale(string $locale): string
    {
        return match (strtolower(trim($locale))) {
            'ru', 'hy', 'en' => strtolower(trim($locale)),
            default => 'en',
        };
    }

    private function copy(string $locale): array
    {
        return match ($locale) {
            'ru' => [
                'subject' => 'Подарочная карта Freya Beauty',
                'title' => 'Подарочная карта Freya Beauty',
                'intro' => 'Здравствуйте, :name. Ваша подарочная карта готова.',
                'amount' => 'Сумма',
                'code' => 'Код',
                'attachment_notice' => 'Карта прикреплена к письму в PNG-файле.',
            ],
            'hy' => [
                'subject' => 'Freya Beauty նվեր քարտ',
                'title' => 'Freya Beauty նվեր քարտ',
                'intro' => 'Բարեւ, :name։ Ձեր նվեր քարտը պատրաստ է։',
                'amount' => 'Գումար',
                'code' => 'Կոդ',
                'attachment_notice' => 'Քարտը կցված է նամակին PNG ֆայլով։',
            ],
            default => [
                'subject' => 'Freya Beauty Gift Card',
                'title' => 'Freya Beauty Gift Card',
                'intro' => 'Hello, :name. Your gift card is ready.',
                'amount' => 'Amount',
                'code' => 'Code',
                'attachment_notice' => 'The gift card is attached to this email as a PNG file.',
            ],
        };
    }
}
