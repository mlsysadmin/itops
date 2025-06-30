<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignUserPositions extends Command
{
    protected $signature = 'users:assign-positions';
    protected $description = 'Assign positions to users who do not have one';

    public function handle()
    {
        $positions = [
            'Network Admin',
            'Database Admin',
            'IT Security Admin',
            'Application Security Admin',
            'Database Admin',
            'System Admin',
        ];

        $users = User::whereNull('position')->get();
        
        foreach ($users as $user) {
            $user->update(['position' => $positions[array_rand($positions)]]);
        }

        $this->info(count($users) . " users updated with positions.");
    }
}
