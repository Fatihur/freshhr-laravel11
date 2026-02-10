<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use App\Models\User;
use App\Models\OfficeSetting;
use App\Models\Shift;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Departments
        $departments = [
            ['name' => 'Engineering', 'code' => 'ENG'],
            ['name' => 'Creative', 'code' => 'CRE'],
            ['name' => 'Human Resource', 'code' => 'HR'],
            ['name' => 'Marketing', 'code' => 'MKT'],
            ['name' => 'IT Department', 'code' => 'IT'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // Create Positions
        $positions = [
            ['name' => 'Senior Engineer', 'department_id' => 1, 'level' => 3],
            ['name' => 'Junior Engineer', 'department_id' => 1, 'level' => 1],
            ['name' => 'UI Designer', 'department_id' => 2, 'level' => 2],
            ['name' => 'HR Manager', 'department_id' => 3, 'level' => 4],
            ['name' => 'HR Staff', 'department_id' => 3, 'level' => 1],
            ['name' => 'Marketing Specialist', 'department_id' => 4, 'level' => 2],
            ['name' => 'System Admin', 'department_id' => 5, 'level' => 3],
        ];

        foreach ($positions as $pos) {
            Position::create($pos);
        }

        // Create Employees
        $employees = [
            ['employee_id' => 'EMP-001', 'name' => 'James Wilson', 'email' => 'james@freshhr.com', 'position_id' => 1, 'department_id' => 1, 'join_date' => '2022-01-12', 'status' => 'active'],
            ['employee_id' => 'EMP-002', 'name' => 'Maria Garcia', 'email' => 'maria@freshhr.com', 'position_id' => 3, 'department_id' => 2, 'join_date' => '2023-03-05', 'status' => 'active'],
            ['employee_id' => 'EMP-003', 'name' => 'Kevin Hart', 'email' => 'kevin@freshhr.com', 'position_id' => 4, 'department_id' => 3, 'join_date' => '2021-08-20', 'status' => 'on_leave'],
            ['employee_id' => 'EMP-004', 'name' => 'Sarah Connor', 'email' => 'sarah@freshhr.com', 'position_id' => 7, 'department_id' => 5, 'join_date' => '2023-11-15', 'status' => 'active'],
            ['employee_id' => 'EMP-005', 'name' => 'John Doe', 'email' => 'john@freshhr.com', 'position_id' => 2, 'department_id' => 1, 'join_date' => '2024-01-10', 'status' => 'active'],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // Create Users
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@freshhr.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'employee_id' => null,
        ]);

        User::create([
            'name' => 'Kevin Hart',
            'email' => 'hr@freshhr.com',
            'password' => Hash::make('password'),
            'role' => 'hr_admin',
            'employee_id' => 3,
        ]);

        User::create([
            'name' => 'James Wilson',
            'email' => 'depthead@freshhr.com',
            'password' => Hash::make('password'),
            'role' => 'dept_head',
            'employee_id' => 1,
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'employee@freshhr.com',
            'password' => Hash::make('password'),
            'role' => 'employee',
            'employee_id' => 5,
        ]);

        // Create Office Settings
        OfficeSetting::create([
            'name' => 'Kantor Pusat Jakarta',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 500,
            'tolerance' => 10,
            'work_start_time' => '09:00',
            'work_end_time' => '17:00',
            'is_active' => true,
        ]);

        // Create Shifts
        $shifts = [
            ['name' => 'Shift Pagi', 'start_time' => '09:00', 'end_time' => '17:00', 'status' => 'applied'],
            ['name' => 'Shift Siang', 'start_time' => '13:00', 'end_time' => '21:00', 'status' => 'applied'],
            ['name' => 'Shift Malam', 'start_time' => '21:00', 'end_time' => '05:00', 'status' => 'draft'],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }

        // Create sample Attendances for today
        $today = now()->toDateString();
        Attendance::create(['employee_id' => 1, 'date' => $today, 'time_in' => '08:55:00', 'status' => 'present', 'location' => 'Kantor Pusat']);
        Attendance::create(['employee_id' => 2, 'date' => $today, 'time_in' => '09:15:00', 'status' => 'late', 'location' => 'Remote']);
        Attendance::create(['employee_id' => 4, 'date' => $today, 'time_in' => '08:45:00', 'status' => 'present', 'location' => 'Kantor Pusat']);
        Attendance::create(['employee_id' => 5, 'date' => $today, 'time_in' => '09:05:00', 'status' => 'late', 'location' => 'Kantor Pusat']);
    }
}
