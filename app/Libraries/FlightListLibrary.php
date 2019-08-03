<?php

namespace App\Libraries;

use App\Contracts\Libraries\FlightListContract;
use App\Models\Flight;
use Illuminate\Support\Collection;

/**
 * Class FlightListLibrary
 * @package App\Libraries
 */
class FlightListLibrary implements FlightListContract
{
    /**
     * @var GpsFrameLibrary
     */
    private $gpsFrameLibrary;

    /**
     * @var DroneLibrary
     */
    private $droneLibrary;

    /**
     * FlightListLibrary constructor.
     * @param GpsFrameLibrary $gpsFrameLibrary
     * @param DroneLibrary $droneLibrary
     */
    public function __construct(GpsFrameLibrary $gpsFrameLibrary, DroneLibrary $droneLibrary)
    {
        $this->gpsFrameLibrary = $gpsFrameLibrary;
        $this->droneLibrary = $droneLibrary;
    }

    /**
     * @param $uuid
     * @return array
     */
    public function get($uuid)
    {
        /** @var Flight $flight */
        $flight = Flight::where('uuid', $uuid)->first();

        if (!$flight) {
            return ['error' => 'Flight uuid not found'];
        }

        $drone = $flight->drone;
        list($point, $duration, $countryOfFlight) = $this->gpsFrameLibrary->getPointDurationCountry($flight);
        $batteries = $this->droneLibrary->getBatteries($drone);

        return [
            'uuid' => $flight->uuid,
            'aircraft_name' => $drone->aircraft_name,
            'aircraft_sn' => $drone->aircraft_sn,
            'home_point' => $point,
            'flight_duration' => $duration,
            'batteries' => $batteries,
            'country' => $countryOfFlight
        ];
    }

    /**
     * @return Flight[]|Collection
     */
    public function getAll()
    {
        return Flight::all()->map(function (Flight $flight) {
            return $this->get($flight->uuid);
        })->toArray();
    }
}
