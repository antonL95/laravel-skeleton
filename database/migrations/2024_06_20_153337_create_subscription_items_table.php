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
        Schema::create('subscription_items', function (Blueprint $blueprint): void {
            $blueprint->id();
            $blueprint->foreignId('subscription_id');
            $blueprint->string('stripe_id')->unique();
            $blueprint->string('stripe_product');
            $blueprint->string('stripe_price');
            $blueprint->integer('quantity')->nullable();
            $blueprint->timestamps();

            $blueprint->index(['subscription_id', 'stripe_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_items');
    }
};
