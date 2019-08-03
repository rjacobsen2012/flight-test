<?php

namespace App\Libraries;

use App\Contracts\Libraries\FlightDataContract;
use App\Models\Battery;
use App\Models\Drone;
use App\Models\Flight as FlightModel;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class FlightDataLibrary
 * @package App\Libraries
 */
class FlightDataLibrary implements FlightDataContract
{
    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        /**
         * @var FlightModel $flight
         * @var Drone $drone
         */
        list($flight, $drone) = $this->getFlight($request);
        $batteries = $this->addBatteries($request, $drone);
        $this->addFrames($request, $batteries, $flight);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getFlight(Request $request)
    {
        /** @var FlightModel $flight */
        $flight = FlightModel::where('uuid', $request->json('uuid'))->first();
        $drone = $this->getDrone($request);

        if (!$flight) {
            $flight = $drone->flights()->create([
                'uuid' => $request->json('uuid'),
            ]);
        }

        return [$flight, $drone];
    }

    /**
     * @param Request $request
     * @return Drone
     */
    protected function getDrone(Request $request): Drone
    {
        /** @var Drone $drone */
        $drone = Drone::where('aircraft_sn', $request->json('aircraft_sn'))->first();

        if (!$drone) {
            $drone = Drone::create([
                'aircraft_name' => $request->json('aircraft_name'),
                'aircraft_sn' => $request->json('aircraft_sn')
            ]);
        }

        return $drone;
    }

    /**
     * @param Request $request
     * @param Drone $drone
     * @return array
     */
    protected function addBatteries(Request $request, Drone $drone): array
    {
        $batteries = [];

        foreach ($request->json('batteries') as $batteryData) {
            $batteries[$batteryData['battery_sn']] = $this->getBattery($drone, $batteryData);
        }
        return $batteries;
    }

    /**
     * @param Drone $drone
     * @param $batteryData
     * @return Battery
     */
    protected function getBattery(Drone $drone, $batteryData)
    {
        $battery = $drone->batteries->where('battery_sn', $batteryData['battery_sn'])->first();

        if (!$battery) {
            $battery = $drone->batteries()->create([
                'battery_sn' => $batteryData['battery_sn'],
                'battery_name' => $batteryData['battery_name'],
            ]);
        }

        return $battery;
    }

    /**
     * @param Request $request
     * @param array $batteries
     * @param FlightModel $flight
     */
    protected function addFrames(Request $request, array $batteries, FlightModel $flight): void
    {
        foreach ($request->json('frames') as $frame) {
            $seconds = $frame['timestamp'] / 1000;
            $timestamp = Carbon::parse(date('m/d/Y H:i:s', $seconds));
            switch ($frame['type']) {
                case 'battery':
                    $this->addBatteryFrame($frame, $batteries, $timestamp);
                    break;
                case 'gps':
                    $this->addGpsFrame($flight, $frame, $timestamp);
                    break;
            }
        }
    }

    /**
     * @param $frame
     * @param array $batteries
     * @param Carbon $timestamp
     */
    protected function addBatteryFrame($frame, array $batteries, Carbon $timestamp): void
    {
        if (array_key_exists($frame['battery_sn'], $batteries)) {
            $batteries[$frame['battery_sn']]->batteryFrames()->create([
                'timestamp' => $timestamp,
                'battery_percent' => $frame['battery_percent'],
                'battery_temperature' => $frame['battery_temperature'],
            ]);
        }
    }

    /**
     * @param FlightModel $flight
     * @param $frame
     * @param Carbon $timestamp
     */
    protected function addGpsFrame(FlightModel $flight, $frame, Carbon $timestamp): void
    {
        $flight->gpsFrames()->create([
            'timestamp' => $timestamp,
            'lat' => $frame['lat'],
            'long' => $frame['long'],
            'alt' => $frame['alt'],
        ]);
    }
}
