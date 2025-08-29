<?php

declare(strict_types=1);

namespace App\Enums;

enum CheckoutStatus: string
{
    case PAID = 'paid';
    case UNPAID = 'unpaid';
    case NO_PAYMENT_REQUIRED = 'no_payment_required';
}
