<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use NotificationChannels\WebPush\WebPushChannel;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('webpush', function () {
            // Gunakan service container agar dependency injection otomatis
            return app()->make(WebPushChannel::class);
        });
    }
}
