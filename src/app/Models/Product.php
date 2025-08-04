<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Depot;
use App\Models\CustomerDepotProduct;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'description',
        'default_case_count',
        'default_pallets',
    ];

    protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
];


    public function bookings()
    {
        return $this->belongsToMany(Booking::class)
                    ->withPivot(['po_reference', 'cases', 'pallets'])
                    ->withTimestamps();
    }

    public function depots()
    {
        return $this->belongsToMany(Depot::class)
                    ->withPivot(['expected_case_count', 'override_duration_minutes'])
                    ->withTimestamps();
    }

    public function customerDepots()
    {
        return $this->hasMany(CustomerDepotProduct::class);
    }
}