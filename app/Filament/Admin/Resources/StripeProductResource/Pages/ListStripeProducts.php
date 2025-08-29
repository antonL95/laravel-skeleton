<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProductResource\Pages;

use App\Filament\Admin\Actions\SyncStripeProducts;
use App\Filament\Admin\Resources\StripeProductResource;
use Filament\Resources\Pages\ListRecords;

final class ListStripeProducts extends ListRecords
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            SyncStripeProducts::make(),
        ];
    }
}
