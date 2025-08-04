<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ResetAdminRole extends Command
{
    protected $signature = 'reset:adminrole';
    protected $description = 'Reset admin role for user ID 1';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = User::find(1);
        
        if ($user) {
            $user->syncRoles();  // Remove all roles first
            $user->assignRole('admin');  // Reassign admin role
            $this->info('Admin role has been successfully reassigned to user ID 1.');
        } else {
            $this->error('User with ID 1 not found!');
        }
    }
}
