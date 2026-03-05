<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $panel = Filament::getPanel('admin');
    $user = auth()->user();

    if ($user !== null && $user->canAccessPanel($panel)) {
        return redirect()->to($panel->getUrl());
    }

    return redirect()->to($panel->getLoginUrl());
});
