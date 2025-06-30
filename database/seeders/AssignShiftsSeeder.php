<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class AssignShiftsSeeder extends Seeder
{
    public function run()
    {
        // Define available shifts
        $shiftTimes = ['6am-3pm', '8am-4pm', '8am-5pm', '8:30am-5:30pm', '10pm-6am', 'sick-leave', 'vacation-leave', 'day-off'];

        // Get all users from the database
        $users = User::all();

        // Get the current month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        foreach ($users as $user) {
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                Shift::create([
                    'user_id' => $user->id,
                    'date' => $date->copy(), // Ensure unique Carbon instance
                    'shift_time' => $shiftTimes[array_rand($shiftTimes)], // Assign random shift
                ]);
            }
        }

        echo "Shifts assigned for the entire month successfully.\n";
    }
}
