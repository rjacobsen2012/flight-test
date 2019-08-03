<?php

namespace App\Libraries;

use App\Contracts\Libraries\DistanceContract;
use Illuminate\Support\Collection;

/**
 * Class DistanceLibrary
 * @package App\Libraries
 */
class DistanceLibrary implements DistanceContract
{
    /**
     * @param Collection $distances
     * @return int
     */
    public function get(Collection $distances)
    {
        $distance = 0;

        foreach ($distances as $key => $data) {
            if ($key) {
                $this->calculate(
                    $distance,
                    $distances[$key - 1]['lat'],
                    $distances[$key - 1]['long'],
                    $data['lat'],
                    $data['long']
                );
            }
        }

        return $distance;
    }

    /**
     * @param $distance
     * @param $lat1
     * @param $long1
     * @param $lat2
     * @param $long2
     */
    protected function calculate(&$distance, $lat1, $long1, $lat2, $long2)
    {
        $distance += $this->calculator($lat1, $long1, $lat2, $long2);
    }

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|int
     */
    protected function calculator($lat1, $lon1, $lat2, $lon2)
    {
        if (($lat1 === $lat2) && ($lon1 === $lon2)) {
            return 0;
        }

        else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $kilometers = $miles * 1.609344;
            $meters = $kilometers * 1000;

            return $meters;
        }
    }
}
