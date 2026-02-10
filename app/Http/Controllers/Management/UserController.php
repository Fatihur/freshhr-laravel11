<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('employee')
            ->orderBy('name')
            ->paginate(10);

        return view('management.users.index', compact('users'));
    }

    public function create()
    {
        $employees = Employee::whereDoesntHave('user')->get();
        
        return view('management.users.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,hr_admin,dept_head,employee',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('management.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $employees = Employee::whereDoesntHave('user')
            ->orWhere('id', $user->employee_id)
            ->get();
        
        return view('management.users.edit', compact('user', 'employees'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:super_admin,hr_admin,dept_head,employee',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('management.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('management.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}
