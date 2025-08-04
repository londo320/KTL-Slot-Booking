<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CheckAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix admin user roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking admin user...');
        
        $user = User::where('email', 'admin@example.com')->first();
        
        if (!$user) {
            $this->error('Admin user not found!');
            return 1;
        }
        
        $this->info('Admin user found: ' . $user->name);
        $this->info('Email: ' . $user->email);
        
        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $this->error('Admin role not found!');
            return 1;
        }
        
        // Check if user has admin role
        if ($user->hasRole('admin')) {
            $this->info('✅ User has admin role');
        } else {
            $this->warn('❌ User does NOT have admin role - assigning...');
            $user->assignRole('admin');
            $this->info('✅ Admin role assigned');
        }
        
        $this->info('All roles: ' . $user->roles->pluck('name')->implode(', '));
        
        return 0;
    }
}
