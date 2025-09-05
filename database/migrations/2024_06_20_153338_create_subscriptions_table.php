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
        Schema::create('subscriptions', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('user_id');
            $blueprint->string('type');
            $blueprint->string('stripe_id')->unique();
            $blueprint->string('stripe_status');
            $blueprint->string('stripe_price')->nullable();
            $blueprint->integer('quantity')->nullable();
            $blueprint->timestamp('trial_ends_at')->nullable();
            $blueprint->timestamp('ends_at')->nullable();
            $blueprint->timestamps();

            $blueprint->index(['user_id', 'stripe_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
