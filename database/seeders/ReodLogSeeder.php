<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EodLog;
use App\Models\User;
use Illuminate\Support\Str;


class ReodLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Get all users
        $users = User::all();

        // If no users exist, create dummy users
        if ($users->isEmpty()) {
            \App\Models\User::factory(10)->create(); // Creates 10 users
            $users = User::all();
        }

        // Define possible schedules
        $schedules = ['6am-3pm', '7am-4pm', '8am-5pm', '10pm-6am'];

        // Insert multiple logs
        foreach ($users as $user) {
            EodLog::create([
                'user_id' => $user->id,
                'schedule' => $schedules[array_rand($schedules)], // Random schedule
                'tasks' => Str::random(50), // Generate random task description
                'created_at' => now()->subDays(rand(0, 30)), // Random date in last 30 days
                'updated_at' => now(),
            ]);
        }
    }
}
