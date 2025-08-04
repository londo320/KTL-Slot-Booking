<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{

    use SoftDeletes;

    /**
     * Store multiple contact emails for this customer as JSON.
     */
    protected $casts = [
        'emails' => 'array',
    ];

    /**
     * Allow mass assignment for name and emails array
     */
    protected $fillable = [
        'name',
        'emails', // array of contact emails
    ];

    /**
     * A customer can have many user accounts (customer role)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'customer_id');
    }

    /**
     * Accessor for emails to ensure always an array
     */
    protected function emails(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ?? [],
            set: fn($value) => is_array($value) ? $value : []
        );
    }

    public function allowed_slots()
{
    return $this->belongsToMany(Slot::class, 'slot_customer');
}


}