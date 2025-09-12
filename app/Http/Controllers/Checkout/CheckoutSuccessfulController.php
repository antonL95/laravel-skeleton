<?php

declare(strict_types=1);

namespace App\Http\Controllers\Checkout;

use App\Data\FlashData;
use App\Enums\CheckoutStatus;
use App\Enums\FlashMessageType;
use App\Events\FlashMessageEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

final readonly class CheckoutSuccessfulController
{
    public function __invoke(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var string $stringable */
        $stringable = $request->string('session_id', '');

        if ($stringable === '') {
            return to_route('checkout.index');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($stringable);

        if ($session->payment_status !== CheckoutStatus::PAID->value) {
            return to_route('checkout.index');
        }

        broadcast(
            new FlashMessageEvent(
                $user,
                new FlashData(
                    FlashMessageType::SUCCESS,
                    __('messages.toast.subscription_success.title'),
                    __('messages.toast.subscription_success.description'),
                ),
            ),
        );

        return to_route('dashboard');
    }
}
