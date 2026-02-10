<?php

namespace Tests\Feature\Management;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_positions_list(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        Position::factory()->count(3)->create();

        $response = $this->actingAs($user)->get('/management/positions');

        $response->assertStatus(200);
        $response->assertViewIs('management.positions.index');
    }

    public function test_admin_can_create_position(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $department = Department::factory()->create();

        $response = $this->actingAs($user)->post('/management/positions', [
            'name' => 'Senior Developer',
            'department_id' => $department->id,
            'level' => 3,
            'description' => 'Responsible for development',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('positions', [
            'name' => 'Senior Developer',
            'level' => 3,
        ]);
    }

    public function test_position_level_must_be_between_1_and_10(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $department = Department::factory()->create();

        $response = $this->actingAs($user)->post('/management/positions', [
            'name' => 'Test Position',
            'department_id' => $department->id,
            'level' => 15, // Invalid
        ]);

        $response->assertSessionHasErrors('level');
    }

    public function test_admin_can_update_position(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $position = Position::factory()->create();

        $response = $this->actingAs($user)->put("/management/positions/{$position->id}", [
            'name' => 'Updated Position Name',
            'department_id' => $position->department_id,
            'level' => 5,
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();

        $position->refresh();
        $this->assertEquals('Updated Position Name', $position->name);
        $this->assertEquals(5, $position->level);
    }

    public function test_admin_can_delete_position_without_employees(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $position = Position::factory()->create();

        $response = $this->actingAs($user)->delete("/management/positions/{$position->id}");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('positions', ['id' => $position->id]);
    }

    public function test_admin_cannot_delete_position_with_employees(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $position = Position::factory()->create();
        Employee::factory()->create(['position_id' => $position->id]);

        $response = $this->actingAs($user)->delete("/management/positions/{$position->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('positions', ['id' => $position->id]);
    }

    public function test_position_shows_employee_count(): void
    {
        $user = User::factory()->create(['role' => 'super_admin']);
        $position = Position::factory()->create();
        Employee::factory()->count(3)->create(['position_id' => $position->id]);

        $response = $this->actingAs($user)->get('/management/positions');

        $response->assertViewHas('positions', function ($positions) {
            return $positions->first()->employees_count === 3;
        });
    }
}
