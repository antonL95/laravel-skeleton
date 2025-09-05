<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_provider_user', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id')->constrained()->onDelete('cascade');
            $blueprint->string('provider_slug');

            $blueprint->string('provider_user_id');
            $blueprint->string('nickname')->nullable();
            $blueprint->string('name')->nullable();
            $blueprint->string('email')->nullable();
            $blueprint->string('avatar')->nullable();
            $blueprint->text('provider_data')->nullable();

            $blueprint->string('token', 1024);
            $blueprint->string('refresh_token')->nullable();
            $blueprint->timestamp('token_expires_at')->nullable();
            $blueprint->timestamps();

            $blueprint->unique(['user_id', 'provider_slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_provider_user');
    }
};
