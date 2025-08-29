<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stripe_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stripe_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price_id')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('currency')->nullable();
            $table->json('features')->nullable();
            $table->boolean('recurring')->default(false);
            $table->boolean('active')->default(true);
            $table->json('metadata');
            $table->timestamps();

            $table->index(['stripe_id', 'price_id', 'price', 'currency']);
        });
    }
};
