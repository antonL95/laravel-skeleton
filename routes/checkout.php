<?php

declare(strict_types=1);

use App\Http\Controllers\Checkout\CheckoutCanceledController;
use App\Http\Controllers\Checkout\CheckoutSessionController;
use App\Http\Controllers\Checkout\CheckoutSuccessfulController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function (): void {
    Route::get(
        '/billing',
        static function (Request $request) {
            /** @var User|null $user */
            $user = $request->user();

            return $user?->redirectToBillingPortal(options: ['locale' => app()->getLocale()])
                ?? redirect()->route('login');
        },
    )->name('billing.portal');

    Route::get('/checkout', [CheckoutSessionController::class,'index'])->name('checkout.index');
    Route::get('/checkout-store/{price_id}', [CheckoutSessionController::class,'store'])->name('checkout.store');
    Route::get('/checkout/success', CheckoutSuccessfulController::class)->name('checkout.success');
    Route::get('/checkout/cancel', CheckoutCanceledController::class)->name('checkout.cancel');
});
