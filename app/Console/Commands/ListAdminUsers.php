<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListAdminUsers extends Command
{
    protected $signature = 'users:list-admin';
    protected $description = 'List all admin users';

    public function handle()
    {
        $this->info('Admin Users:');
        $this->info('===========');
        
        $users = User::with('role')->get();
        
        foreach ($users as $user) {
            $roleName = $user->role ? $user->role->name : 'No role';
            $isAdmin = $user->isAdmin() ? 'YES' : 'NO';
            
            $this->line("Email: {$user->email}");
            $this->line("Role: {$roleName}");
            $this->line("Is Admin: {$isAdmin}");
            $this->line("---");
        }
        
        return 0;
    }
} 