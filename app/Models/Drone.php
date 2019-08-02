<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Class Drone
 * @package App\Models
 *
 * @property int id
 * @property string uuid
 * @property string aircraft_name
 * @property string aircraft_sn
 * @property int battery_id
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Collection|Battery[] batteries
 * @property Collection|GpsFrame[] gpsFrames
 * @property Collection|GpsFrame[] gpsFramesFirst
 * @property Collection|GpsFrame[] gpsFramesLast
 */
class Drone extends Model
{
    protected $guarded = ['id'];

    /**
     * @return HasMany
     */
    public function batteries()
    {
        return $this->hasMany(Battery::class);
    }

    /**
     * @return HasMany
     */
    public function gpsFrames()
    {
        return $this->hasMany(GpsFrame::class);
    }

    /**
     * @return HasMany
     */
    public function gpsFramesFirst()
    {
        return $this->hasMany(GpsFrame::class)->orderedFirst();
    }

    /**
     * @return HasMany
     */
    public function gpsFramesLast()
    {
        return $this->hasMany(GpsFrame::class)->orderedLast();
    }
}
