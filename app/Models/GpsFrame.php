<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * Class GpsFrame
 * @package App\Models
 *
 * @property int id
 * @property Carbon timestamp
 * @property float lat
 * @property float long
 * @property float alt
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property Drone drone
 *
 * @method static|Builder|QueryBuilder orderedFirst()
 * @method static|Builder|QueryBuilder orderedLast()
 */
class GpsFrame extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'lat' => 'float',
        'long' => 'float',
        'alt' => 'float',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'timestamp',
    ];

    /**
     * @return BelongsTo
     */
    public function drone()
    {
        return $this->belongsTo(Drone::class);
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
