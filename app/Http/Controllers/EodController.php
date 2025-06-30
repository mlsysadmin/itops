<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EodLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class EodController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userPosition = $user->position ?? null;
        $showTeamLogs = filter_var($request->query('team'), FILTER_VALIDATE_BOOLEAN);
        
        // Get the date 29 days ago
        $startDate = Carbon::now()->subDays(29);

        if ($showTeamLogs && !empty($userPosition)) {
            $teamUsers = User::where('position', $userPosition)->pluck('id')->toArray();
            $logs = EodLog::whereIn('user_id', $teamUsers)
                        ->where('created_at', '>=', $startDate) // Filter last 29 days
                        ->with('user')
                        ->latest()
                        ->paginate(10);
        } else {
            $logs = EodLog::where('user_id', $user->id)
                        ->where('created_at', '>=', $startDate) // Filter last 29 days
                        ->with('user')
                        ->latest()
                        ->paginate(10);
        }

        if ($request->ajax()) {
            return response()->json([
                'logs' => $logs->items(),
                'pagination' => (string) $logs->links() // Handle pagination links
            ]);
        }

        return view('eod', compact('logs'));
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $today = now()->toDateString(); // Get today's date (YYYY-MM-DD)

        // Check if the user already submitted EOD logs today
        $existingLog = EodLog::where('user_id', $userId)
                            ->whereDate('created_at', $today)
                            ->first();

        if ($existingLog) {
            return response()->json([
                'success' => false,
                'message' => 'You already submitted an EOD log today. You may edit your existing log instead.'
            ], 403); // 403 Forbidden
        }

        // Validate input
        $request->validate([
            'schedule' => 'required',
            'tasks' => 'required|string|min:3'
        ]);

        try {
            // Create new EOD log
            EodLog::create([
                'user_id' => $userId,
                'schedule' => $request->schedule,
                'tasks' => trim($request->tasks)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'End of day report submitted successfully!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit report. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'tasks' => 'required|string|min:3'
        ]);

        $log = EodLog::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or log not found!'
            ], 403);
        }

        $log->update([
            'tasks' => trim($request->tasks)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Log updated successfully!'
        ], 200);
    }


}
