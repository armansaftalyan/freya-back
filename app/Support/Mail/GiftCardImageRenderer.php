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
        $width = 1200;
        $height = 756;

        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            return '';
        }

        imageantialias($image, true);

        [$start, $middle, $end, $glow] = $this->themePalette($theme);
        $this->drawHorizontalGradient($image, $width, $height, $start, $middle, $end);
        $this->drawGlow($image, 160, 120, 220, [255, 255, 255], 92);
        $this->drawGlow($image, 1020, 640, 240, $glow, 88);
        $this->drawDiagonalSheen($image, $width, $height);

        $whiteSoft = imagecolorallocatealpha($image, 255, 255, 255, 75);
        imageline($image, 72, 214, 1128, 214, $whiteSoft);

        $fontBold = $this->resolveFontPath(true);
        $fontRegular = $this->resolveFontPath(false);

        $labelColor = imagecolorallocatealpha($image, 245, 235, 225, 32);
        $amountColor = imagecolorallocate($image, 255, 255, 255);
        $hintColor = imagecolorallocatealpha($image, 245, 235, 225, 22);

        $this->drawText($image, $fontRegular, 26, 0, 74, 64, $labelColor, $label);
        $this->drawText($image, $fontBold, 76, 0, 74, 154, $amountColor, $this->formatAmount($amount, $currency));
        $this->drawText($image, $fontRegular, 24, 0, 74, 590, $hintColor, 'Freya');
        $this->drawText($image, $fontRegular, 28, 0, 74, 636, $hintColor, $hint);
        $this->drawText($image, $fontRegular, 22, 0, 74, 694, $hintColor, $code);

        $this->drawBadge($image, 980, 58, 136, 136);
        $this->drawLogo($image, 1018, 96, 60, 60);

        $this->drawBadge($image, 934, 470, 184, 184);
        $this->drawQr($image, $token, 956, 492, 140, 140);

        ob_start();
        imagepng($image);
        $png = (string) ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    private function themePalette(string $theme): array
    {
        return match ($theme) {
            'black' => [[11, 11, 11], [26, 26, 26], [49, 49, 49], [160, 160, 160]],
            'rose' => [[43, 17, 25], [107, 35, 56], [215, 122, 154], [255, 205, 220]],
            default => [[18, 18, 18], [43, 34, 23], [215, 162, 75], [255, 217, 128]],
        };
    }

    private function drawHorizontalGradient($image, int $width, int $height, array $start, array $middle, array $end): void
    {
        for ($x = 0; $x < $width; $x++) {
            $ratio = $x / max(1, $width - 1);
            if ($ratio < 0.55) {
                $localRatio = $ratio / 0.55;
                $rgb = $this->mix($start, $middle, $localRatio);
            } else {
                $localRatio = ($ratio - 0.55) / 0.45;
                $rgb = $this->mix($middle, $end, $localRatio);
            }

            $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
            imageline($image, $x, 0, $x, $height, $color);
        }
    }

    private function drawGlow($image, int $cx, int $cy, int $radius, array $rgb, int $alpha): void
    {
        for ($i = $radius; $i > 0; $i -= 6) {
            $fade = 1 - ($i / $radius);
            $color = imagecolorallocatealpha(
                $image,
                $rgb[0],
                $rgb[1],
                $rgb[2],
                min(127, (int) round($alpha + ($fade * 36)))
            );
            imagefilledellipse($image, $cx, $cy, $i * 2, $i * 2, $color);
        }
    }

    private function drawDiagonalSheen($image, int $width, int $height): void
    {
        for ($i = -240; $i < $width + 240; $i += 2) {
            $alpha = abs(($i % 220) - 110) < 24 ? 104 : 124;
            $color = imagecolorallocatealpha($image, 255, 255, 255, $alpha);
            imageline($image, $i, 0, $i - 240, $height, $color);
        }
    }

    private function drawBadge($image, int $x, int $y, int $width, int $height): void
    {
        $fill = imagecolorallocatealpha($image, 255, 255, 255, 8);
        $stroke = imagecolorallocatealpha($image, 255, 255, 255, 52);
        $this->drawRoundedRectangle($image, $x, $y, $width, $height, 26, $fill, true);
        $this->drawRoundedRectangle($image, $x, $y, $width, $height, 26, $stroke, false, 2);
    }

    private function drawLogo($image, int $x, int $y, int $width, int $height): void
    {
        $logoPath = public_path('logo.png');
        if (! is_file($logoPath)) {
            return;
        }

        $logo = @imagecreatefrompng($logoPath);
        if ($logo === false) {
            return;
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagecopyresampled($image, $logo, $x, $y, 0, 0, $width, $height, imagesx($logo), imagesy($logo));
        imagedestroy($logo);
    }

    private function drawQr($image, string $token, int $x, int $y, int $width, int $height): void
    {
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

        imagecopyresampled($image, $qrImage, $x, $y, 0, 0, $width, $height, imagesx($qrImage), imagesy($qrImage));
        imagedestroy($qrImage);
    }

    private function drawText($image, ?string $fontPath, int $size, float $angle, int $x, int $y, int $color, string $text): void
    {
        if ($fontPath !== null && is_file($fontPath)) {
            imagettftext($image, $size, $angle, $x, $y, $color, $fontPath, $text);
            return;
        }

        imagestring($image, 5, $x, max(0, $y - 18), $text, $color);
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
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
            ]
            : [
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                '/usr/share/fonts/dejavu/DejaVuSans.ttf',
            ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function mix(array $from, array $to, float $ratio): array
    {
        return [
            (int) round($from[0] + (($to[0] - $from[0]) * $ratio)),
            (int) round($from[1] + (($to[1] - $from[1]) * $ratio)),
            (int) round($from[2] + (($to[2] - $from[2]) * $ratio)),
        ];
    }

    private function formatAmount(float $amount, string $currency): string
    {
        return number_format($amount, 2, '.', ',').' '.$currency;
    }
}
