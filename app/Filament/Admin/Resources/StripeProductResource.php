<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StripeProductResource\Pages\ListStripeProducts;
use App\Models\StripeProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Number;
use UnitEnum;

final class StripeProductResource extends Resource
{
    protected static ?string $model = StripeProduct::class;

    protected static ?string $slug = 'stripe-products';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|UnitEnum|null $navigationGroup = 'Stripe';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('stripe_id'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description'),
                TextColumn::make('price_id'),
                TextColumn::make('price')
                    ->formatStateUsing(fn (StripeProduct $stripeProduct) => Number::currency(
                        $stripeProduct->price / 100,
                        $stripeProduct->currency ?? Config::string('cashier.currency'),
                    )),
                TextColumn::make('metadata'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStripeProducts::route('/'),
        ];
    }
}
