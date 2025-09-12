<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts;

use App\Filament\Admin\Resources\StripeProducts\Pages\CreateStripeProduct;
use App\Filament\Admin\Resources\StripeProducts\Pages\EditStripeProduct;
use App\Filament\Admin\Resources\StripeProducts\Pages\ListStripeProducts;
use App\Filament\Admin\Resources\StripeProducts\Schemas\StripeProductForm;
use App\Filament\Admin\Resources\StripeProducts\Tables\StripeProductsTable;
use App\Models\StripeProduct;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

final class StripeProductResource extends Resource
{
    protected static ?string $model = StripeProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;

    protected static string|null|\UnitEnum $navigationGroup = 'Stripe';

    #[\Override]
    public static function form(Schema $schema): Schema
    {
        return StripeProductForm::configure($schema);
    }

    #[\Override]
    public static function table(Table $table): Table
    {
        return StripeProductsTable::configure($table);
    }

    #[\Override]
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStripeProducts::route('/'),
            'create' => CreateStripeProduct::route('/create'),
            'edit' => EditStripeProduct::route('/{record}/edit'),
        ];
    }
}
