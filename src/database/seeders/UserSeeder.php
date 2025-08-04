<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Depot;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure at least one depot exists
        $depot = Depot::first() ?? Depot::create([
            'name' => 'Main Depot',
            'location' => 'Default Location',
        ]);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => null,
        ]);
        $admin->assignRole('admin');

        $depotAdmin = User::create([
            'name' => 'Depot Admin',
            'email' => 'depotadmin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $depotAdmin->assignRole('depot-admin');

        $siteAdmin = User::create([
            'name' => 'Site Admin',
            'email' => 'siteadmin@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $siteAdmin->assignRole('site-admin');

        $customer = User::create([
            'name' => 'Customer One',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'depot_id' => $depot->id,
        ]);
        $customer->assignRole('customer');
    }
}
