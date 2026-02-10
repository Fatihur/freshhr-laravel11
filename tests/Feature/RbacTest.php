<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficeSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        OfficeSetting::factory()->create(['is_active' => true]);
    }

    // ===========================================
    // SUPER ADMIN ACCESS TESTS
    // ===========================================

    public function test_super_admin_can_access_all_routes(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);

        // Dashboard
        $this->actingAs($user)->get('/dashboard')->assertOk();

        // Attendance
        $this->actingAs($user)->get('/attendance')->assertOk();

        // Management - Users (Super Admin only)
        $this->actingAs($user)->get('/management/users')->assertOk();

        // Management - Office Settings (Super Admin only)
        $this->actingAs($user)->get('/management/office')->assertOk();

        // Management - Employees (HR & Admin)
        $this->actingAs($user)->get('/management/employees')->assertOk();

        // Reports (Manager & above)
        $this->actingAs($user)->get('/reports')->assertOk();
    }

    // ===========================================
    // HR ADMIN ACCESS TESTS
    // ===========================================

    public function test_hr_admin_can_access_employee_and_position_management(): void
    {
        $user = User::factory()->create(['role' => 'hr_admin']);

        $this->actingAs($user)->get('/management/employees')->assertOk();
        $this->actingAs($user)->get('/management/positions')->assertOk();
    }

    public function test_hr_admin_cannot_access_user_management(): void
    {
        $user = User::factory()->create(['role' => 'hr_admin']);

        $this->actingAs($user)->get('/management/users')->assertForbidden();
    }

    public function test_hr_admin_cannot_access_office_settings(): void
    {
        $user = User::factory()->create(['role' => 'hr_admin']);

        $this->actingAs($user)->get('/management/office')->assertForbidden();
    }

    public function test_hr_admin_can_access_reports(): void
    {
        $user = User::factory()->create(['role' => 'hr_admin']);

        $this->actingAs($user)->get('/reports')->assertOk();
    }

    // ===========================================
    // DEPT HEAD ACCESS TESTS
    // ===========================================

    public function test_dept_head_can_access_reports(): void
    {
        $user = User::factory()->create(['role' => 'dept_head']);

        $this->actingAs($user)->get('/reports')->assertOk();
    }

    public function test_dept_head_cannot_access_management(): void
    {
        $user = User::factory()->create(['role' => 'dept_head']);

        $this->actingAs($user)->get('/management/employees')->assertForbidden();
        $this->actingAs($user)->get('/management/positions')->assertForbidden();
        $this->actingAs($user)->get('/management/users')->assertForbidden();
        $this->actingAs($user)->get('/management/office')->assertForbidden();
    }

    // ===========================================
    // EMPLOYEE ACCESS TESTS
    // ===========================================

    public function test_employee_can_access_basic_routes(): void
    {
        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create();
        $user->update(['employee_id' => $employee->id]);

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($user)->get('/attendance')->assertOk();
        $this->actingAs($user)->get('/leave')->assertOk();
        $this->actingAs($user)->get('/schedule')->assertOk();
    }

    public function test_employee_cannot_access_reports(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        $this->actingAs($user)->get('/reports')->assertForbidden();
    }

    public function test_employee_cannot_access_management(): void
    {
        $user = User::factory()->create(['role' => 'employee']);

        $this->actingAs($user)->get('/management/employees')->assertForbidden();
        $this->actingAs($user)->get('/management/positions')->assertForbidden();
        $this->actingAs($user)->get('/management/users')->assertForbidden();
        $this->actingAs($user)->get('/management/office')->assertForbidden();
    }

    // ===========================================
    // LEAVE APPROVAL PERMISSIONS
    // ===========================================

    public function test_manager_and_above_can_approve_leave(): void
    {
        $employee = Employee::factory()->create();
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending'
        ]);

        // Dept Head can approve
        $deptHead = User::factory()->create(['role' => 'dept_head']);
        $this->actingAs($deptHead)
            ->post("/leave/{$leaveRequest->id}/approve")
            ->assertRedirect();

        // Reset for next test
        $leaveRequest->update(['status' => 'pending', 'approval_dept_head' => false]);

        // HR Admin can approve
        $hrAdmin = User::factory()->create(['role' => 'hr_admin']);
        $this->actingAs($hrAdmin)
            ->post("/leave/{$leaveRequest->id}/approve")
            ->assertRedirect();

        // Reset for next test
        $leaveRequest->update(['status' => 'pending', 'approval_dept_head' => false]);

        // Super Admin can approve
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->actingAs($superAdmin)
            ->post("/leave/{$leaveRequest->id}/approve")
            ->assertRedirect();
    }

    public function test_employee_cannot_approve_leave(): void
    {
        $employee = Employee::factory()->create();
        $leaveRequest = LeaveRequest::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'pending'
        ]);

        $employeeUser = User::factory()->create(['role' => 'employee']);

        $this->actingAs($employeeUser)
            ->post("/leave/{$leaveRequest->id}/approve")
            ->assertForbidden();
    }

    // ===========================================
    // USER MODEL HELPER METHODS
    // ===========================================

    public function test_user_role_helper_methods(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->canManageUsers());
        $this->assertTrue($superAdmin->canManageOffice());
        $this->assertTrue($superAdmin->canManageEmployees());
        $this->assertTrue($superAdmin->canApproveLeave());

        $hrAdmin = User::factory()->create(['role' => 'hr_admin']);
        $this->assertTrue($hrAdmin->isHrAdmin());
        $this->assertFalse($hrAdmin->canManageUsers());
        $this->assertFalse($hrAdmin->canManageOffice());
        $this->assertTrue($hrAdmin->canManageEmployees());
        $this->assertTrue($hrAdmin->canApproveLeave());

        $deptHead = User::factory()->create(['role' => 'dept_head']);
        $this->assertTrue($deptHead->isDeptHead());
        $this->assertFalse($deptHead->canManageEmployees());
        $this->assertTrue($deptHead->canApproveLeave());

        $employee = User::factory()->create(['role' => 'employee']);
        $this->assertTrue($employee->isEmployee());
        $this->assertFalse($employee->canApproveLeave());
        $this->assertFalse($employee->canManageEmployees());
    }
}
