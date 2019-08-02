<?php

namespace App\Libraries;

use App\Models\Battery;
use App\Models\BatteryFrame;
use App\Models\Drone;
use App\Models\Flight as FlightModel;
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
        /**
         * @var FlightModel $flight
         * @var Drone $drone
         */
        list($flight, $drone) = $this->getFlight($request);
        $batteries = $this->addBatteries($request, $drone);
        $this->addFrames($request, $batteries, $flight);
    }

    /**
     * @param $uuid
     * @return array
     */
    public function show($uuid)
    {
        /** @var FlightModel $flight */
        $flight = FlightModel::where('uuid', $uuid)->first();

        if (!$flight) {
            return ['error' => 'Flight uuid not found'];
        }

        $drone = $flight->drone;
        list($point, $duration, $countryOfFlight) = $this->getPointDurationCountry($flight);
        $batteries = $this->getDroneBatteries($drone);

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
     * @param $uuid
     * @return array
     */
    public function showDetail($uuid)
    {
        /** @var FlightModel $flight */
        $flight = FlightModel::where('uuid', $uuid)->first();

        if (!$flight) {
            return ['error' => 'Flight uuid not found'];
        }

        list($flightEndpoints, $flightPath, $distance, $address) = $this->getFlightEndpoints($flight);

        return [
            'uuid' => $flight->uuid,
            'flight_endpoints' => $flightEndpoints,
            'battery_details' => $this->getBatteryDetails($flight->drone),
            'flight_path' => $flightPath,
            'distance' => $distance,
            'address' => $address,
        ];
    }

    /**
     * @return Collection
     */
    public function list()
    {
        return FlightModel::all()->map(function (FlightModel $flight) {
            return $this->show($flight->uuid);
        });
    }

    /**
     * @return Collection
     */
    public function listDetail()
    {
        return FlightModel::all()->map(function (FlightModel $flight) {
            return $this->showDetail($flight->uuid);
        });
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

    /**
     * @param FlightModel $flight
     * @return array
     */
    protected function getPointDurationCountry(FlightModel $flight): array
    {
        /** @var GpsFrame $startFrame */
        $startFrame = $flight->gpsFramesFirst->first();
        $point = $startFrame ? new Point([$startFrame->lat, $startFrame->long]) : null;

        /** @var GpsFrame $lastFrame */
        $lastFrame = $flight->gpsFramesLast->first();

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
                "http://api.geonames.org/countryCodeJSON?lat={$startFrame->lat}&lng={$startFrame->long}&username=rjacobsen");
            $responseData = json_decode($response->getBody()->getContents());
            $countryOfFlight = $responseData->countryName;
        } catch (GuzzleException $e) {
        }

        return $countryOfFlight;
    }

    /**
     * @param GpsFrame $startFrame
     * @return mixed
     */
    protected function getAddress(GpsFrame $startFrame)
    {
        $client = new Client();
        $address = null;

        try {
            $response = $client->request(
                'GET',
                "http://api.geonames.org/extendedFindNearby?lat={$startFrame->lat}&lng={$startFrame->long}&username=rjacobsen",
                [
                    'headers' => ['Accept' => 'application/json','Content-type' => 'application/json']
                ]);
            $responseData = json_decode($response->getBody()->getContents());
            $address = (array) $responseData->address;
        } catch (GuzzleException $e) {
        }

        return $address;
    }

    /**
     * @param FlightModel $flight
     * @return array
     */
    protected function getFlightEndpoints(FlightModel $flight): array
    {
        $flightEndpoints = [];

        $endpoints = new Collection();
        $distances = new Collection();

        $address = $this->getAddress($flight->gpsFramesFirst->first());

        $flight->gpsFramesFirst->each(function (GpsFrame $gpsFrame) use (&$flightEndpoints, &$endpoints, &$distances) {
            $point = new Point([$gpsFrame->long, $gpsFrame->lat]);
            $flightEndpoints[] = [
                'timestamp' => strtotime($gpsFrame->timestamp->toDateTimeLocalString()) * 1000,
                'type' => $point->getType(),
                'coordinates' => $point->getCoordinates(),
            ];
            $endpoints->push([$gpsFrame->long, $gpsFrame->lat]);
            $distances->push([
                'timestamp' => $gpsFrame->timestamp,
                'lat' => $gpsFrame->lat,
                'long' => $gpsFrame->long,
            ]);
        });

        return [$flightEndpoints, [
            'type' => 'LineString',
            'coordinates' => array_values($endpoints->unique()->toArray())
        ], $this->getDistance($distances), $address];
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

    /**
     * @param $lat1
     * @param $lon1
     * @param $lat2
     * @param $lon2
     * @return float|int
     */
    protected function distanceCalculator($lat1, $lon1, $lat2, $lon2)
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

    /**
     * @param Collection $distances
     * @return float|int
     */
    protected function getDistance(Collection $distances)
    {
        $distance = 0;

        foreach ($distances as $key => $data) {
            if ($key) {
                $distance += $this->distanceCalculator(
                    $distances[$key - 1]['lat'],
                    $distances[$key - 1]['long'],
                    $data['lat'],
                    $data['long']
                );
            }
        }
        return $distance;
    }
}
