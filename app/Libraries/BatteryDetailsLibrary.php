<?php

namespace App\Libraries;

use App\Contracts\Libraries\BatteryDetailsContract;
use App\Models\Battery;
use App\Models\BatteryFrame;

/**
 * Class BatteryDetailsLibrary
 * @package App\Libraries
 */
class BatteryDetailsLibrary implements BatteryDetailsContract
{
    /**
     * @param Battery $battery
     * @return array
     */
    public function get(Battery $battery)
    {
        $batteryTemperatures = [];
        $batteryPercents = [];

        $battery->batteryFramesFirst->each(function (BatteryFrame $batteryFrame) use (
            &$batteryTemperatures,
            &$batteryPercents
        ) {
            $timestamp = strtotime($batteryFrame->timestamp->toDateTimeLocalString()) * 1000;
            $batteryTemperatures[] = $this->getTemperature($batteryFrame, $timestamp);
            $batteryPercents[] = $this->getPercent($batteryFrame, $timestamp);
        });

        return array($batteryTemperatures, $batteryPercents);
    }

    /**
     * @param BatteryFrame $batteryFrame
     * @param $timestamp
     * @return array
     */
    protected function getPercent(BatteryFrame $batteryFrame, $timestamp)
    {
        return [
            'timestamp' => $timestamp,
            'battery_percent' => $batteryFrame->battery_percent,
        ];
    }

    /**
     * @param BatteryFrame $batteryFrame
     * @param $timestamp
     * @return array
     */
    protected function getTemperature(BatteryFrame $batteryFrame, $timestamp)
    {
        return [
            'timestamp' => $timestamp,
            'battery_temperature' => $batteryFrame->battery_temperature,
        ];
    }
}
