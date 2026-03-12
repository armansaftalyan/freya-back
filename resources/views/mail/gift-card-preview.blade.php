@php
    $formattedAmount = number_format((float) $amount, 0, '.', ' ');
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
                            <tr>
                                <td align="center" style="padding-bottom:10px;">
                                    <table role="presentation" width="360" cellspacing="0" cellpadding="0" style="width:360px;max-width:100%;border-radius:18px;overflow:hidden;background:linear-gradient(140deg,#121212 0%,#2b2217 32%,#7e5925 68%,#d7a24b 100%);color:#fff;box-shadow:0 14px 30px rgba(0,0,0,.25);">
                                        <tr>
                                            <td style="padding:14px 14px 12px 14px;vertical-align:top;">
                                                <div style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;opacity:.85;">Gold</div>
                                                <div style="font-size:25px;font-weight:700;margin-top:8px;line-height:1;">{{ $formattedAmount }} {{ $currency }}</div>
                                                <div style="margin-top:8px;height:1px;background:rgba(255,255,255,.25);"></div>
                                                <div style="font-size:11px;margin-top:12px;opacity:.95;">Freya Beauty Gift Card</div>
                                                <div style="font-size:11px;margin-top:6px;opacity:.85;">{{ $code }}</div>
                                                <div style="font-size:10px;margin-top:12px;opacity:.9;">Use in salon with QR scan</div>
                                            </td>
                                            <td width="92" style="padding:14px 14px 12px 0;vertical-align:top;">
                                                <table role="presentation" width="40" cellspacing="0" cellpadding="0" style="margin-left:auto;margin-bottom:8px;background:rgba(255,255,255,.92);border-radius:10px;">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $logoUrl }}" alt="Freya" width="28" height="28" style="display:block;border:0;width:28px;height:28px;border-radius:6px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table role="presentation" width="78" cellspacing="0" cellpadding="0" style="background:#fff;border-radius:10px;box-shadow:0 8px 22px rgba(0,0,0,.28);">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $qrImageUrl }}" alt="QR code" width="66" height="66" style="display:block;border:0;width:66px;height:66px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding-bottom:10px;">
                                    <table role="presentation" width="360" cellspacing="0" cellpadding="0" style="width:360px;max-width:100%;border-radius:18px;overflow:hidden;background:linear-gradient(145deg,#0b0b0b 0%,#1a1a1a 36%,#313131 100%);color:#fff;box-shadow:0 14px 30px rgba(0,0,0,.25);">
                                        <tr>
                                            <td style="padding:14px 14px 12px 14px;vertical-align:top;">
                                                <div style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;opacity:.85;">Black</div>
                                                <div style="font-size:25px;font-weight:700;margin-top:8px;line-height:1;">{{ $formattedAmount }} {{ $currency }}</div>
                                                <div style="margin-top:8px;height:1px;background:rgba(255,255,255,.25);"></div>
                                                <div style="font-size:11px;margin-top:12px;opacity:.95;">Freya Beauty Gift Card</div>
                                                <div style="font-size:11px;margin-top:6px;opacity:.85;">{{ $code }}</div>
                                                <div style="font-size:10px;margin-top:12px;opacity:.9;">Use in salon with QR scan</div>
                                            </td>
                                            <td width="92" style="padding:14px 14px 12px 0;vertical-align:top;">
                                                <table role="presentation" width="40" cellspacing="0" cellpadding="0" style="margin-left:auto;margin-bottom:8px;background:rgba(255,255,255,.92);border-radius:10px;">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $logoUrl }}" alt="Freya" width="28" height="28" style="display:block;border:0;width:28px;height:28px;border-radius:6px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table role="presentation" width="78" cellspacing="0" cellpadding="0" style="background:#fff;border-radius:10px;box-shadow:0 8px 22px rgba(0,0,0,.28);">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $qrImageUrl }}" alt="QR code" width="66" height="66" style="display:block;border:0;width:66px;height:66px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center">
                                    <table role="presentation" width="360" cellspacing="0" cellpadding="0" style="width:360px;max-width:100%;border-radius:18px;overflow:hidden;background:linear-gradient(145deg,#2b1119 0%,#6b2338 42%,#d77a9a 100%);color:#fff;box-shadow:0 14px 30px rgba(0,0,0,.25);">
                                        <tr>
                                            <td style="padding:14px 14px 12px 14px;vertical-align:top;">
                                                <div style="font-size:11px;letter-spacing:0.14em;text-transform:uppercase;opacity:.85;">Rose</div>
                                                <div style="font-size:25px;font-weight:700;margin-top:8px;line-height:1;">{{ $formattedAmount }} {{ $currency }}</div>
                                                <div style="margin-top:8px;height:1px;background:rgba(255,255,255,.25);"></div>
                                                <div style="font-size:11px;margin-top:12px;opacity:.95;">Freya Beauty Gift Card</div>
                                                <div style="font-size:11px;margin-top:6px;opacity:.85;">{{ $code }}</div>
                                                <div style="font-size:10px;margin-top:12px;opacity:.9;">Use in salon with QR scan</div>
                                            </td>
                                            <td width="92" style="padding:14px 14px 12px 0;vertical-align:top;">
                                                <table role="presentation" width="40" cellspacing="0" cellpadding="0" style="margin-left:auto;margin-bottom:8px;background:rgba(255,255,255,.92);border-radius:10px;">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $logoUrl }}" alt="Freya" width="28" height="28" style="display:block;border:0;width:28px;height:28px;border-radius:6px;">
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table role="presentation" width="78" cellspacing="0" cellpadding="0" style="background:#fff;border-radius:10px;box-shadow:0 8px 22px rgba(0,0,0,.28);">
                                                    <tr>
                                                        <td style="padding:6px;">
                                                            <img src="{{ $qrImageUrl }}" alt="QR code" width="66" height="66" style="display:block;border:0;width:66px;height:66px;">
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
