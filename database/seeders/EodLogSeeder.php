<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EodLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EodLogSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();

        // Define possible schedules
        $schedules = ['6am-3pm', '7am-4pm', '8am-5pm', '10pm-6am'];

        // Set start (first of the month) and end date (YESTERDAY)
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::yesterday();

        $logs = [];

        foreach ($users as $user) {
            $currentDate = clone $startDate;

            while ($currentDate->lte($endDate)) {
                $logs[] = [
                    'user_id' => $user->id,
                    'schedule' => $schedules[array_rand($schedules)],
                    'tasks' => 'Completed tasks for ' . $currentDate->toDateString(),
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ];

                $currentDate->addDay();
            }
        }

        // Debug: Check if logs exist before inserting
        if (empty($logs)) {
            echo "No logs to insert!\n";
            return;
        }

        // Insert in chunks for performance optimization
        DB::transaction(function () use ($logs) {
            foreach (array_chunk($logs, 1000) as $chunk) {
                EodLog::insert($chunk);
            }
        });

        echo "Logs inserted successfully!\n";
    }
}
