<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $employees = Employee::with(['position', 'department'])
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('management.employees.index', compact('employees', 'search', 'status'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();
        
        return view('management.employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'required|exists:departments,id',
            'join_date' => 'required|date',
            'address' => 'nullable|string',
        ]);

        // Generate employee ID
        $lastEmployee = Employee::orderBy('id', 'desc')->first();
        $newId = $lastEmployee ? intval(substr($lastEmployee->employee_id, 4)) + 1 : 1;
        $validated['employee_id'] = 'EMP-' . str_pad($newId, 3, '0', STR_PAD_LEFT);

        Employee::create($validated);

        return redirect()->route('management.employees.index')
            ->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();
        
        return view('management.employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:active,on_leave,terminated',
            'address' => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('management.employees.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        
        return redirect()->route('management.employees.index')
            ->with('success', 'Karyawan berhasil dihapus.');
    }
}
