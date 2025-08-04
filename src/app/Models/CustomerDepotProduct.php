<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDepotProduct extends Model
{
    protected $table = 'customer_depot_product';

    protected $fillable = [
        'customer_id',
        'depot_id',
        'product_id',
        'min_cases',
        'max_cases',
        'override_duration_minutes',
    ];

    protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}