<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\CheckoutSessionController;
use App\Providers\Filament\AdminPanelProvider;

test('app')
    ->expect('App')
    ->toUseStrictTypes();

arch()->preset()->laravel()->ignoring([
    SocialController::class,
    CheckoutSessionController::class,
    AdminPanelProvider::class,
    'App\Http\Integrations',
]);

arch()->preset()->security();
