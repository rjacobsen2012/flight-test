<?php

namespace App\Libraries;

use App\Contracts\Libraries\BatteryContract;
use App\Models\Battery;
use App\Models\Drone;

/**
 * Class BatteryLibrary
 * @package App\Libraries
 */
class BatteryLibrary implements BatteryContract
{
    /**
     * @var BatteryDetailsLibrary
     */
    private $batteryFrameLibrary;

    /**
     * BatteryLibrary constructor.
     * @param BatteryDetailsLibrary $batteryFrameLibrary
     */
    public function __construct(BatteryDetailsLibrary $batteryFrameLibrary)
    {
        $this->batteryFrameLibrary = $batteryFrameLibrary;
    }

    /**
     * @param Drone $drone
     * @return array
     */
    public function get(Drone $drone)
    {
        $batteries = [];

        $drone->batteries->each(function (Battery $battery) use (&$batteries) {
            list($batteryTemperatures, $batteryPercents) = $this->batteryFrameLibrary->get($battery);

            $batteries[] = [
                'battery_sn' => $battery->battery_sn,
                'battery_name' => $battery->battery_name,
                'battery_temperatures' => $batteryTemperatures,
                'battery_percents' => $batteryPercents,
            ];
        });

        return $batteries;
    }
}
