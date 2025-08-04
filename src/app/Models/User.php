<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{

    use SoftDeletes;
    
    use HasFactory, Notifiable, HasRoles; // Add HasRoles trait

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'email',
        'password',
        'customer_id', // Make sure you have this in your migration
    ];

    // Relationship with depots
    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'depot_user');
    }

    // Relationship with customer (One-to-One) - Legacy for customer role
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with customers (Many-to-Many) - New for multiple customer assignment
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_user');
    }

public function depotIds()
{
    return $this->depots->pluck('id')->toArray();
}


public function getCustomerId(): ?int
{
    return $this->customer_id ?? null;
}

public function belongsToDepot(int $depotId): bool
{
    return $this->depots->pluck('id')->contains($depotId);
}

/**
 * Get all customer IDs this user has access to
 * - Customer role: Uses legacy customer_id field
 * - Admin/Site roles: Uses many-to-many customers, or all if none assigned
 */
public function getAccessibleCustomerIds(): array
{
    // If user has customer role, restrict to their assigned customer only
    if ($this->hasRole('customer')) {
        return $this->customer_id ? [$this->customer_id] : [];
    }

    // For admin/site roles, check if they have specific customers assigned
    $assignedCustomerIds = $this->customers()->pluck('customers.id')->toArray();
    
    // If no specific customers assigned, they can see all customers
    if (empty($assignedCustomerIds)) {
        return Customer::pluck('id')->toArray();
    }
    
    return $assignedCustomerIds;
}

/**
 * Check if user can access a specific customer
 */
public function canAccessCustomer(int $customerId): bool
{
    return in_array($customerId, $this->getAccessibleCustomerIds());
}

/**
 * Check if user can see all customers (admin/site roles with no specific assignment)
 */
public function canSeeAllCustomers(): bool
{
    // Customer role can never see all customers
    if ($this->hasRole('customer')) {
        return false;
    }
    
    // Admin/site roles can see all if no specific customers assigned
    return $this->customers()->count() === 0;
}



}
