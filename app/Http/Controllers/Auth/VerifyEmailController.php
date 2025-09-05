<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

final readonly class VerifyEmailController
{
    public function __invoke(EmailVerificationRequest $emailVerificationRequest): RedirectResponse
    {
        /** @var User $user */
        $user = $emailVerificationRequest->user();
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        $emailVerificationRequest->fulfill();

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
