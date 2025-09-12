<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\StripeProducts\Schemas;

use Filament\Schemas\Schema;

final class StripeProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
