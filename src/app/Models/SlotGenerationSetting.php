<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotGenerationSetting extends Model
{

    //use SoftDeletes;

    protected $fillable = [
        'depot_id',
        'start_time',
        'end_time',
        'interval_minutes',
        'slots_per_block',
        'default_capacity',
        'days_active',
    ];


    protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
    'start_time' => 'datetime:H:i',
    'end_time'   => 'datetime:H:i',
    ];


    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
}
