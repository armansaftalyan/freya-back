@php
    $formattedAmount = number_format((float) $amount, 0, '.', ' ');
    $formattedAmountOnCard = number_format((float) $amount, 2, ',', ' ');
    $qrSrc = $qrImageUrl;
    $logoSrc = $logoImageUrl ?? null;
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
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;">Hello {{ $recipientName }}, your gift card is ready.</p>
                        <p style="margin:0;font-size:14px;color:#5b5149;"><strong>Amount:</strong> {{ $formattedAmount }} {{ $currency }} | <strong>Code:</strong> {{ $code }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:12px 24px 24px 24px;">
                        <table role="presentation" width="360" cellspacing="0" cellpadding="0" style="width:360px;max-width:100%;border:1px solid rgba(243,226,196,.35);border-radius:24px;overflow:hidden;background:
                            radial-gradient(circle at 14% 9%,rgba(255,255,255,.18),rgba(255,255,255,0) 36%),
                            radial-gradient(circle at 88% 88%,rgba(251,191,36,.22),rgba(251,191,36,0) 44%),
                            linear-gradient(120deg,transparent 38%,rgba(255,255,255,.18) 50%,transparent 62%),
                            linear-gradient(140deg,#121212 0%,#2b2217 32%,#7e5925 68%,#d7a24b 100%);
                            color:#ffffff;box-shadow:0 20px 50px rgba(10,10,10,.45);">
                            <tr>
                                <td style="height:227px;padding:20px;vertical-align:top;">
                                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                        <tr>
                                            <td style="vertical-align:top;padding-right:8px;">
                                                <div style="font-size:10px;line-height:1.2;letter-spacing:0.22em;text-transform:uppercase;color:rgba(253,242,230,.86);">FREYA BEAUTY ՆՎԵՐ ՔԱՐՏ</div>
                                                <div style="margin-top:8px;font-size:30px;font-weight:700;line-height:1;white-space:nowrap;color:#ffffff;">{{ $formattedAmountOnCard }} {{ $currency }}</div>
                                            </td>
                                            <td width="96" style="vertical-align:top;">
                                                <table role="presentation" width="54" cellspacing="0" cellpadding="0" style="margin-left:auto;background:rgba(255,255,255,.92);border:1px solid rgba(255,255,255,.45);border-radius:16px;box-shadow:0 8px 24px rgba(0,0,0,.35);">
                                                    <tr>
                                                        <td style="padding:8px;text-align:center;">
                                                            <span>{{ $logoSrc }}</span>
                                                            @if ($logoSrc)
                                                            <img src="{{ $logoSrc }}" alt="Freya logo" width="36" height="36" style="display:block;margin:0 auto;border:0;width:36px;height:36px;border-radius:8px;object-fit:contain;">
                                                            @else
                                                            <span style="display:inline-block;padding:6px 4px;font-size:11px;font-weight:700;color:#111;letter-spacing:0.08em;">FREYA</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding-top:14px;">
                                                <div style="height:1px;background:rgba(255,255,255,.25);"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="height:96px;vertical-align:bottom;padding-top:12px;">
                                                <div style="font-size:10px;letter-spacing:0.16em;text-transform:uppercase;color:rgba(253,242,230,.78);">Freya</div>
                                                <div style="margin-top:4px;font-size:14px;line-height:1.3;color:rgba(253,242,230,.92);">Օգտագործվում է սրահում QR սկանով</div>
                                            </td>
                                            <td width="96" style="vertical-align:bottom;padding-top:12px;">
                                                <table role="presentation" width="88" cellspacing="0" cellpadding="0" style="margin-left:auto;background:#ffffff;border-radius:12px;box-shadow:0 8px 22px rgba(0,0,0,.28);">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $qrSrc }}" alt="{{ $qrSrc }}" width="76" height="76" style="display:block;border:0;width:76px;height:76px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
