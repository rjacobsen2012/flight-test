<?php

namespace App\Contracts\Libraries;

use App\Models\Drone;

/**
 * Interface BatteryContract
 * @package App\Contracts\Libraries
 */
interface BatteryContract
{
    /**
     * @param Drone $drone
     * @return array
     */
    public function get(Drone $drone);
}
