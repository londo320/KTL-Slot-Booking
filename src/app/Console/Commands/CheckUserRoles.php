<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CheckUserRoles extends Command
{
    protected $signature = 'users:check-roles {email?}';
    protected $description = 'Check and fix user roles';

    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("User with email {$email} not found");
                return;
            }
            $users = collect([$user]);
        } else {
            $users = User::with('roles')->get();
        }

        $this->info('Current user roles:');
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->join(', ') ?: 'No roles';
            $this->line("{$user->name} ({$user->email}): {$roles}");
        }

        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $this->error('Admin role does not exist!');
            if ($this->confirm('Create admin role?')) {
                Role::create(['name' => 'admin']);
                $this->info('Admin role created');
            }
        }

        // Offer to assign admin role
        if ($email && $this->confirm('Assign admin role to this user?')) {
            $user = User::where('email', $email)->first();
            $user->assignRole('admin');
            $this->info("Admin role assigned to {$user->name}");
        }
    }
}