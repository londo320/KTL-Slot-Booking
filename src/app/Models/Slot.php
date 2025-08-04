<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slot extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'depot_id',
        'start_at',
        'end_at',
        'capacity',
        'is_blocked',
    ];

protected $casts = [
    'start_at'     => 'datetime',
    'end_at'       => 'datetime',
    'start_time'   => 'datetime',
    'end_time'     => 'datetime',
    'locked_at'    => 'datetime',
    'released_at'  => 'datetime',
    'cut_off_time' => 'string', // or 'datetime:H:i' if needed
];

protected $dates = ['released_at', 'locked_at'];


    /**
     * A slot belongs to a depot
     */
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    /**
     * A slot can have many bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

public function allowed_customers()
{
    return $this->belongsToMany(
        Customer::class,
        'slot_customer',       // Pivot table
        'slot_id',             // Foreign key on pivot table for this model
        'customer_id'          // Foreign key on pivot table for related model
    );
}

}