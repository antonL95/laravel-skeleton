<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Saloon\Http\Senders\GuzzleSender;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GuzzleSender::class, fn () => new GuzzleSender);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();
        Model::preventLazyLoading(!$this->app->isProduction());
        Model::shouldBeStrict(!$this->app->isProduction());
        Model::unguard();

        Date::use(CarbonImmutable::class);
        DB::prohibitDestructiveCommands($this->app->isProduction());
        Password::defaults(fn () => $this->app->isProduction() ? Password::min(8)->uncompromised() : null);
        URL::forceHttps();
    }
}
