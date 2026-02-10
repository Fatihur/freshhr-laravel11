<?php

namespace Tests\Feature\Management;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_employees_list(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        Employee::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/management/employees');

        $response->assertStatus(200);
        $response->assertViewIs('management.employees.index');
    }

    public function test_admin_can_create_employee(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->actingAs($user)->post('/management/employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
            'address' => 'Jakarta',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_employee_id_is_generated_automatically(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $this->actingAs($user)->post('/management/employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'department_id' => $department->id,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
        ]);

        $employee = Employee::where('email', 'john@example.com')->first();
        $this->assertNotNull($employee->employee_id);
        $this->assertStringStartsWith('EMP-', $employee->employee_id);
    }

    public function test_admin_can_update_employee(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $employee = Employee::factory()->create();
        $newDepartment = Department::factory()->create();
        $newPosition = Position::factory()->create(['department_id' => $newDepartment->id]);

        $response = $this->actingAs($user)->put("/management/employees/{$employee->id}", [
            'name' => 'Updated Name',
            'email' => $employee->email,
            'department_id' => $newDepartment->id,
            'position_id' => $newPosition->id,
            'status' => 'on_leave',
            'join_date' => $employee->join_date,
        ]);

        $response->assertRedirect();

        $employee->refresh();
        $this->assertEquals('Updated Name', $employee->name);
        $this->assertEquals('on_leave', $employee->status);
    }

    public function test_admin_can_delete_employee(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $employee = Employee::factory()->create();

        $response = $this->actingAs($user)->delete("/management/employees/{$employee->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
    }

    public function test_employee_requires_valid_department(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $position = Position::factory()->create();

        $response = $this->actingAs($user)->post('/management/employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'department_id' => 9999, // Invalid
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('department_id');
    }

    public function test_employee_requires_valid_position(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $department = Department::factory()->create();

        $response = $this->actingAs($user)->post('/management/employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'department_id' => $department->id,
            'position_id' => 9999, // Invalid
            'join_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('position_id');
    }

    public function test_employee_email_must_be_unique(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $existingEmployee = Employee::factory()->create(['email' => 'john@example.com']);
        $department = Department::factory()->create();
        $position = Position::factory()->create(['department_id' => $department->id]);

        $response = $this->actingAs($user)->post('/management/employees', [
            'name' => 'Another John',
            'email' => 'john@example.com', // Duplicate
            'department_id' => $department->id,
            'position_id' => $position->id,
            'join_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_employee_search_filters_results(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        Employee::factory()->create(['name' => 'John Smith']);
        Employee::factory()->create(['name' => 'Jane Doe']);

        $response = $this->actingAs($user)->get('/management/employees?search=John');

        $response->assertViewHas('employees', function ($employees) {
            return $employees->count() === 1;
        });
    }

    public function test_employee_status_filter_works(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        Employee::factory()->create(['status' => 'active']);
        Employee::factory()->create(['status' => 'on_leave']);

        $response = $this->actingAs($user)->get('/management/employees?status=active');

        $response->assertViewHas('employees', function ($employees) {
            return $employees->count() === 1;
        });
    }
}
