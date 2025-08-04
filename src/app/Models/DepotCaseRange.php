<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepotCaseRange extends Model
{
    use HasFactory;

    protected $fillable = ['depot_id','min_cases','max_cases','duration_minutes'];


protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
];


    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }
}
