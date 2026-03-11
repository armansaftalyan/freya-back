@php
    $formattedAmount = number_format((float) $amount, 0, '.', ' ');
    $scanUrl = rtrim((string) config('app.url', 'http://localhost'), '/').'/account/gift-cards/scan/'.urlencode((string) $token);
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
                                <td style="padding-bottom:10px;">
                                    <div style="border-radius:14px;padding:14px;background:linear-gradient(140deg,#121212 0%,#2b2217 32%,#7e5925 68%,#d7a24b 100%);color:#fff;">
                                        <div style="font-size:11px;letter-spacing:0.12em;text-transform:uppercase;opacity:.85;">Gold</div>
                                        <div style="font-size:26px;font-weight:700;margin-top:8px;">{{ $formattedAmount }} {{ $currency }}</div>
                                        <div style="font-size:12px;margin-top:18px;opacity:.9;">Use in salon with QR scan</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding-bottom:10px;">
                                    <div style="border-radius:14px;padding:14px;background:linear-gradient(145deg,#0b0b0b 0%,#1a1a1a 36%,#313131 100%);color:#fff;">
                                        <div style="font-size:11px;letter-spacing:0.12em;text-transform:uppercase;opacity:.85;">Black</div>
                                        <div style="font-size:26px;font-weight:700;margin-top:8px;">{{ $formattedAmount }} {{ $currency }}</div>
                                        <div style="font-size:12px;margin-top:18px;opacity:.9;">Use in salon with QR scan</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="border-radius:14px;padding:14px;background:linear-gradient(145deg,#2b1119 0%,#6b2338 42%,#d77a9a 100%);color:#fff;">
                                        <div style="font-size:11px;letter-spacing:0.12em;text-transform:uppercase;opacity:.85;">Rose</div>
                                        <div style="font-size:26px;font-weight:700;margin-top:8px;">{{ $formattedAmount }} {{ $currency }}</div>
                                        <div style="font-size:12px;margin-top:18px;opacity:.9;">Use in salon with QR scan</div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding:16px 24px 24px 24px;">
                        <p style="margin:0 0 8px 0;font-size:13px;color:#5b5149;"><strong>QR payload now:</strong></p>
                        <p style="margin:0 0 8px 0;font-size:13px;color:#5b5149;word-break:break-all;">{{ $scanUrl }}</p>
                        <p style="margin:0;font-size:13px;color:#5b5149;word-break:break-all;"><strong>Token:</strong> {{ $token }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
