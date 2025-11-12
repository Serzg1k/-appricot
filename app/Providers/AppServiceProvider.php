<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Movies\Contracts\MovieAggregator as MovieAggregatorContract;
use App\Services\Movies\MovieAggregator;
use App\Domain\Movies\Contracts\MovieProvider;
use App\Infrastructure\Movies\Providers\FooMovieProvider;
use App\Infrastructure\Movies\Providers\BarMovieProvider;
use App\Infrastructure\Movies\Providers\BazMovieProvider;
use App\Domain\Movies\Decorators\ResilientCachedMovieProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MovieAggregatorContract::class, function ($app) {
            $retries = (int) config('movies.retries', 3);
            $sleep   = (int) config('movies.retry_sleep_ms', 200);
            $ttl     = (int) config('movies.cache_ttl', 300);

            $wrap = fn (MovieProvider $p)
            => new ResilientCachedMovieProvider($p, $retries, $sleep, $ttl);

            $providers = [
                $wrap($app->make(FooMovieProvider::class)),
                $wrap($app->make(BarMovieProvider::class)),
                $wrap($app->make(BazMovieProvider::class)),
            ];

            return new MovieAggregator($providers);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
