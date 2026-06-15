<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passkeys\Passkeys;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passkeys', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->uuid()->unique()->after('id');
            $blueprint->foreignIdFor(Passkeys::userModel(), 'user_id')->constrained()->cascadeOnDelete();
            $blueprint->string('name');
            $blueprint->string('credential_id')->unique();
            $blueprint->json('credential');
            $blueprint->timestamp('last_used_at')->nullable();
            $blueprint->timestamps();

            $blueprint->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
