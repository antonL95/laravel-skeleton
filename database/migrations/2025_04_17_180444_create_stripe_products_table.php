<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_products', function (Blueprint $blueprint): void {
            $blueprint->uuid('id')->primary();
            $blueprint->string('stripe_id')->unique();
            $blueprint->string('name');
            $blueprint->text('description')->nullable();
            $blueprint->string('price_id')->nullable();
            $blueprint->unsignedBigInteger('price')->nullable();
            $blueprint->string('currency')->nullable();
            $blueprint->json('features')->nullable();
            $blueprint->boolean('recurring')->default(false);
            $blueprint->boolean('active')->default(true);
            $blueprint->json('metadata');
            $blueprint->timestamps();

            $blueprint->index(['stripe_id', 'price_id', 'price', 'currency']);
        });
    }
};
