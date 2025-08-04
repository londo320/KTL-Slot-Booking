<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{

    use SoftDeletes;
    
protected $fillable = [
    'slot_id',
    'booking_type_id',
    'user_id',
    'customer_id',
    'reference',
    'booking_reference',
    'notes',
    'actual_cases',
    'expected_cases',
    'case_variance',
    'expected_pallets',
    'actual_pallets',
    'pallet_variance',
    'container_size',
    'status',
    'end_time',
    'vehicle_registration',
    'container_number',
    'driver_name',
    'driver_phone',
    'carrier_company',
    'carrier_contact',
    'gate_number',
    'bay_number',
    'manifest_number',
    'load_type',
    'hazmat',
    'temperature_requirements',
    'estimated_arrival',
    'special_instructions',
];

  protected $casts = [
    'start_at'    => 'datetime',
    'end_at'      => 'datetime',
    'start_time'  => 'datetime',   // for bookings
    'end_time'    => 'datetime',   // for bookings
    'cut_off_time'=> 'string',     // or 'date:H:i' if you like
    'arrived_at' => 'datetime',
    'departed_at' => 'datetime',
    'estimated_arrival' => 'datetime',
    'hazmat' => 'boolean',
];

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function bookingType()
    {
        return $this->belongsTo(BookingType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['po_reference', 'cases', 'pallets'])
            ->withTimestamps();
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function scopeForUserDepots($query, $user = null)
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return $query; // Optionally throw or fail if unauthenticated
        }

        $depotIds = $user->depots()->pluck('depots.id')->toArray();

        return $query->whereHas('slot', fn($q) => $q->whereIn('depot_id', $depotIds));
    }

    /**
     * Generate a unique booking reference
     */
    public static function generateBookingReference(): string
    {
        do {
            // Format: WM-YYYYMMDD-XXXX (e.g., WM-20250802-A1B2)
            $reference = 'WM-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        } while (self::where('booking_reference', $reference)->exists());

        return $reference;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });
    }

}