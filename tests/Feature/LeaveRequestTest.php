<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaveRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_leave_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/leave');

        $response->assertStatus(200);
        $response->assertViewIs('leave.index');
    }

    public function test_user_can_create_leave_request(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $response = $this->actingAs($user)->post('/leave', [
            'type' => 'annual',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'reason' => 'Liburan keluarga',
            'handover_notes' => 'Serahkan ke tim',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('leave_requests', [
            'employee_id' => $employee->id,
            'type' => 'annual',
            'status' => 'pending',
        ]);
    }

    public function test_leave_request_requires_valid_type(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $response = $this->actingAs($user)->post('/leave', [
            'type' => 'invalid_type',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_leave_request_requires_start_date_before_end_date(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $response = $this->actingAs($user)->post('/leave', [
            'type' => 'annual',
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_leave_request_requires_start_date_after_today(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $response = $this->actingAs($user)->post('/leave', [
            'type' => 'annual',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
        ]);

        $response->assertSessionHasErrors('start_date');
    }

    public function test_user_can_view_leave_request_detail(): void
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
        ]);

        $response = $this->actingAs($user)->get("/leave/{$leaveRequest->id}");

        $response->assertStatus(200);
        $response->assertViewIs('leave.show');
        $response->assertViewHas('leaveRequest');
    }

    public function test_dept_head_can_approve_leave_request(): void
    {
        $deptHead = User::factory()->create(['role' => 'dept_head']);
        $employee = Employee::factory()->create();

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($deptHead)->post("/leave/{$leaveRequest->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $leaveRequest->refresh();
        $this->assertTrue($leaveRequest->approval_dept_head);
        $this->assertNotNull($leaveRequest->dept_head_approved_at);
    }

    public function test_hr_admin_can_approve_leave_request(): void
    {
        $hrAdmin = User::factory()->create(['role' => 'hr_admin']);
        $employee = Employee::factory()->create();

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($hrAdmin)->post("/leave/{$leaveRequest->id}/approve");

        $response->assertRedirect();

        $leaveRequest->refresh();
        $this->assertTrue($leaveRequest->approval_hrm);
        $this->assertNotNull($leaveRequest->hrm_approved_at);
    }

    public function test_super_admin_can_approve_and_finalize_leave_request(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $employee = Employee::factory()->create();

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($superAdmin)->post("/leave/{$leaveRequest->id}/approve");

        $response->assertRedirect();

        $leaveRequest->refresh();
        $this->assertTrue($leaveRequest->approval_gm);
        $this->assertNotNull($leaveRequest->gm_approved_at);
        $this->assertEquals('approved', $leaveRequest->status);
    }

    public function test_approver_can_reject_leave_request(): void
    {
        $hrAdmin = User::factory()->create(['role' => 'hr_admin']);
        $employee = Employee::factory()->create();

        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($hrAdmin)->post("/leave/{$leaveRequest->id}/reject");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $leaveRequest->refresh();
        $this->assertEquals('rejected', $leaveRequest->status);
    }

    public function test_leave_request_calculates_duration_correctly(): void
    {
        $leaveRequest = LeaveRequest::factory()->create([
            'start_date' => now(),
            'end_date' => now()->addDays(4),
        ]);

        // Duration should be 5 days (inclusive)
        $this->assertEquals(5, $leaveRequest->duration);
    }

    public function test_employee_only_sees_their_own_leave_requests(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        // Create leave request for this employee
        LeaveRequest::factory()->create(['employee_id' => $employee->id]);

        // Create leave request for another employee
        $otherEmployee = Employee::factory()->create();
        LeaveRequest::factory()->create(['employee_id' => $otherEmployee->id]);

        $response = $this->actingAs($user)->get('/leave');

        $response->assertViewHas('leaveRequests', function ($requests) {
            return $requests->count() === 1;
        });
    }
}
