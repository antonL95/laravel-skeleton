<?php

declare(strict_types=1);

namespace App\Http\Controllers\Checkout;

use Illuminate\Http\RedirectResponse;

final readonly class CheckoutCanceledController
{
    public function __invoke(): RedirectResponse
    {
        return to_route('checkout.index');
    }
}
