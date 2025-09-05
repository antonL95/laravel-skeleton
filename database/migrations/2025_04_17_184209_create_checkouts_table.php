<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $blueprint): void {
            $blueprint->uuid('id')->primary();
            $blueprint->foreignId('user_id')->constrained('users');
            $blueprint->string('price_id');
            $blueprint->string('checkout_session_id')->nullable();
            $blueprint->string('status');
            $blueprint->timestamps();
        });
    }
};
