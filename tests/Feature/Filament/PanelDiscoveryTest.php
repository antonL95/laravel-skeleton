<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Panel;
use Tests\Support\MemoizedPanel;

it('replays complete discovery after overlapping directory scans', function (string $panelId): void {
    $first = new MemoizedPanel()->id('overlapping');
    $first->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources');
    $first->discoverResources(in: app_path('Filament/Admin/Resources/Users'), for: 'App\\Filament\\Admin\\Resources\\Users');

    $plain = new Panel()->id($panelId);
    $plain->discoverResources(in: app_path('Filament/Admin/Resources/Users'), for: 'App\\Filament\\Admin\\Resources\\Users');

    expect($plain->getResources())->toContain(UserResource::class);

    foreach (range(1, 2) as $attempt) {
        $panel = new MemoizedPanel()->id($panelId);
        $panel->discoverResources(in: app_path('Filament/Admin/Resources/Users'), for: 'App\\Filament\\Admin\\Resources\\Users');

        expect($panel->getResources())->toBe($plain->getResources());
    }
})->with(['same panel id' => 'overlapping', 'different panel id' => 'narrow']);
