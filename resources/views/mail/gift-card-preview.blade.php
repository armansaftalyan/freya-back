<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $copy['title'] }}</title>
</head>
<body style="margin:0;padding:0;background:#f6f4f1;font-family:Arial,sans-serif;color:#1f1a16;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f4f1;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellspacing="0" cellpadding="0" style="width:640px;max-width:96%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #eadfce;">
                <tr>
                    <td style="padding:24px;">
                        <h1 style="margin:0 0 10px 0;font-size:24px;line-height:1.3;">{{ $copy['title'] }}</h1>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;">{{ str_replace(':name', $recipientName, $copy['intro']) }}</p>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;"><strong>{{ $copy['amount'] }}:</strong> {{ number_format((float) $amount, 0, '.', ' ') }} {{ $currency }}</p>
                        <p style="margin:0 0 6px 0;font-size:14px;color:#5b5149;"><strong>{{ $copy['code'] }}:</strong> {{ $code }}</p>
                        <p style="margin:12px 0 0 0;font-size:14px;line-height:1.6;color:#7a6f64;">{{ $copy['attachment_notice'] }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
