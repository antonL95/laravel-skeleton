<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Pages;

use App\Filament\Admin\Resources\StripeProducts\StripeProductResource;
use Filament\Resources\Pages\ViewRecord;

final class ViewStripeProduct extends ViewRecord
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
