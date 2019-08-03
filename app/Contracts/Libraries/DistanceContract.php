<?php

namespace App\Contracts\Libraries;

use Illuminate\Support\Collection;

/**
 * Interface DistanceContract
 * @package App\Contracts\Libraries
 */
interface DistanceContract
{
    /**
     * @param Collection $distances
     * @return int
     */
    public function get(Collection $distances);
}
