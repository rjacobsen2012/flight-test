<?php

namespace App\Libraries;

use App\Contracts\Libraries\FlightDetailContract;
use App\Models\Flight;

/**
 * Class FlightDetailLibrary
 * @package App\Libraries
 */
class FlightDetailLibrary implements FlightDetailContract
{
    /**
     * @var BatteryLibrary
     */
    private $batteryLibrary;
    /**
     * @var FlightEndpointsLibrary
     */
    private $flightEndpointsLibrary;

    /**
     * FlightDetailLibrary constructor.
     * @param BatteryLibrary $batteryLibrary
     * @param FlightEndpointsLibrary $flightEndpointsLibrary
     */
    public function __construct(BatteryLibrary $batteryLibrary, FlightEndpointsLibrary $flightEndpointsLibrary)
    {
        $this->batteryLibrary = $batteryLibrary;
        $this->flightEndpointsLibrary = $flightEndpointsLibrary;
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

        list($flightEndpoints, $flightPath, $distance, $address) = $this->flightEndpointsLibrary->get($flight);

        return [
            'uuid' => $flight->uuid,
            'flight_endpoints' => $flightEndpoints,
            'battery_details' => $this->batteryLibrary->get($flight->drone),
            'flight_path' => $flightPath,
            'distance' => $distance,
            'address' => $address,
        ];
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return Flight::all()->map(function (Flight $flight) {
            return $this->get($flight->uuid);
        })->toArray();
    }
}
