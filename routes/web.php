<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SinkingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\EodController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PDFExportController;


Route::get('/', function () {
    return view('welcome');
});

// Login Route
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Google Authentication Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/eod', [EodController::class, 'index'])->name('eod');
    Route::post('/eod/store', [EodController::class, 'store'])->name('eod.store');
    Route::put('/eod/update/{id}', [EodController::class, 'update'])->name('eod.update');

    Route::get('/schedule', [ShiftController::class, 'index'])->name('schedule');
    Route::get('/shifts', [ShiftController::class, 'fetchShifts'])->name('shifts.fetch');
    Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
    Route::post('/shifts/update', [ShiftController::class, 'update'])->name('shifts.update');
    Route::delete('/shifts/{date}/{userName}', [ShiftController::class, 'deleteShift']);
    // 👇 Add the session route here inside the auth group
    Route::post('/set-loading-screen-session', function (\Illuminate\Http\Request $request) {
        session(['loadingScreenShown' => true]); // Store session variable
        return response()->json(['status' => 'success']);
    })->name('set-loading-screen-session');

    Route::get('/sinking', [SinkingController::class, 'index'])->name('sinking.index');

    //PDF Export Routes
    Route::get('/export/mysql-status', [PDFExportController::class, 'mysqlStatus']);
    Route::get('/export/replication-status', [PDFExportController::class, 'mysqlReplicationStatus']);
    Route::get('/export/home-utilization', [PDFExportController::class, 'homeUtilization']);
    Route::get('/dbteam', [PDFExportController::class, 'viewDbTeam'])->name('dbteam');


});


Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/users', [AdminController::class, 'showUsers'])->name('admin.users');
    Route::post('/admin/users/create', [AdminController::class, 'addUser'])->name('user.users.create');
    Route::post('/admin/users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    Route::get('/admin/shifts', [AdminController::class, 'showShifts'])->name('admin.shifts');

    Route::get('/admin/eod', [AdminController::class, 'showEods'])->name('admin.eod');
    Route::get('/admin/eod/{id}', [AdminController::class, 'getEodDetails']);
    Route::get('/admin/eodreport', [AdminController::class, 'eodreports'])->name('admin.eodreports');

    Route::get('/get-weekly-logs', [AdminController::class, 'getWeeklyLogs']);
    Route::get('/get-monthly-logs', [AdminController::class, 'getMonthlyLogs']);
    Route::get('/get-yearly-logs', [AdminController::class, 'getYearlyLogs']);
    Route::get('/get-date-range-logs', [AdminController::class, 'getDateRangeLogs']);



    Route::post('/set-loading-screen-session', function (\Illuminate\Http\Request $request) {
        session(['loadingScreenShown' => true]); // Store session variable
        return response()->json(['status' => 'success']);
    })->name('set-loading-screen-session');

});


require __DIR__ . '/auth.php';
