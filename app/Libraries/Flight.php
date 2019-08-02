<?php

namespace App\Libraries;

use App\Models\Battery;
use App\Models\BatteryFrame;
use App\Models\Drone;
use App\Models\GpsFrame;
use Carbon\Carbon;
use GeoJson\Geometry\Point;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Flight
{
    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $drone = $this->getDrone($request);
        $batteries = $this->addBatteries($request, $drone);
        $this->addFrames($request, $batteries, $drone);
    }

    /**
     * @param $uuid
     * @return array
     */
    public function show($uuid)
    {
        $drone = Drone::where('uuid', $uuid)->first();
        list($point, $duration, $countryOfFlight) = $this->getPointDurationCountry($drone);
        $batteries = $this->getDroneBatteries($drone);

        return [
            'uuid' => $drone->uuid,
            'home_point' => $point,
            'flight_duration' => $duration,
            'aircraft_name' => $drone->aircraft_name,
            'aircraft_sn' => $drone->aircraft_sn,
            'batteries' => $batteries,
            'country' => $countryOfFlight
        ];
    }

    /**
     * @param $uuid
     * @return array
     */
    public function showDetail($uuid)
    {
        /** @var Drone $drone */
        $drone = Drone::where('uuid', $uuid)->first();

        return [
            'uuid' => $drone->uuid,
            'endpoints' => $this->getFlightEndpoints($drone),
            'battery_details' => $this->getBatteryDetails($drone),
        ];
    }

    /**
     * @return Collection
     */
    public function list()
    {
        return Drone::all()->map(function (Drone $drone) {
            return $this->show($drone->uuid);
        });
    }

    /**
     * @return Collection
     */
    public function listDetail()
    {
        return Drone::all()->map(function (Drone $drone) {
            return $this->showDetail($drone->uuid);
        });
    }

    /**
     * @param Request $request
     * @return Drone
     */
    protected function getDrone(Request $request): Drone
    {
        /** @var Drone $drone */
        $drone = Drone::where('uuid', $request->json('uuid'))->first();

        if (!$drone) {
            $drone = Drone::create([
                'uuid' => $request->json('uuid'),
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
     * @param Drone $drone
     */
    protected function addFrames(Request $request, array $batteries, Drone $drone): void
    {
        foreach ($request->json('frames') as $frame) {
            $seconds = $frame['timestamp'] / 1000;
            $timestamp = Carbon::parse(date('m/d/Y H:i:s', $seconds));
            switch ($frame['type']) {
                case 'battery':
                    $this->addBatteryFrame($frame, $batteries, $timestamp);
                    break;
                case 'gps':
                    $this->addGpsFrame($drone, $frame, $timestamp);
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
     * @param Drone $drone
     * @param $frame
     * @param Carbon $timestamp
     */
    protected function addGpsFrame(Drone $drone, $frame, Carbon $timestamp): void
    {
        $drone->gpsFrames()->create([
            'timestamp' => $timestamp,
            'lat' => $frame['lat'],
            'long' => $frame['long'],
            'alt' => $frame['alt'],
        ]);
    }

    /**
     * @param Drone $drone
     * @return array
     */
    protected function getPointDurationCountry(Drone $drone): array
    {
        /** @var GpsFrame $startFrame */
        $startFrame = $drone->gpsFramesFirst->first();
        $point = $startFrame ? new Point([$startFrame->lat, $startFrame->long]) : null;

        /** @var GpsFrame $lastFrame */
        $lastFrame = $drone->gpsFramesLast->first();

        $countryOfFlight = $startFrame ? $this->getCountry($startFrame) : null;

        $point = $point ? ['type' => $point->getType(), 'coordinates' => $point->getCoordinates()] : '';
        $duration = $startFrame && $lastFrame ? $startFrame->timestamp->diffInSeconds($lastFrame->timestamp) : '';

        return array($point, $duration, $countryOfFlight ?: 'Unknown');
    }

    /**
     * @param Drone $drone
     * @return array
     */
    protected function getDroneBatteries(Drone $drone): array
    {
        $batteries = [];
        $drone->batteries->each(function (Battery $battery) use (&$batteries) {
            array_push($batteries, [
                'battery_sn' => $battery->battery_sn,
                'battery_name' => $battery->battery_name
            ]);
        });

        return $batteries;
    }

    /**
     * @param GpsFrame $startFrame
     * @return mixed
     */
    protected function getCountry(GpsFrame $startFrame)
    {
        $client = new Client();
        $countryOfFlight = null;

        try {
            $response = $client->request('GET',
                "http://api.geonames.org/countryCodeJSON?lat={$startFrame->lat}&lng={$startFrame->lat}&username=rjacobsen");
            $responseData = json_decode($response->getBody()->getContents());
            $countryOfFlight = $responseData->countryName;
        } catch (GuzzleException $e) {
        }

        return $countryOfFlight;
    }

    /**
     * @param Drone $drone
     * @return array
     */
    protected function getFlightEndpoints(Drone $drone): array
    {
        $flightEndpoints = [];

        $drone->gpsFramesFirst->each(function (GpsFrame $gpsFrame) use (&$flightEndpoints) {
            $point = new Point([$gpsFrame->lat, $gpsFrame->long]);
            $flightEndpoints[] = [
                'timestamp' => strtotime($gpsFrame->timestamp->toDateTimeLocalString()) * 1000,
                'type' => $point->getType(),
                'coordinates' => $point->getCoordinates(),
            ];
        });

        return $flightEndpoints;
    }

    /**
     * @param Drone $drone
     * @return array
     */
    protected function getBatteryDetails(Drone $drone): array
    {
        $batteries = [];

        $drone->batteries->each(function (Battery $battery) use (&$batteries) {
            list($batteryTemperatures, $batteryPercents) = $this->getBatteryPercentsAndTemperatures($battery);

            $batteries[] = [
                'battery_sn' => $battery->battery_sn,
                'battery_name' => $battery->battery_name,
                'battery_temperatures' => $batteryTemperatures,
                'battery_percents' => $batteryPercents,
            ];
        });

        return $batteries;
    }

    /**
     * @param Battery $battery
     * @return array
     */
    protected function getBatteryPercentsAndTemperatures(Battery $battery): array
    {
        $batteryTemperatures = [];
        $batteryPercents = [];

        $battery->batteryFramesFirst->each(function (BatteryFrame $batteryFrame) use (
            &$batteryTemperatures,
            &
            $batteryPercents
        ) {
            $batteryTemperatures[] = [
                'timestamp' => strtotime($batteryFrame->timestamp->toDateTimeLocalString()) * 1000,
                'battery_temperature' => $batteryFrame->battery_temperature,
            ];
            $batteryPercents[] = [
                'timestamp' => strtotime($batteryFrame->timestamp->toDateTimeLocalString()) * 1000,
                'battery_percent' => $batteryFrame->battery_percent,
            ];
        });

        return array($batteryTemperatures, $batteryPercents);
    }
}
