<?php

declare(strict_types=1);

use App\Models\Passkey;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;

test('user model implements the passkey contract and trait', function (): void {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(PasskeyUser::class)
        ->and(class_uses_recursive($user))->toContain(PasskeyAuthenticatable::class)
        ->and($user->hasPasskeysEnabled())->toBeFalse();

    Passkey::factory()->for($user)->create();

    expect($user->refresh()->hasPasskeysEnabled())->toBeTrue();
});

test('a passkey is keyed by its uuid for route binding', function (): void {
    $passkey = Passkey::factory()->for(User::factory())->create();

    expect($passkey->getRouteKeyName())->toBe('uuid')
        ->and($passkey->uuid)->not->toBeNull()
        ->and($passkey->getRouteKey())->toBe($passkey->uuid);
});

test('passkey registration options require authentication', function (): void {
    $this->getJson('/user/passkeys/options')->assertUnauthorized();
});

test('passkey registration options require a confirmed password', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->getJson('/user/passkeys/options')->assertStatus(423);
});

test('passkey registration options are returned once the password is confirmed', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/user/confirm-password', ['password' => 'password']);

    $this->actingAs($user)->getJson('/user/passkeys/options')
        ->assertOk()
        ->assertJsonStructure(['options']);
});

test('a passkey cannot be deleted without a confirmed password', function (): void {
    $user = User::factory()->create();
    $passkey = Passkey::factory()->for($user)->create();

    $this->actingAs($user)->deleteJson('/user/passkeys/'.$passkey->uuid)->assertStatus(423);

    expect(Passkey::query()->whereKey($passkey->getKey())->exists())->toBeTrue();
});

test('a user can delete their own passkey', function (): void {
    $user = User::factory()->create();
    $passkey = Passkey::factory()->for($user)->create();

    $this->actingAs($user)->post('/user/confirm-password', ['password' => 'password']);

    $this->actingAs($user)->deleteJson('/user/passkeys/'.$passkey->uuid)->assertOk();

    expect(Passkey::query()->whereKey($passkey->getKey())->exists())->toBeFalse();
});

test('a user cannot delete another users passkey', function (): void {
    $owner = User::factory()->create();
    $passkey = Passkey::factory()->for($owner)->create();

    $attacker = User::factory()->create();
    $this->actingAs($attacker)->post('/user/confirm-password', ['password' => 'password']);

    $this->actingAs($attacker)->deleteJson('/user/passkeys/'.$passkey->uuid)->assertForbidden();

    expect(Passkey::query()->whereKey($passkey->getKey())->exists())->toBeTrue();
});

test('the security settings page exposes the users passkeys', function (): void {
    $user = User::factory()->create();
    $passkey = Passkey::factory()->for($user)->create(['name' => 'MacBook Pro']);

    $this->actingAs($user)->post('/user/confirm-password', ['password' => 'password']);

    $this->actingAs($user)->get('/settings/two-factor')
        ->assertOk()
        ->assertInertia(fn (Assert $assert): Assert => $assert
            ->component('settings/two-factor')
            ->has('passkeys', 1)
            ->where('passkeys.0.uuid', $passkey->uuid)
            ->where('passkeys.0.name', 'MacBook Pro'));
});
