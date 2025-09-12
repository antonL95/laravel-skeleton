<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Pages;

use App\Filament\Admin\Resources\StripeProducts\StripeProductResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateStripeProduct extends CreateRecord
{
    protected static string $resource = StripeProductResource::class;
}
