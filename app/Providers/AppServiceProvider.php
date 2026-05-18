<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;


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
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('auth-register', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return back()
                        ->withInput($request->except('password', 'password_confirmation'))
                        ->withErrors([
                            'email' => 'Muitas tentativas de cadastro. Aguarde 1 minuto e tente novamente.',
                        ])
                        ->setStatusCode(429, 'Too Many Requests')
                        ->withHeaders($headers);
                });
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $email = (string) $request->string('email');
            $key = mb_strtolower($email) . '|' . $request->ip();

            return Limit::perMinute(3)
                ->by($key)
                ->response(function (Request $request, array $headers) {
                    return back()
                        ->withInput($request->only('email'))
                        ->withErrors([
                            'email' => 'Muitas tentativas de recuperacao de senha. Aguarde 1 minuto e tente novamente.',
                        ])
                        ->setStatusCode(429, 'Too Many Requests')
                        ->withHeaders($headers);
                });
        });

    }
}
