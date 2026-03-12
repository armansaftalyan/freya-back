@php
    $formattedAmount = number_format((float) $amount, 0, '.', ' ');
    $qrSrc = $qrImageUrl;
    if (isset($message) && !empty($qrImagePng ?? null)) {
        $qrSrc = $message->embedData($qrImagePng, 'gift-card-qr.png', 'image/png');
    }
    $logoSrc = null;
    if (isset($message) && !empty($logoImagePng ?? null)) {
        $logoSrc = $message->embedData($logoImagePng, 'freya-logo.png', 'image/png');
    }
    $cardThemes = [
        [
            'label' => 'Gold',
            'background' => 'linear-gradient(140deg,#121212 0%,#2b2217 32%,#7e5925 68%,#d7a24b 100%)',
        ],
        [
            'label' => 'Black',
            'background' => 'linear-gradient(145deg,#0b0b0b 0%,#1a1a1a 36%,#313131 100%)',
        ],
        [
            'label' => 'Rose',
            'background' => 'linear-gradient(145deg,#2b1119 0%,#6b2338 42%,#d77a9a 100%)',
        ],
    ];
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gift Card Preview</title>
</head>
<body style="margin:0;padding:0;background:#f6f4f1;font-family:Arial,sans-serif;color:#1f1a16;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4f1;padding:20px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:640px;max-width:96%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #eadfce;">
                <tr>
                    <td style="padding:24px 24px 12px 24px;">
                        <h1 style="margin:0 0 10px 0;font-size:24px;line-height:1.3;">Freya Beauty Gift Card</h1>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;">Hello {{ $recipientName }}, this is a test email template with all 3 card styles.</p>
                        <p style="margin:0;font-size:14px;color:#5b5149;"><strong>Amount:</strong> {{ $formattedAmount }} {{ $currency }} | <strong>Code:</strong> {{ $code }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 24px 8px 24px;">
                        <div style="font-size:13px;color:#5b5149;margin-bottom:10px;">Card styles from frontend:</div>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            @foreach ($cardThemes as $theme)
                            <tr>
                                <td align="center" style="padding-bottom:10px;">
                                    <table role="presentation" width="460" cellspacing="0" cellpadding="0" style="width:460px;max-width:100%;border-radius:20px;overflow:hidden;background:{{ $theme['background'] }};color:#fff;box-shadow:0 14px 30px rgba(0,0,0,.25);">
                                        <tr>
                                            <td style="padding:20px 20px 18px 20px;vertical-align:top;">
                                                <div style="font-size:11px;letter-spacing:0.16em;text-transform:uppercase;opacity:.85;">{{ $theme['label'] }}</div>
                                                <div style="font-size:42px;font-weight:700;margin-top:10px;line-height:1;">{{ $formattedAmount }}</div>
                                                <div style="font-size:24px;font-weight:700;margin-top:6px;line-height:1;">{{ $currency }}</div>
                                                <div style="margin-top:12px;height:1px;background:rgba(255,255,255,.25);"></div>
                                                <div style="font-size:12px;margin-top:14px;opacity:.95;">Freya Beauty Gift Card</div>
                                                <div style="font-size:12px;margin-top:6px;opacity:.85;">{{ $code }}</div>
                                                <div style="font-size:11px;margin-top:20px;opacity:.9;">Use in salon with QR scan</div>
                                            </td>
                                            <td width="136" style="padding:20px 20px 18px 0;vertical-align:top;">
                                                <table role="presentation" width="56" cellspacing="0" cellpadding="0" style="margin-left:auto;margin-bottom:10px;background:rgba(255,255,255,.92);border-radius:14px;">
                                                    <tr>
                                                        <td style="padding:6px;text-align:center;">
                                                            @if ($logoSrc)
                                                            <img src="{{ $logoSrc }}" alt="Freya" width="36" height="36" style="display:block;margin:0 auto;border:0;width:36px;height:36px;border-radius:8px;">
                                                            @else
                                                            <span style="display:inline-block;padding:6px 4px;font-size:11px;font-weight:700;color:#111;letter-spacing:0.08em;">FREYA</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table role="presentation" width="108" cellspacing="0" cellpadding="0" style="background:#fff;border-radius:14px;box-shadow:0 8px 22px rgba(0,0,0,.28);">
                                                    <tr>
                                                        <td style="padding:8px;">
                                                            <img src="{{ $qrSrc }}" alt="QR code" width="92" height="92" style="display:block;border:0;width:92px;height:92px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
