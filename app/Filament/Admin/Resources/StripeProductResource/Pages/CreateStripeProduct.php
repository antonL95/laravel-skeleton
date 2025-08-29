<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProductResource\Pages;

use App\Filament\Admin\Resources\StripeProductResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateStripeProduct extends CreateRecord
{
    protected static string $resource = StripeProductResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
