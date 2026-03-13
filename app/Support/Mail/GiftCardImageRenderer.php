<?php

declare(strict_types=1);

namespace App\Support\Mail;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class GiftCardImageRenderer
{
    public function render(
        float $amount,
        string $currency,
        string $code,
        string $token,
        string $theme = 'gold',
        string $label = 'FREYA BEAUTY GIFT PLUS',
        string $hint = 'Use in salon with QR scan',
    ): string {
        $templatePath = $this->templatePath($theme);
        if (! is_file($templatePath)) {
            return '';
        }

        $template = @imagecreatefrompng($templatePath);
        if ($template === false) {
            return '';
        }

        $width = imagesx($template);
        $height = imagesy($template);
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            imagedestroy($template);
            return '';
        }

        imagealphablending($image, true);
        imagesavealpha($image, false);
        imageantialias($image, true);
        $background = imagecolorallocate($image, 246, 244, 241);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        imagecopy($image, $template, 0, 0, 0, 0, $width, $height);
        imagedestroy($template);

        $isAmd = strtoupper($currency) === 'AMD';
        $fontBold = $this->resolveAmountFontPath($isAmd);
        $amountColor = imagecolorallocate($image, 255, 255, 255);
        $amountX = 64;
        $amountY = 204;
        $amountText = $this->formatAmount($amount, $currency);

        $this->drawText(
            $image,
            $fontBold,
            50,
            0,
            $amountX,
            $amountY,
            $amountColor,
            $amountText
        );

        if ($isAmd) {
            $this->drawDramSymbol(
                $image,
                $fontBold,
                50,
                $amountX + $this->measureTextWidth($fontBold, 50, $amountText) + 14,
                $amountY - 44,
                $amountColor
            );
        }

        $this->drawQrBadge($image, $token, 878, 386, 154, 154);

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    private function templatePath(string $theme): string
    {
        $normalizedTheme = in_array($theme, ['gold', 'black', 'rose'], true) ? $theme : 'gold';

        return resource_path('gift-card-templates/'.$normalizedTheme.'.png');
    }

    private function drawQrBadge($image, string $token, int $x, int $y, int $width, int $height): void
    {
        $fill = imagecolorallocatealpha($image, 255, 255, 255, 8);
        $stroke = imagecolorallocatealpha($image, 255, 255, 255, 46);
        $this->drawRoundedRectangle($image, $x, $y, $width, $height, 22, $fill, true);
        $this->drawRoundedRectangle($image, $x, $y, $width, $height, 22, $stroke, false, 2);

        $qr = (new Builder(
            data: $token,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: 220,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        ))->build();

        $qrImage = @imagecreatefromstring($qr->getString());
        if ($qrImage === false) {
            return;
        }

        imagecopyresampled(
            $image,
            $qrImage,
            $x + 18,
            $y + 18,
            0,
            0,
            $width - 36,
            $height - 36,
            imagesx($qrImage),
            imagesy($qrImage)
        );

        imagedestroy($qrImage);
    }

    private function drawText($image, ?string $fontPath, int $size, float $angle, int $x, int $y, int $color, string $text): void
    {
        if ($fontPath !== null && is_file($fontPath)) {
            $shadow = imagecolorallocatealpha($image, 0, 0, 0, 92);
            imagettftext($image, $size, $angle, $x + 1, $y + 2, $shadow, $fontPath, $text);
            imagettftext($image, $size, $angle, $x, $y, $color, $fontPath, $text);
            return;
        }

        imagestring($image, 5, $x, max(0, $y - 18), $text, $color);
    }

    private function drawDramSymbol($image, ?string $fontPath, int $size, int $x, int $y, int $color): void
    {
        imagesetthickness($image, 5);
        imageline($image, $x + 18, $y, $x + 18, $y + 54, $color);
        imageline($image, $x + 36, $y, $x + 36, $y + 54, $color);
        imageline($image, $x + 18, $y, $x + 36, $y, $color);
        imageline($image, $x + 18, $y + 54, $x + 36, $y + 54, $color);
        imageline($image, $x + 8, $y + 18, $x + 46, $y + 18, $color);
        imageline($image, $x + 8, $y + 36, $x + 46, $y + 36, $color);
        imagesetthickness($image, 1);
    }

    private function drawRoundedRectangle($image, int $x, int $y, int $width, int $height, int $radius, int $color, bool $filled, int $thickness = 1): void
    {
        if ($filled) {
            imagefilledrectangle($image, $x + $radius, $y, $x + $width - $radius, $y + $height, $color);
            imagefilledrectangle($image, $x, $y + $radius, $x + $width, $y + $height - $radius, $color);
            imagefilledellipse($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, $color);
            imagefilledellipse($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, $color);
            imagefilledellipse($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
            imagefilledellipse($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, $color);
            return;
        }

        imagesetthickness($image, $thickness);
        imageline($image, $x + $radius, $y, $x + $width - $radius, $y, $color);
        imageline($image, $x + $radius, $y + $height, $x + $width - $radius, $y + $height, $color);
        imageline($image, $x, $y + $radius, $x, $y + $height - $radius, $color);
        imageline($image, $x + $width, $y + $radius, $x + $width, $y + $height - $radius, $color);
        imagearc($image, $x + $radius, $y + $radius, $radius * 2, $radius * 2, 180, 270, $color);
        imagearc($image, $x + $width - $radius, $y + $radius, $radius * 2, $radius * 2, 270, 360, $color);
        imagearc($image, $x + $radius, $y + $height - $radius, $radius * 2, $radius * 2, 90, 180, $color);
        imagearc($image, $x + $width - $radius, $y + $height - $radius, $radius * 2, $radius * 2, 0, 90, $color);
        imagesetthickness($image, 1);
    }

    private function resolveFontPath(bool $bold): ?string
    {
        $candidates = $bold
            ? [
                '/usr/share/fonts/truetype/noto/NotoSansDisplay-Bold.ttf',
                '/usr/share/fonts/truetype/noto/NotoSans-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                '/usr/share/fonts/truetype/noto/NotoSansDisplay-Regular.ttf',
                '/usr/share/fonts/truetype/noto/NotoSans-Regular.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function resolveAmountFontPath(bool $preferArmenian): ?string
    {
        $candidates = $preferArmenian
            ? [
                '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
                '/usr/share/fonts/truetype/freefont/FreeSans.ttf',
                '/usr/share/fonts/truetype/noto/NotoSansArmenian-Bold.ttf',
                '/usr/share/fonts/truetype/noto/NotoSansArmenian-Regular.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            ]
            : [
                '/usr/share/fonts/truetype/noto/NotoSansDisplay-Bold.ttf',
                '/usr/share/fonts/truetype/noto/NotoSans-Bold.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return $this->resolveFontPath(true);
    }

    private function formatAmount(float $amount, string $currency): string
    {
        if (strtoupper($currency) === 'AMD') {
            return number_format($amount, 0, '.', ' ');
        }

        return number_format($amount, 2, ',', ' ').' '.$currency;
    }

    private function measureTextWidth(?string $fontPath, int $size, string $text): int
    {
        if ($fontPath !== null && is_file($fontPath)) {
            $box = imagettfbbox($size, 0, $fontPath, $text);
            if (is_array($box)) {
                return (int) abs($box[2] - $box[0]);
            }
        }

        return strlen($text) * 18;
    }
}
