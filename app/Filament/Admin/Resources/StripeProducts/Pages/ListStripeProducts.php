<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Pages;

use App\Filament\Admin\Actions\SyncStripeProducts;
use App\Filament\Admin\Resources\StripeProducts\StripeProductResource;
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
