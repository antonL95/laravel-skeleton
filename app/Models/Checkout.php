<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CheckoutFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Checkout extends Model
{
    /** @use HasFactory<CheckoutFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'user_id',
        'price_id',
        'checkout_session_id',
        'status',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
