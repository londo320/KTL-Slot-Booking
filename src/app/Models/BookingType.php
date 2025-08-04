<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingType extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'duration_minutes',
    ];

protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
];


    public function depots()
    {
        return $this->belongsToMany(Depot::class)
                    ->withPivot('duration_minutes')
                    ->withTimestamps();
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
}
