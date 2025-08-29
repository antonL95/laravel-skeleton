<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StripeProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class StripeProduct extends Model
{
    /** @use HasFactory<StripeProductFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'stripe_id',
        'name',
        'description',
        'price_id',
        'price',
        'currency',
        'metadata',
        'features',
        'recurring',
        'active',
    ];

    /**
     * @return array{
     *     metadata: 'array',
     *     features: 'array',
     *     recurring: 'boolean',
     *     active: 'boolean',
     * }
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'features' => 'array',
            'recurring' => 'boolean',
            'active' => 'boolean',
        ];
    }
}
