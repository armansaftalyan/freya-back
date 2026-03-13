<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use App\Domain\Salon\Models\GiftCard;
use App\Support\Mail\GiftCardImageRenderer;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $panel = Filament::getPanel('admin');
    $user = auth()->user();

    if ($user !== null && $user->canAccessPanel($panel)) {
        return redirect()->to($panel->getUrl());
    }

    return redirect()->to($panel->getLoginUrl());
});

Route::get('/mail/qr/{token}.png', function (string $token) {
    $decoded = trim(urldecode($token));
    if ($decoded === '') {
        abort(404);
    }

    $result = (new Builder(
        data: $decoded,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Medium,
        size: 220,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
    ))->build();

    return response($result->getString(), 200, [
        'Content-Type' => $result->getMimeType(),
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('token', '.*')->name('mail.qr');

Route::get('/mail/gift-card-preview.png', function () {
    $renderer = app(GiftCardImageRenderer::class);

    $png = $renderer->render(
        amount: (float) request()->query('amount', 10000),
        currency: (string) request()->query('currency', 'AMD'),
        code: (string) request()->query('code', 'FREYA-TEST-0001'),
        token: (string) request()->query('token', 'TEST_QR_TOKEN_123'),
        theme: (string) request()->query('theme', 'gold'),
    );

    return response($png, 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    ]);
})->name('mail.gift-card-preview');

Route::get('/gift-cards/{token}/image.png', function (string $token) {
    $giftCard = GiftCard::query()->where('qr_token', $token)->firstOrFail();
    $renderer = app(GiftCardImageRenderer::class);
    $frontendUrl = rtrim((string) config('app.frontend_url', config('app.url')), '/');
    $scanUrl = $frontendUrl.'/account/gift-cards/scan/'.urlencode($giftCard->qr_token);

    $png = $renderer->render(
        amount: (float) $giftCard->initial_amount,
        currency: (string) $giftCard->currency,
        code: (string) $giftCard->code,
        token: $scanUrl,
        theme: (string) data_get($giftCard->meta, 'theme', 'gold'),
    );

    return response($png, 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=86400',
        'Content-Disposition' => 'inline; filename="'.$giftCard->code.'.png"',
    ]);
})->where('token', '.*')->name('gift-card.image');
