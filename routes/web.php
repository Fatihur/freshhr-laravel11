<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Management\EmployeeController;
use App\Http\Controllers\Management\PositionController;
use App\Http\Controllers\Management\UserController;
use App\Http\Controllers\Management\OfficeSettingController;
use App\Http\Controllers\Management\AuditLogController;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Redirect /home to dashboard (for compatibility)
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ===========================================
// ALL AUTHENTICATED USERS
// ===========================================
Route::middleware(['auth'])->group(function () {
    // Dashboard - All roles
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Attendance - All roles
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkout'])->name('attendance.checkout');

    // Leave Requests - All roles (view, create)
    Route::get('/leave', [LeaveRequestController::class, 'index'])->name('leave.index');
    Route::get('/leave/create', [LeaveRequestController::class, 'create'])->name('leave.create');
    Route::post('/leave', [LeaveRequestController::class, 'store'])->name('leave.store');
    Route::get('/leave/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave.show');

    // Schedule - All roles (view only)
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
});

// ===========================================
// MANAGER & ADMIN ONLY (dept_head, hr_admin, super_admin)
// ===========================================
Route::middleware(['auth', 'role:dept_head,hr_admin,super_admin'])->group(function () {
    // Approve/Reject Leave Requests
    Route::post('/leave/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave.approve');
    Route::post('/leave/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave.reject');

    // Schedule Management
    Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::post('/schedule/publish', [ScheduleController::class, 'publish'])->name('schedule.publish');
    Route::delete('/schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('schedule.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

// ===========================================
// HR & ADMIN ONLY (hr_admin, super_admin)
// ===========================================
Route::middleware(['auth', 'role:hr_admin,super_admin'])->prefix('management')->name('management.')->group(function () {
    // Employees Management
    Route::resource('employees', EmployeeController::class);

    // Positions Management
    Route::resource('positions', PositionController::class);
});

// ===========================================
// SUPER ADMIN ONLY
// ===========================================
Route::middleware(['auth', 'role:super_admin'])->prefix('management')->name('management.')->group(function () {
    // Users Management
    Route::resource('users', UserController::class);

    // Office Settings
    Route::get('/office', [OfficeSettingController::class, 'index'])->name('office.index');
    Route::put('/office', [OfficeSettingController::class, 'updateOffice'])->name('office.update');
    Route::post('/office/shifts', [OfficeSettingController::class, 'storeShift'])->name('office.shifts.store');
    Route::put('/office/shifts/{shift}', [OfficeSettingController::class, 'updateShift'])->name('office.shifts.update');
    Route::delete('/office/shifts/{shift}', [OfficeSettingController::class, 'destroyShift'])->name('office.shifts.destroy');

    // Audit Logs (Super Admin only)
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');
});
