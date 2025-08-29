<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SocialProviderUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SocialProviderUser extends Model
{
    /** @use HasFactory<SocialProviderUserFactory> */
    use HasFactory;

    protected $table = 'social_provider_user';

    protected $fillable = [
        'user_id',
        'provider_slug',
        'provider_user_id',
        'nickname',
        'name',
        'email',
        'avatar',
        'provider_data',
        'token',
        'refresh_token',
        'token_expires_at',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return string[]
     */
    public function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
        ];
    }
}
