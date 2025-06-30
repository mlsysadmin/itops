<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EodLog;
use App\Models\Shift;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // ✅ Check if the logged-in user submitted an EOD log today
        $hasSubmittedEodToday = EodLog::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->exists();

        // ✅ Users on leave today (Same position as logged-in user)
        $dayOffUsers = Shift::whereDate('date', $today)
            ->whereIn('shift_time', ['day-off', 'vacation-leave', 'sick-leave'])
            ->whereHas('user', function ($query) use ($user) {
                $query->where('position', $user->position); // ✅ Filter users with the same position
            })
            ->with('user:id,name,avatar') // ✅ Load user details
            ->get();


        // ✅ Users who did not submit EOD logs today (Same team/position as logged-in user)
        $usersWithoutEodToday = User::whereDoesntHave('eodLogs', function ($query) use ($today) {
            $query->whereDate('created_at', $today);
        })
            ->where('position', $user->position) // ✅ Filter by same position as logged-in user
            ->select('id', 'name', 'position', 'avatar')
            ->get();

        return view('dashboard', compact('dayOffUsers', 'usersWithoutEodToday', 'hasSubmittedEodToday'));
    }


}
