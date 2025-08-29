<?php

declare(strict_types=1);

use App\Http\Controllers\CheckoutSessionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get(
        '/billing',
        static function (Request $request) {
            /** @var User|null $user */
            $user = $request->user();

            return $user?->redirectToBillingPortal(options: ['locale' => app()->getLocale()])
                ?? redirect()->route('login');
        },
    )->name('billing.portal');

    Route::get('/checkout', [CheckoutSessionController::class, 'show'])->name('checkout.show');
    Route::get('/checkout/store', [CheckoutSessionController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutSessionController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutSessionController::class, 'cancel'])->name('checkout.cancel');
});
