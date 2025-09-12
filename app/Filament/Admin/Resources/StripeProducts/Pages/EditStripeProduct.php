<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Pages;

use App\Filament\Admin\Resources\StripeProducts\StripeProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

final class EditStripeProduct extends EditRecord
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
