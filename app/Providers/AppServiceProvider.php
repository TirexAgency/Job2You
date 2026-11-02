<?php

namespace App\Providers;

use App\Services\Sms\SmsProvider;
use App\Services\Sms\TirexSmsClient;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsProvider::class, TirexSmsClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'local') {
            URL::forceRootUrl(config('app.url'));
        }
    }
}
