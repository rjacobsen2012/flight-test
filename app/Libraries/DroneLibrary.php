<?php

namespace App\Libraries;

use App\Contracts\Libraries\DroneContract;
use App\Models\Battery;
use App\Models\Drone;

/**
 * Class DroneLibrary
 * @package App\Libraries
 */
class DroneLibrary implements DroneContract
{
    /**
     * @param Drone $drone
     * @return array
     */
    public function getBatteries(Drone $drone)
    {
        $batteries = [];
        $drone->batteries->each(function (Battery $battery) use (&$batteries) {
            array_push($batteries, [
                'battery_sn' => $battery->battery_sn,
                'battery_name' => $battery->battery_name
            ]);
        });

        return $batteries;
    }
}
