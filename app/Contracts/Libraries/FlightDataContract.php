<?php

namespace App\Contracts\Libraries;

use Illuminate\Http\Request;

/**
 * Interface FlightDataContract
 * @package App\Contracts\Libraries
 */
interface FlightDataContract
{
    /**
     * @param Request $request
     */
    public function save(Request $request);
}
