<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
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

    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data='.urlencode($decoded);
    $response = Http::timeout(10)->get($qrUrl);
    if (! $response->successful()) {
        abort(404);
    }

    return response($response->body(), 200, [
        'Content-Type' => 'image/png',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('token', '.*');
