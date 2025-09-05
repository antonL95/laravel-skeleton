<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use const JSON_THROW_ON_ERROR;

use App\Models\SocialProviderUser;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use RuntimeException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\RedirectResponse;

final readonly class SocialController
{
    public function redirect(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        return Socialite::driver($driver)->redirect();
    }

    public function callback(Request $request, string $driver): RedirectResponse
    {
        $this->dynamicallySetSocialProviderCredentials($driver);

        try {
            $socialiteUser = Socialite::driver($driver)->user();
            $providerUser = $this->findOrCreateProviderUser($socialiteUser, $driver);

            if ($providerUser instanceof RedirectResponse) {
                return $providerUser;
            }

            throw_unless($providerUser->user instanceof Authenticatable, new RuntimeException);

            Auth::login($providerUser->user);

            return redirect()->route('dashboard');
        } catch (Exception $e) {
            Integration::captureUnhandledException($e);

            return redirect()->route('login')->with('error', 'An error occurred during authentication. Please try again.');
        }
    }

    private function dynamicallySetSocialProviderCredentials(string $provider): void
    {
        if (app()->environment('production')) {
            Config::set('services.'.$provider.'.redirect', '/auth/'.$provider.'/callback');
        } else {
            Config::set('services.'.$provider.'.redirect', 'https://fwd.host/https://tensen.test/auth/'.$provider.'/callback');
        }
    }

    private function findOrCreateProviderUser(SocialUser $socialUser, string $driver): SocialProviderUser|RedirectResponse
    {
        $providerUser = SocialProviderUser::query()->where('provider_slug', $driver)
            ->where('provider_user_id', $socialUser->getId())
            ->first();

        if ($providerUser !== null) {
            return $providerUser;
        }

        $user = User::query()->where('email', $socialUser->getEmail())->first();

        if ($user !== null) {
            $existingProvider = $user->socialProviders()->first();
            if ($existingProvider) {
                Log::error('This email is already associated with another provider. Please login using that provider.');

                return redirect()->route('login')->with(
                    'error',
                    'This email is already associated with another provider. Please login using that provider.',
                );
            }
        }

        return DB::transaction(function () use ($socialUser, $driver, $user): SocialProviderUser {
            $user ??= $this->createUser($socialUser);
            event(new Verified($user));

            return $this->createSocialProviderUser($user, $socialUser, $driver);
        });
    }

    private function createUser(SocialUser $socialUser): User
    {
        return User::query()->create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'email_verified_at' => now(),
        ]);
    }

    private function createSocialProviderUser(User $user, SocialUser $socialUser, string $driver): SocialProviderUser
    {
        return $user->socialProviders()->create([
            'provider_slug' => $driver,
            'provider_user_id' => $socialUser->getId(),
            'nickname' => $socialUser->getNickname(),
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'avatar' => $socialUser->getAvatar(),
            'provider_data' => $socialUser instanceof SocialiteUser
                ? json_encode($socialUser->user, JSON_THROW_ON_ERROR)
                : null,
            'token' => $socialUser instanceof SocialiteUser
                ? $socialUser->token
                : '',
            'refresh_token' => $socialUser instanceof SocialiteUser
                ? $socialUser->refreshToken
                : null,
            'token_expires_at' => $socialUser instanceof SocialiteUser && $socialUser->expiresIn !== null
                ? now()->addSeconds($socialUser->expiresIn)
                : null,
        ]);
    }
}
