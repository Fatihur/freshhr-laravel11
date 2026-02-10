<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\DepartmentSchedule;
use App\Models\Employee;
use App\Models\OfficeSetting;
use App\Models\ScheduleDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private function createScheduleForEmployee($employeeId, $date = null): void
    {
        $date = $date ?? now()->toDateString();

        $schedule = DepartmentSchedule::factory()->create([
            'status' => DepartmentSchedule::STATUS_APPLIED,
        ]);

        ScheduleDetail::factory()->create([
            'department_schedule_id' => $schedule->id,
            'employee_id' => $employeeId,
            'date' => $date,
        ]);
    }

    public function test_user_can_view_attendance_page(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertViewIs('attendance.index');
    }

    public function test_user_can_check_in(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create([
            'is_active' => true,
            'work_start_time' => '09:00:00',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
        ]);

        $this->createScheduleForEmployee($employee->id);

        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'photo' => null,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'status_in' => Attendance::STATUS_VALID_ON_TIME,
        ]);
    }

    public function test_user_cannot_check_in_twice(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create([
            'is_active' => true,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
        ]);

        // First check-in
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:30:00',
        ]);

        // Try second check-in
        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_without_employee_cannot_check_in(): void
    {
        $user = User::factory()->create(['employee_id' => null]);
        OfficeSetting::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_check_in_after_work_start_time_is_marked_late(): void
    {
        // Set time to 10:00 AM (after 09:00 work start)
        $this->travelTo(now()->setTime(10, 0, 0));

        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create([
            'is_active' => true,
            'work_start_time' => '09:00:00',
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
        ]);

        $this->createScheduleForEmployee($employee->id);

        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'status_in' => Attendance::STATUS_VALID_LATE,
        ]);
    }

    public function test_user_can_check_out(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create([
            'is_active' => true,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
        ]);

        // Create check-in record
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'time_in' => '08:30:00',
            'time_out' => null,
        ]);

        $response = $this->actingAs($user)->post('/attendance/checkout', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
        ]);

        $attendance = Attendance::where('employee_id', $employee->id)->first();
        $this->assertNotNull($attendance->time_out);
    }

    public function test_user_cannot_check_out_without_check_in(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $response = $this->actingAs($user)->post('/attendance/checkout', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_check_in_requires_latitude_and_longitude(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => '',
            'longitude' => '',
        ]);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
    }

    public function test_attendance_stores_location_data(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        OfficeSetting::factory()->create([
            'is_active' => true,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
            'radius' => 100,
        ]);

        $this->createScheduleForEmployee($employee->id);

        $response = $this->actingAs($user)->post('/attendance', [
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'latitude' => -6.2088,
            'longitude' => 106.8456,
        ]);
    }
}
