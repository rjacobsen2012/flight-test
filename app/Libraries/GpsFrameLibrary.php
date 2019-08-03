<?php

namespace App\Libraries;

use App\Contracts\Libraries\GpsFrameContract;
use App\Models\Flight;
use App\Models\GpsFrame;
use App\Services\Geonames;

/**
 * Class GpsFrameLibrary
 * @package App\Libraries
 */
class GpsFrameLibrary implements GpsFrameContract
{
    /**
     * @var Geonames
     */
    private $geonames;

    /**
     * GpsFrameLibrary constructor.
     * @param Geonames $geonames
     */
    public function __construct(Geonames $geonames)
    {
        $this->geonames = $geonames;
    }

    /**
     * @param Flight $flight
     * @return array
     */
    public function getPointDurationCountry(Flight $flight)
    {
        /** @var GpsFrame $startFrame */
        $startFrame = $flight->gpsFramesFirst->first();

        /** @var GpsFrame $lastFrame */
        $lastFrame = $flight->gpsFramesLast->first();

        $countryOfFlight = $startFrame ? $this->geonames->getCountry($startFrame->lat, $startFrame->long) : null;

        $point = $startFrame ? ['lat' => $startFrame->lat, 'long' => $startFrame->long] : '';
        $duration = $startFrame && $lastFrame ? $startFrame->timestamp->diffInSeconds($lastFrame->timestamp) : '';

        return array($point, $duration, $countryOfFlight ?: 'Unknown');
    }
}
