<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depot extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'name',
    ];

protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
];





    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function bookingTypes()
    {
        return $this->belongsToMany(BookingType::class)
                    ->withPivot('duration_minutes')
                    ->withTimestamps();
    }

    public function slotGenerationSetting()
{
    return $this->hasOne(SlotGenerationSetting::class);
}

public function products()
{
    return $this->belongsToMany(Product::class)
        ->withPivot(['expected_case_count', 'override_duration_minutes'])
        ->withTimestamps();
}

    public function customerProducts()
    {
        return $this->hasMany(CustomerDepotProduct::class);
    }

    public function caseRanges()
    {
        return $this->hasMany(DepotCaseRange::class);
    }

        public function users()
    {
        return $this->belongsToMany(User::class, 'depot_user');
    }


}

