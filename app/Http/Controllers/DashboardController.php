<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get today's stats
        $today = now()->toDateString();
        
        $stats = [
            'present' => Attendance::whereDate('date', $today)->where('status', 'present')->count(),
            'late' => Attendance::whereDate('date', $today)->where('status', 'late')->count(),
            'on_leave' => LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
            'absent' => Employee::where('status', 'active')->count() - 
                Attendance::whereDate('date', $today)->whereIn('status', ['present', 'late'])->count() -
                LeaveRequest::where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)
                    ->whereDate('end_date', '>=', $today)
                    ->count(),
        ];

        // Weekly attendance data
        $weeklyData = [];
        $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum'];
        for ($i = 4; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $weeklyData[] = [
                'name' => $days[4 - $i] ?? now()->subDays($i)->format('D'),
                'count' => Attendance::whereDate('date', $date)->whereIn('status', ['present', 'late'])->count(),
            ];
        }

        // Recent logs
        $recentLogs = Attendance::with('employee')
            ->whereDate('date', $today)
            ->orderBy('time_in', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'weeklyData', 'recentLogs', 'user'));
    }
}
