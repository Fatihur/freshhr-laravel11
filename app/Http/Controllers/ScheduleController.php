<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfWeek()->toDateString());
        $endDate = Carbon::parse($startDate)->addDays(13)->toDateString();

        $schedules = Schedule::with(['employee', 'shift'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');

        $shifts = Shift::all();
        $employees = Employee::where('status', 'active')->get();

        $status = Schedule::whereBetween('date', [$startDate, $endDate])
            ->first()?->status ?? 'draft';

        return view('schedule.index', compact('schedules', 'shifts', 'employees', 'startDate', 'endDate', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
        ]);

        Schedule::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'shift_id' => $request->shift_id,
                'status' => 'draft',
            ]
        );

        return back()->with('success', 'Jadwal berhasil disimpan.');
    }

    public function publish(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        Schedule::whereBetween('date', [$request->start_date, $request->end_date])
            ->update(['status' => 'applied']);

        return back()->with('success', 'Jadwal telah diterbitkan.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        
        return back()->with('success', 'Jadwal berhasil dihapus.');
    }
}
