<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Drone
 * @package App\Models
 *
 * @property int id
 * @property string uuid
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Drone drone
 * @property Collection|GpsFrame[] gpsFrames
 * @property Collection|GpsFrame[] gpsFramesFirst
 * @property Collection|GpsFrame[] gpsFramesLast
 */
class Flight extends Model
{
    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function drone()
    {
        return $this->belongsTo(Drone::class);
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
