<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Get logged-in manager
        $employees = User::where('position', $user->position)->get(); // Get team members

        return view('schedule', compact('employees')); // Pass employees to view
    }

}
