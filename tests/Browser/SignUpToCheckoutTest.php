<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\assertDatabaseHas;

test('user can register, verify email, access dashboard, logout, and login again', function (): void {
    Notification::fake();

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $pendingAwaitablePage = visit('/register');

    $pendingAwaitablePage->assertSee('Create an account')
        ->fill('name', $userData['name'])
        ->fill('email', $userData['email'])
        ->fill('password', $userData['password'])
        ->fill('password_confirmation', $userData['password_confirmation'])
        ->press('Create account');

    assertDatabaseHas(User::class, [
        'name' => $userData['name'],
        'email' => $userData['email'],
        'email_verified_at' => null,
    ]);

    Notification::assertSentTo(
        User::query()->where('email', $userData['email'])->first(),
        VerifyEmail::class
    );

    $user = User::query()->where('email', $userData['email'])->first();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1((string) $user->email)]
    );

    $pendingAwaitablePage->navigate($verificationUrl);

    $pendingAwaitablePage->assertSee('Dashboard')
        ->assertNoJavascriptErrors();

    expect($user->fresh()->email_verified_at)->not->toBeNull();

    $pendingAwaitablePage->press('[data-slot="dropdown-menu-trigger"]');
    $pendingAwaitablePage->press('[data-testid="logout"]');

    $pendingAwaitablePage->navigate('/login')
        ->assertSee('Log in to your account')
        ->fill('email', $userData['email'])
        ->fill('password', $userData['password'])
        ->press('Log in');

    $pendingAwaitablePage->assertSee('Dashboard')
        ->assertNoJavascriptErrors();
});
