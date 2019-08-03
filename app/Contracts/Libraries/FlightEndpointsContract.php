<?php

namespace App\Contracts\Libraries;

use App\Models\Flight;

/**
 * Interface FlightEndpointsContract
 * @package App\Contracts\Libraries
 */
interface FlightEndpointsContract
{
    /**
     * @param Flight $flight
     * @return array
     */
    public function get(Flight $flight);
}
