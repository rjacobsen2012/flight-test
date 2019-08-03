<?php

namespace App\Contracts\Services;

/**
 * Interface GeonamesContract
 * @package App\Contracts\Services
 */
interface GeonamesContract
{
    /**
     * @param $lat
     * @param $long
     * @return array
     */
    public function getAddress($lat, $long);

    /**
     * @param $lat
     * @param $long
     * @return array
     */
    public function getCountry($lat, $long);
}
