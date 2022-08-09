<?php

namespace App\Providers;

use App\Channels\MailgunChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Send notifications through the Mailgun API
        Notification::extend('mailgun', function ($app) {
            return new MailgunChannel();
        });
    }
}
