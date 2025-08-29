<?php

declare(strict_types=1);

namespace App\Enums;

enum FlashMessageAction: string
{
    case DELETE = 'delete';
    case UPDATE = 'update';
    case CREATE = 'create';
    case RENEW = 'renew';
}
