<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;

class ShiftController extends Controller
{
    /**
     * Display the schedule page with employees.
     */
    public function index()
    {
        $user = Auth::user(); // Get logged-in manager
        $employees = User::where('position', $user->position)->get(); // Get team members

        return view('schedule', compact('employees')); // Pass employees to view
    }

    /**
     * Store a newly created shift in the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array|min:1', // Expecting multiple users
            'user_ids.*' => 'required|exists:users,id', // Validate each user ID
            'dates' => 'required|array|min:1', // Expecting multiple dates
            'dates.*' => 'required|date',
            'shift_time' => 'required|string',
        ]);

        foreach ($request->user_ids as $user_id) {
            foreach ($request->dates as $date) {
                Shift::updateOrCreate(
                    [
                        'user_id' => $user_id,
                        'date' => $date,
                    ],
                    [
                        'shift_time' => $request->shift_time
                    ]
                );
            }
        }

        return response()->json(['message' => 'Shifts assigned successfully']);
    }


    /**
     * Fetch shifts for a given month and year.
     */
    public function fetchShifts(Request $request)
    {
        try {
            $request->validate([
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
            ]);

            // Get the position of the currently logged-in user
            $userPosition = auth()->user()->position; // Assuming the position is stored in the 'position' column

            // Fetch shifts for users with the same position as the logged-in user
            $shifts = Shift::with('user') // Ensure this matches the function in the Shift model
                ->whereMonth('date', $request->month)
                ->whereYear('date', $request->year)
                ->whereHas('user', function ($query) use ($userPosition) {
                    // Filter users by the logged-in user's position
                    $query->where('position', $userPosition);
                })
                ->get();

            return response()->json($shifts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteShift($date, $userName)
    {
        // Find and delete the shift for the given date and user name
        $shift = Shift::where('date', $date)->whereHas('user', function ($query) use ($userName) {
            $query->where('name', $userName);
        })->first();

        if ($shift) {
            $shift->delete();
            return response()->json(['message' => 'Shift deleted successfully']);
        }

        return response()->json(['message' => 'Shift not found'], 404);
    }

    public function update(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|exists:users,name',
            'shift_time' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        Shift::updateOrCreate(
            ['user_id' => $user->id, 'date' => $request->date],
            ['shift_time' => $request->shift_time]
        );

        return response()->json(['message' => 'Shift updated successfully']);
    }


}
