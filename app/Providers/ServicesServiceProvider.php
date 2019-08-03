<?php

namespace App\Providers;

use App\Contracts\Services\GeonamesContract;
use App\Services\Geonames;
use Illuminate\Support\ServiceProvider;

/**
 * Class ServicesServiceProvider
 * @package App\Providers
 */
class ServicesServiceProvider extends ServiceProvider
{
    public function register() {
        $this->app->bind(GeonamesContract::class, Geonames::class);
    }
}
