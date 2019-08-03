<?php

namespace App\Contracts\Libraries;

use App\Models\Drone;

/**
 * Interface DroneContract
 * @package App\Contracts\Libraries
 */
interface DroneContract
{
    /**
     * @param Drone $drone
     * @return array
     */
    public function getBatteries(Drone $drone);
}
