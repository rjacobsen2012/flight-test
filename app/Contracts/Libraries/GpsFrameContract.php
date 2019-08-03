<?php

namespace App\Contracts\Libraries;

use App\Models\Flight;

/**
 * Interface GpsFrameContract
 * @package App\Contracts\Libraries
 */
interface GpsFrameContract
{
    /**
     * @param Flight $flight
     * @return array
     */
    public function getPointDurationCountry(Flight $flight);
}
