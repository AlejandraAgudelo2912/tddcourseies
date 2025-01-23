<?php

namespace App\Providers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\TwitterClient;
use Illuminate\Support\ServiceProvider;

class TwitterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TwitterOAuth::class, function () {
            return new TwitterOAuth(
                (string)config('twitter.consumer_key'),
                (string)config('twitter.consumer_secret'),
                (string)config('twitter.access_token'),
                (string)config('twitter.access_token_secret')
            );
        });

        $this->app->bind('twitter', function () {
            return app(TwitterClient::class);
        });
    }

    public function boot(): void
    {
    }
}
