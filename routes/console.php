<?php

use App\Mail\GiftCardPreviewMail;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('giftcard:test-mail {email} {--name=Arman} {--amount=10000} {--currency=AMD} {--code=FREYA-TEST-0001} {--token=TEST_QR_TOKEN_123} {--theme=gold} {--locale=en}', function () {
    $email = (string) $this->argument('email');
    $name = (string) $this->option('name');
    $amount = (float) $this->option('amount');
    $currency = (string) $this->option('currency');
    $code = (string) $this->option('code');
    $token = (string) $this->option('token');
    $theme = (string) $this->option('theme');
    $locale = (string) $this->option('locale');

    Mail::to($email)->send(new GiftCardPreviewMail(
        recipientName: $name,
        amount: $amount,
        currency: $currency,
        code: $code,
        token: $token,
        theme: $theme,
        locale: $locale,
    ));

    $mailer = (string) config('mail.default');
    $this->info("Gift card preview mail queued/sent via mailer [{$mailer}] to {$email}");

    if ($mailer === 'log') {
        $this->warn('MAIL_MAILER=log, so email was written to logs and not delivered to Gmail.');
    }
})->purpose('Send gift card preview email');
