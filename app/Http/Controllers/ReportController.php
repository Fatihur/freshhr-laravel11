<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $filter = $request->get('filter', 'all');

        $query = Attendance::with(['employee.position', 'employee.department'])
            ->whereDate('date', $date);

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        $attendances = $query->orderBy('time_in', 'desc')->paginate(10);

        // Get all employees to show absent ones
        $allEmployees = Employee::where('status', 'active')->count();
        $presentEmployees = Attendance::whereDate('date', $date)
            ->whereIn('status', ['present', 'late'])
            ->count();

        return view('reports.index', compact('attendances', 'date', 'filter', 'allEmployees', 'presentEmployees'));
    }

    public function export(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $attendances = Attendance::with(['employee.position', 'employee.department'])
            ->whereDate('date', $date)
            ->get();

        $csvContent = "Nama,ID Karyawan,Jabatan,Departemen,Jam Masuk,Jam Pulang,Status,Lokasi\n";
        
        foreach ($attendances as $attendance) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s\n",
                $attendance->employee->name,
                $attendance->employee->employee_id,
                $attendance->employee->position->name ?? '-',
                $attendance->employee->department->name ?? '-',
                $attendance->time_in?->format('H:i') ?? '-',
                $attendance->time_out?->format('H:i') ?? '-',
                $attendance->status_label,
                $attendance->location ?? '-'
            );
        }

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="attendance_' . $date . '.csv"');
    }
}
