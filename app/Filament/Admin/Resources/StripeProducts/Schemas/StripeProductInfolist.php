<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Schemas;

use App\Models\StripeProduct;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Support\Number;

final class StripeProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('description'),
                TextEntry::make('price')
                    ->formatStateUsing(
                        fn (string $state, StripeProduct $stripeProduct): string => (string) Number::currency(
                            (float) $state / 100,
                            (string) $stripeProduct->currency,
                        ),
                    ),
            ]);
    }
}
