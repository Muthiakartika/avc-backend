<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NgrokCorsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $ngrokUrl = env('NGROK_URL');

        if ($ngrokUrl) {
            config([
                'cors.allowed_origins' => array_merge(
                    config('cors.allowed_origins', []),
                    [$ngrokUrl]
                ),
                'sanctum.stateful' => array_merge(
                    config('sanctum.stateful', []),
                    [parse_url($ngrokUrl, PHP_URL_HOST)]
                ),
            ]);
        }
    }
}
