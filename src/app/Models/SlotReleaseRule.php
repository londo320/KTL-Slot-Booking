<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotReleaseRule extends Model
{
    protected $fillable = [
        'depot_id',
        'customer_id',
        'release_day',
        'release_time',
        'lock_cutoff_days',
        'lock_cutoff_time',
        'priority',
    ];

    public function depot()
    {
        return $this->belongsTo(Depot::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customers()
    {
    return $this->belongsToMany(Customer::class, 'slot_release_rule_customer');
    }


}