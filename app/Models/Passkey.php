<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\PasskeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Passkeys\Passkey as BasePasskey;

/**
 * Application passkey model.
 *
 * Extends the laravel/passkeys model to expose a UUID for external
 * identification so credentials are referenced by UUID (not their
 * auto-increment id) in URLs and frontend props. Registered as the passkey
 * model via Passkeys::usePasskeyModel() in FortifyServiceProvider.
 *
 * @property string $uuid
 */
final class Passkey extends BasePasskey
{
    /** @use HasFactory<PasskeyFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'credential_id',
        'credential',
    ];

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    #[\Override]
    protected static function booted(): void
    {
        self::creating(function (Passkey $passkey): void {
            if (in_array($passkey->uuid, [null, ''], true)) {
                $passkey->uuid = (string) Str::uuid();
            }
        });
    }
}
