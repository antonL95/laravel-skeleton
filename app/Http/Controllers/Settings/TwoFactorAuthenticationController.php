<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Data\PasskeyData;
use App\Http\Requests\Settings\TwoFactorAuthenticationRequest;
use App\Models\Passkey;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

final readonly class TwoFactorAuthenticationController implements HasMiddleware
{
    public static function middleware(): array
    {
        return Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            ? [new Middleware('password.confirm', only: ['show'])]
            : [];
    }

    public function show(TwoFactorAuthenticationRequest $twoFactorAuthenticationRequest): Response
    {
        $twoFactorAuthenticationRequest->ensureStateIsValid();

        $user = $twoFactorAuthenticationRequest->user();

        return Inertia::render('settings/two-factor', [
            'twoFactorEnabled' => $user?->hasEnabledTwoFactorAuthentication(),
            'requiresConfirmation' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'passkeysEnabled' => Features::enabled(Features::passkeys()),
            'hasPassword' => $user?->password !== null,
            'passkeys' => $user === null
                ? []
                : Passkey::query()
                    ->where('user_id', $user->getKey())
                    ->latest()
                    ->get()
                    ->map(fn (Passkey $passkey): PasskeyData => PasskeyData::build($passkey))
                    ->all(),
        ]);
    }
}
