<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class BatteryFrame
 * @package App\Models
 *
 * @property int id
 * @property Carbon timestamp
 * @property float battery_percent
 * @property float battery_temperature
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Battery battery
 *
 * @method static|Builder|QueryBuilder orderedFirst()
 * @method static|Builder|QueryBuilder orderedLast()
 */
class BatteryFrame extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'battery_percent' => 'float',
        'battery_temperature' => 'float',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'timestamp',
    ];

    /**
     * @return BelongsTo
     */
    public function battery()
    {
        return $this->belongsTo(Battery::class);
    }

    public function scopeOrderedFirst($query)
    {
        return $query->orderBy('timestamp', 'asc');
    }

    public function scopeOrderedLast($query)
    {
        return $query->orderBy('timestamp', 'desc');
    }
}
