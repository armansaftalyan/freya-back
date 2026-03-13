@php
    $cardSrc = null;
    if (isset($message) && !empty($cardImagePng ?? null)) {
        $cardSrc = $message->embedData($cardImagePng, 'freya-gift-card-preview.png', 'image/png');
    }
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gift Card Preview</title>
</head>
<body style="margin:0;padding:0;background:#f6f4f1;font-family:Arial,sans-serif;color:#1f1a16;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4f1;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:640px;max-width:96%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #eadfce;">
                <tr>
                    <td style="padding:24px 24px 8px 24px;">
                        <h1 style="margin:0 0 10px 0;font-size:24px;line-height:1.3;">Freya Beauty Gift Card</h1>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;">Hello {{ $recipientName }}, your gift card is ready.</p>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;"><strong>Amount:</strong> {{ number_format((float) $amount, 0, '.', ' ') }} {{ $currency }}</p>
                        <p style="margin:0;font-size:14px;color:#5b5149;"><strong>Code:</strong> {{ $code }}</p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding:16px 24px 8px 24px;">
                        @if ($cardSrc)
                            <img src="{{ $cardSrc }}" alt="Freya gift card" width="560" style="display:block;width:100%;max-width:560px;height:auto;border:0;">
                        @else
                            <p style="margin:0;font-size:14px;color:#5b5149;">The gift card image is attached to this email as a PNG file.</p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px 24px 24px 24px;">
                        <p style="margin:0;font-size:13px;line-height:1.6;color:#7a6f64;">
                            The gift card is also attached as a PNG file, so the recipient can save it directly to the phone gallery and present the QR code in the salon.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
