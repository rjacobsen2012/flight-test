<?php

namespace App\Contracts\Libraries;

use App\Models\Battery;

/**
 * Interface BatteryDetailsContract
 * @package App\Contracts\Libraries
 */
interface BatteryDetailsContract
{
    /**
     * @param Battery $battery
     * @return array
     */
    public function get(Battery $battery);
}
