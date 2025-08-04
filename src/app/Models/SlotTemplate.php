<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotTemplate extends Model
{

    //use SoftDeletes;

    protected $fillable = [
        'depot_id',
        'day_of_week',        // integer 0 (Sunday) to 6
        'start_time',
        'end_time',
        'duration_minutes',
        'capacity',
    ];

    protected $casts = [
        'day_of_week'      => 'integer',
        'duration_minutes' => 'integer',
        'capacity'         => 'integer',
    ];

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
}
