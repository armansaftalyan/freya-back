<?php

declare(strict_types=1);

use Filament\Facades\Filament;
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

    $result = Builder::create()
        ->data($decoded)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
        ->size(220)
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->build();

    return response($result->getString(), 200, [
        'Content-Type' => $result->getMimeType(),
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('token', '.*')->name('mail.qr');
