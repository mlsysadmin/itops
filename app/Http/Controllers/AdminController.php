<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\EodLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AdminController extends Controller
{

    public function index()
    {
        $missingLogs = User::whereNotIn('id', function ($query) {
            $query->select('user_id')
                ->from('eod_logs')
                ->whereDate('created_at', now()->subDay());
        })
            ->whereNotIn('position', ['ITOPS Head', 'Application Security Admin', 'Project Manager']) // <-- Exclude specific positions
            ->get();

        $today = Carbon::today();

        // Fetch leave counts
        $sickLeaves = Shift::whereDate('date', $today)->where('shift_time', 'sick-leave')->with('user')->get();
        $vacationLeaves = Shift::whereDate('date', $today)->where('shift_time', 'vacation-leave')->with('user')->get();
        $dayOffs = Shift::whereDate('date', $today)->where('shift_time', 'day-off')->with('user')->get();

        // Get team success rates
        $teamSuccessRates = $this->getTeamSuccessRates(); // Call the function

        return view('admin.dashboard', compact('missingLogs', 'sickLeaves', 'vacationLeaves', 'dayOffs', 'teamSuccessRates'));
    }


    public function showEods(Request $request)
    {
        $logs = EodLog::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.eod', compact('logs'));
    }

    public function getEodDetails($id)
    {
        $log = EodLog::with('user')->find($id);

        if (!$log) {
            return response()->json(['success' => false]);
        }

        return response()->json([
            'success' => true,
            'log' => $log
        ]);
    }

    public function showShifts(Request $request)
    {
        // Fetch all shifts with user details
        $shifts = Shift::with('user')->orderBy('date')->get();

        // Get unique positions from the users table
        $positions = User::select('position')->distinct()->pluck('position');

        return view('admin.shift', compact('shifts', 'positions'));
    }


    public function showUsers(Request $request)
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validated = $request->validate([
            'role' => 'required|string',
            'position' => 'required|string'
        ]);

        $user->update([
            'role' => $validated['role'],
            'position' => $validated['position']
        ]);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }


    function getTeamSuccessRates()
    {
        // Get first day of current month
        $startDate = Carbon::now()->startOfMonth();

        // Get end of yesterday to include full day up to 23:59:59
        $endDate = Carbon::yesterday()->endOfDay();

        // Get all users grouped by position, excluding specific roles
        $teams = User::select('position', DB::raw('COUNT(id) as team_size'))
            ->whereNotIn('position', ['ITOPS Head', 'Application Security Admin', 'Project Manager'])
            ->groupBy('position')
            ->get();

        $results = [];

        foreach ($teams as $team) {
            // Get all users in the current team
            $users = User::where('position', $team->position)->get();

            $totalAttendanceDays = 0;
            $actualLogs = 0;

            foreach ($users as $user) {
                // Get the user's shifts within the date range
                $shifts = Shift::where('user_id', $user->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotIn('shift_time', ['vacation-leave', 'sick-leave', 'day-off'])
                    ->get();

                // Count the days the user had a valid shift
                $attendanceDays = $shifts->count();
                $totalAttendanceDays += $attendanceDays;

                // Count the user's actual logs within the date range
                $userLogs = EodLog::where('user_id', $user->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                // Match logs to attendance days
                $actualLogs += $shifts->filter(function ($shift) use ($userLogs) {
                    return $userLogs->contains(function ($log) use ($shift) {
                        return Carbon::parse($log->created_at)->isSameDay(Carbon::parse($shift->date));
                    });
                })->count();
            }

            // Calculate success rate
            $successRate = $totalAttendanceDays > 0 ? ($actualLogs / $totalAttendanceDays) * 100 : 0;

            $results[$team->position] = [
                'team_size' => $team->team_size,
                'total_attendance_days' => $totalAttendanceDays,
                'actual_logs' => $actualLogs,
                'success_rate' => round($successRate, 2) . '%'
            ];
        }

        return $results;
    }


    public function addUser(Request $request)
    {
        $request->validate([
            'emails' => 'required|array|min:1',
            'emails.*' => 'required|email|unique:users,email',
            'role' => 'required|string|in:admin,user',
            'position' => 'required|string|max:255',
        ]);

        $users = [];
        foreach ($request->emails as $email) {
            $users[] = User::create([
                'email' => $email,
                'name' => null,
                'password' => null,
                'role' => $request->role,
                'position' => $request->position,
                'avatar' => null,
                'google_id' => null,
                'remember_token' => Str::random(10),
            ]);
        }

        return response()->json([
            'message' => count($users) > 1 ? 'Users added successfully!' : 'User added successfully!',
            'users' => collect($users)->map->only(['id', 'email', 'role', 'position']),
        ], 201);
    }


    public function eodreports()
    {
        return view('admin.eodreports');
    }

    public function getWeeklyLogs(Request $request)
    {
        $week = $request->query('week', 'This Week');
        $start = null;
        $end = null;

        if ($week === 'Last Week') {
            $start = Carbon::now()->startOfWeek()->subWeek()->startOfDay();
            $end = Carbon::now()->startOfWeek()->subDay()->startOfDay();
        } else {
            // Default to This Week
            $start = Carbon::now()->startOfWeek()->startOfDay();
            $yesterday = Carbon::yesterday()->startOfDay();
            $endOfWeek = Carbon::now()->endOfWeek()->startOfDay();
            $end = $yesterday->lt($endOfWeek) ? $yesterday : $endOfWeek;
        }

        $users = User::with([
            'shifts' => function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end]);
            },
            'eodLogs'
        ])->get();

        $results = $users->map(function ($user) use ($start, $end) {
            // Filter valid shift types (exclude leaves, dayoffs)
            $validShiftDates = $user->shifts
                ->filter(function ($shift) {
                    return !in_array(strtolower($shift->shift_time), ['vacation-leave', 'sick-leave', 'dayoff']);
                })
                ->pluck('date')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique();

            // Filter EOD logs within the date range
            $logDates = $user->eodLogs
                ->filter(function ($log) use ($start, $end) {
                    $logDate = Carbon::parse($log->created_at)->startOfDay();
                    return $logDate->between($start, $end);
                })
                ->map(fn($log) => Carbon::parse($log->created_at)->format('Y-m-d'))
                ->unique();

            $missedDates = $validShiftDates->diff($logDates)->values();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'missed_logs_count' => $missedDates->count(),
                'missed_dates' => $missedDates,
            ];
        })->filter(fn($user) => $user['missed_logs_count'] > 0)->values();

        return response()->json($results);
    }


    public function getMonthlyLogs(Request $request)
    {
        $month = (int) $request->query('month');
        $year = now()->year;

        if ($month < 1 || $month > 12) {
            return response()->json(['error' => 'Invalid month selected'], 400);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();

        // Important: We limit the endDate to either yesterday or end of month, whichever is earlier
        $endDate = $yesterday->lt($endOfMonth) ? $yesterday : $endOfMonth;

        $users = User::with([
            'shifts' => function ($query) use ($startDate, $endDate) {
                // Only load shifts within the valid date range
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'eodLogs'
        ])->get();

        $usersWithMissingLogs = $users->map(function ($user) use ($startDate, $endDate) {
            $actualShiftDates = $user->shifts
                ->filter(function ($shift) {
                    $type = strtolower($shift->shift_time);
                    return !in_array($type, ['sick-leave', 'vacation-leave', 'day-off']);
                })
                ->pluck('date')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique();

            $eodLogDates = $user->eodLogs
                ->filter(function ($log) use ($startDate, $endDate) {
                    $logDate = Carbon::parse($log->created_at)->startOfDay();
                    return $logDate->between($startDate, $endDate);
                })
                ->map(fn($log) => Carbon::parse($log->created_at)->format('Y-m-d'))
                ->unique();

            $missedDates = $actualShiftDates->diff($eodLogDates)->values();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'missed_logs_count' => $missedDates->count(),
                'missed_dates' => $missedDates,
            ];
        })->filter(fn($user) => $user['missed_logs_count'] > 0)->values();

        return response()->json($usersWithMissingLogs);
    }


    public function getYearlyLogs(Request $request)
    {
        $year = (int) $request->query('year');

        if ($year < 2024 || $year > now()->year) {
            return response()->json(['error' => 'Invalid year selected'], 400);
        }

        $startDate = Carbon::create($year, 1, 1)->startOfDay();
        $endDate = $year === now()->year
            ? Carbon::yesterday()->startOfDay() // Prevent future logs from being included
            : Carbon::create($year, 12, 31)->endOfDay();

        $users = User::with([
            'shifts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'eodLogs'
        ])->get();

        $results = $users->map(function ($user) use ($startDate, $endDate) {
            $validShiftDates = $user->shifts
                ->filter(function ($shift) {
                    return !in_array(strtolower($shift->shift_time), ['vacation-leave', 'sick-leave', 'dayoff']);
                })
                ->pluck('date')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique();

            $logDates = $user->eodLogs
                ->filter(function ($log) use ($startDate, $endDate) {
                    $logDate = Carbon::parse($log->created_at)->startOfDay();
                    return $logDate->between($startDate, $endDate);
                })
                ->map(fn($log) => Carbon::parse($log->created_at)->format('Y-m-d'))
                ->unique();

            $missedDates = $validShiftDates->diff($logDates)->values();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'missed_logs_count' => $missedDates->count(),
                'missed_dates' => $missedDates,
            ];
        })->filter(fn($user) => $user['missed_logs_count'] > 0)->values();

        return response()->json($results);
    }


    public function getDateRangeLogs(Request $request)
    {
        $startDate = Carbon::parse($request->query('start_date'))->startOfDay();
        $rawEndDate = Carbon::parse($request->query('end_date'))->endOfDay();

        if ($startDate > $rawEndDate) {
            return response()->json(['error' => 'Start date cannot be later than end date.'], 400);
        }

        // Prevent counting future shifts
        $yesterday = Carbon::yesterday()->endOfDay();
        $endDate = $rawEndDate->lt($yesterday) ? $rawEndDate : $yesterday;

        $users = User::with([
            'shifts' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            },
            'eodLogs'
        ])->get();

        $results = $users->map(function ($user) use ($startDate, $endDate) {
            $validShiftDates = $user->shifts
                ->filter(function ($shift) {
                    return !in_array(strtolower($shift->shift_time), ['vacation-leave', 'sick-leave', 'dayoff']);
                })
                ->pluck('date')
                ->map(fn($date) => Carbon::parse($date)->format('Y-m-d'))
                ->unique();

            $logDates = $user->eodLogs
                ->filter(function ($log) use ($startDate, $endDate) {
                    $logDate = Carbon::parse($log->created_at)->startOfDay();
                    return $logDate->between($startDate, $endDate);
                })
                ->map(fn($log) => Carbon::parse($log->created_at)->format('Y-m-d'))
                ->unique();

            $missedDates = $validShiftDates->diff($logDates)->values();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'missed_logs_count' => $missedDates->count(),
                'missed_dates' => $missedDates,
            ];
        })->filter(fn($user) => $user['missed_logs_count'] > 0)->values();

        return response()->json($results);
    }




}
