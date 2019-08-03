<?php

namespace App\Contracts\Libraries;

use App\Models\Flight;
use Illuminate\Support\Collection;

/**
 * Interface FlightListContract
 * @package App\Contracts\Libraries
 */
interface FlightListContract
{
    /**
     * @param $uuid
     * @return array
     */
    public function get($uuid);

    /**
     * @return Flight[]|Collection
     */
    public function getAll();
}
