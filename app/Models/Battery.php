<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Battery
 * @package App\Models
 *
 * @property int id
 * @property string battery_name
 * @property string battery_sn
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Drone drone
 * @property Collection|BatteryFrame[] batteryFrames
 * @property Collection|BatteryFrame[] batteryFramesFirst
 * @property Collection|BatteryFrame[] batteryFramesLast
 */
class Battery extends Model
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
    public function batteryFrames()
    {
        return $this->hasMany(BatteryFrame::class);
    }

    /**
     * @return HasMany
     */
    public function batteryFramesFirst()
    {
        return $this->hasMany(BatteryFrame::class)->orderedFirst();
    }

    /**
     * @return HasMany
     */
    public function batteryFramesLast()
    {
        return $this->hasMany(BatteryFrame::class)->orderedLast();
    }
}
