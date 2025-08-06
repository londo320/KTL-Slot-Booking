<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Customer;
use App\Models\Depot;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateTestUsers extends Command
{
    protected $signature = 'make:test-users';
    protected $description = 'Create test users for different roles (development only)';

    public function handle()
    {
        if (app()->isProduction()) {
            $this->error('This command cannot be run in production!');
            return 1;
        }

        $this->info('Creating test users for role testing...');

        // Ensure roles exist
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'depot-admin']);
        Role::firstOrCreate(['name' => 'site-admin']);
        Role::firstOrCreate(['name' => 'customer']);

        // Create test users
        $testUsers = [
            [
                'name' => 'Test Depot Admin',
                'email' => 'depot-admin@test.com',
                'role' => 'depot-admin'
            ],
            [
                'name' => 'Test Site Admin',
                'email' => 'site-admin@test.com',
                'role' => 'site-admin'
            ],
            [
                'name' => 'Test Customer User',
                'email' => 'customer@test.com',
                'role' => 'customer'
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                ]
            );

            // Assign role
            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
                $this->info("✓ Created/Updated: {$user->name} with role '{$userData['role']}'");
            } else {
                $this->line("- Already exists: {$user->name} ({$userData['role']})");
            }

            // Assign to depot if not admin
            if ($userData['role'] !== 'admin' && $user->depots()->count() === 0) {
                $depot = Depot::first();
                if ($depot) {
                    $user->depots()->attach($depot->id);
                    $this->info("  ↳ Assigned to depot: {$depot->name}");
                }
            }

            // Assign to customer if customer role
            if ($userData['role'] === 'customer' && $user->customers()->count() === 0) {
                $customer = Customer::first();
                if ($customer) {
                    $user->customers()->attach($customer->id);
                    $this->info("  ↳ Assigned to customer: {$customer->name}");
                }
            }
        }

        $this->info('');
        $this->info('Test users created! You can now use the user switching dropdown in the admin panel.');
        $this->info('All test users have password: password');
        
        return 0;
    }
}