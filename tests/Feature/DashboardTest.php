<?php

declare(strict_types=1);

use App\Models\User;

test('guests are redirected to the login page', function (): void {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function (): void {
    $this->actingAs($user = User::factory()->create());

    $this->get(route('dashboard'))->assertOk();
});
