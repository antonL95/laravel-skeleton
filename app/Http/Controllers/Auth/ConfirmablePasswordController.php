<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ConfirmablePasswordController
{
    public function show(): Response
    {
        return Inertia::render('auth/confirm-password');
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        throw_unless(Auth::guard('web')->validate([
            'email' => $user->email,
            'password' => $request->password,
        ]), ValidationException::withMessages([
            'password' => __('auth.password'),
        ]));

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
