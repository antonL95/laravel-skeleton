<?php

declare(strict_types=1);

namespace App\Filament\Admin\Actions;

use App\Actions\SyncStripeProductsAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

final class SyncStripeProducts extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make('syncStripeProducts')
            ->label('Sync from Stripe')
            ->icon('heroicon-o-arrow-path')
            ->action(function () {
                app(SyncStripeProductsAction::class)->handle();

                Notification::make()
                    ->title('Stripe products synced successfully!')
                    ->success()
                    ->send();
            });
    }
}
