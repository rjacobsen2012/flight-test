<?php

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;

if (! function_exists('catch_and_return')) {
    /**
     * @param $message
     * @param Exception|GuzzleException $exception
     * @param bool $showStackTrace
     * @param bool $showTime
     * @return string
     */
    function catch_and_return($message, $exception, $showStackTrace = true, $showTime = true)
    {
        $time = Carbon::now()->toDateTimeString();
        $message = $showTime ? "{$time}: {$message}" : $message;

        if ($showStackTrace) {
            Log::critical($message . PHP_EOL .
                $exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        } else {
            Log::critical($message . PHP_EOL .
                $exception->getMessage());
        }

        return $message;
    }
}
