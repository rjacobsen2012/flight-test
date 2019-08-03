<?php

namespace App\Contracts\Libraries;

/**
 * Interface FlightDetailContract
 * @package App\Contracts\Libraries
 */
interface FlightDetailContract
{
    /**
     * @param $uuid
     * @return array
     */
    public function get($uuid);

    /**
     * @return array
     */
    public function getAll();
}
