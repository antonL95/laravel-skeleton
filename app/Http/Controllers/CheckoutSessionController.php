<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\FetchStripeProducts;
use App\Data\FlashData;
use App\Enums\CheckoutStatus;
use App\Enums\FlashMessageType;
use App\Events\FlashMessageEvent;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Cashier\Cashier;
use Symfony\Component\HttpFoundation\Response;

final readonly class CheckoutSessionController
{
    public function show(): InertiaResponse
    {
        return Inertia::render('checkout/index', [
            'pricing' => app(FetchStripeProducts::class)->handle(),
        ]);
    }

    public function store(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();
        $price = $request->string('price_id')->value();

        $user->checkouts()->create([
            'price_id' => $price,
            'status' => CheckoutStatus::UNPAID->value,
        ]);

        return Inertia::location(
            $user->newSubscription('default', $price)
                ->skipTrial()
                ->trialDays(0)
                ->allowPromotionCodes()
                ->checkout([
                    'locale' => app()->getLocale(),
                    'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout-cancel').'?session_id={CHECKOUT_SESSION_ID}',
                ])->redirect(),
        );
    }

    public function success(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var string $sessionId */
        $sessionId = $request->string('session_id', '');

        if ($sessionId === '') {
            return redirect(route('checkout.show'));
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        if ($session->payment_status !== CheckoutStatus::PAID->value) {
            return redirect(route('checkout.show'));
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

        return redirect(route('dashboard'));
    }

    public function cancel(): RedirectResponse
    {
        return redirect(route('checkout.show'));
    }
}
