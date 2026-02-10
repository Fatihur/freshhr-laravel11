<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::with('department')
            ->withCount('employees')
            ->orderBy('level')
            ->paginate(10);

        return view('management.positions.index', compact('positions'));
    }

    public function create()
    {
        $departments = Department::all();
        
        return view('management.positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        Position::create($validated);

        return redirect()->route('management.positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        $departments = Department::all();
        
        return view('management.positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        $position->update($validated);

        return redirect()->route('management.positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        if ($position->employees()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus jabatan yang masih memiliki karyawan.');
        }

        $position->delete();
        
        return redirect()->route('management.positions.index')
            ->with('success', 'Jabatan berhasil dihapus.');
    }
}
