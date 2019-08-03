<?php

namespace App\Providers;

use App\Contracts\Libraries\BatteryContract;
use App\Contracts\Libraries\BatteryDetailsContract;
use App\Contracts\Libraries\DistanceContract;
use App\Contracts\Libraries\DroneContract;
use App\Contracts\Libraries\FlightDataContract;
use App\Contracts\Libraries\FlightDetailContract;
use App\Contracts\Libraries\FlightListContract;
use App\Contracts\Libraries\GpsFrameContract;
use App\Libraries\BatteryDetailsLibrary;
use App\Libraries\BatteryLibrary;
use App\Libraries\DistanceLibrary;
use App\Libraries\DroneLibrary;
use App\Libraries\FlightDataLibrary;
use App\Libraries\FlightDetailLibrary;
use App\Libraries\FlightListLibrary;
use App\Libraries\GpsFrameLibrary;
use Illuminate\Support\ServiceProvider;

/**
 * Class LibrariesServiceProvider
 * @package App\Providers
 */
class LibrariesServiceProvider extends ServiceProvider
{
    public function register() {
        $this->app->bind(BatteryContract::class, BatteryLibrary::class);
        $this->app->bind(BatteryDetailsContract::class, BatteryDetailsLibrary::class);
        $this->app->bind(DroneContract::class, DroneLibrary::class);
        $this->app->bind(FlightDataContract::class, FlightDataLibrary::class);
        $this->app->bind(FlightDetailContract::class, FlightDetailLibrary::class);
        $this->app->bind(FlightListContract::class, FlightListLibrary::class);
        $this->app->bind(GpsFrameContract::class, GpsFrameLibrary::class);
        $this->app->bind(DistanceContract::class, DistanceLibrary::class);
    }
}
