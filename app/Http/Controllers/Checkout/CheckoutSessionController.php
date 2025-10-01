<?php

declare(strict_types=1);

namespace App\Http\Controllers\Checkout;

use App\Actions\FetchStripeProducts;
use App\Enums\CheckoutStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class CheckoutSessionController
{
    public function index(): InertiaResponse
    {
        return Inertia::render('checkout/index', [
            'pricing' => app(FetchStripeProducts::class)->handle(),
        ]);
    }

    public function store(Request $request, string $price_id): Response
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->subscribed()) {
            return Inertia::location(to_route('billing.portal'));
        }

        $user->checkouts()->create([
            'price_id' => $price_id,
            'status' => CheckoutStatus::UNPAID->value,
        ]);

        return Inertia::location(
            $user->newSubscription('default', $price_id)
                ->skipTrial()
                ->trialDays(0)
                ->allowPromotionCodes()
                ->checkout([
                    'locale' => app()->getLocale(),
                    'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
                ])->redirect(),
        );
    }
}
