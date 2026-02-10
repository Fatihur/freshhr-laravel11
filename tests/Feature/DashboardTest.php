<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_statistics(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();

        // Create attendance for today
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('weeklyData');
        $response->assertViewHas('recentLogs');
    }

    public function test_dashboard_shows_correct_present_count(): void
    {
        $user = User::factory()->create();

        // Create 3 present employees
        for ($i = 0; $i < 3; $i++) {
            $employee = Employee::factory()->create();
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
                'status' => 'present',
            ]);
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('stats', function ($stats) {
            return $stats['present'] === 3;
        });
    }

    public function test_dashboard_shows_correct_late_count(): void
    {
        $user = User::factory()->create();

        // Create 2 late employees
        for ($i = 0; $i < 2; $i++) {
            $employee = Employee::factory()->create();
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
                'status' => 'late',
            ]);
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('stats', function ($stats) {
            return $stats['late'] === 2;
        });
    }

    public function test_dashboard_shows_correct_on_leave_count(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();

        // Create approved leave request for today
        LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'approved',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('stats', function ($stats) {
            return $stats['on_leave'] === 1;
        });
    }

    public function test_dashboard_calculates_absent_correctly(): void
    {
        $user = User::factory()->create();

        // Create 5 active employees
        Employee::factory()->count(5)->create(['status' => 'active']);

        // Only 2 attendances
        for ($i = 0; $i < 2; $i++) {
            $employee = Employee::factory()->create(['status' => 'active']);
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'date' => now()->toDateString(),
                'status' => 'present',
            ]);
        }

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('stats', function ($stats) {
            // 7 active - 2 present = 5 absent
            return $stats['absent'] === 5;
        });
    }

    public function test_dashboard_shows_weekly_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('weeklyData');
        $response->assertViewHas('weeklyData', function ($weeklyData) {
            return count($weeklyData) === 5; // 5 days
        });
    }

    public function test_dashboard_shows_recent_logs(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();

        // Create attendance with time_in
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:30:00',
            'status' => 'present',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertViewHas('recentLogs');
    }
}
