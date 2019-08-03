<?php

namespace App\Contracts\Libraries;

use App\Models\Flight;

/**
 * Interface FlightContract
 * @package App\Contracts\Libraries
 */
interface FlightContract
{
    /**
     * @param $uuid
     * @return Flight
     */
    public function getFlight($uuid);
}
