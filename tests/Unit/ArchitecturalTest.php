<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\SocialController;
use App\Providers\Filament\AdminPanelProvider;

test('app')
    ->expect('App')
    ->toUseStrictTypes();

arch()->preset()->laravel()->ignoring([
    SocialController::class,
    AdminPanelProvider::class,
    'App\Http\Integrations',
]);

arch()->preset()->security();
