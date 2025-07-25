<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // NOTE: This is the new email verification link generation logic.
        VerifyEmail::createUrlUsing(function (object $notifiable) {
            // 1. Generate the original, signed backend verification URL.
            $backendVerificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // 2. Create a new URL that points to your Next.js frontend.
            //    We will pass the backend verification URL as a query parameter.
            $frontendVerificationUrl = config('app.frontend_url') . '/verify-email?verify_url=' . urlencode($backendVerificationUrl);

            return $frontendVerificationUrl;
        });
    }
}
