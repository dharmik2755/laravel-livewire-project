<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaskFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task_and_it_appears_in_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test('tasks.task-manager')
            ->set('title', 'Fix login bug')
            ->set('description', 'Investigate 500 error')
            ->set('status', 'pending')
            ->call('createTask')
            ->assertSee('Fix login bug');

        $this->assertDatabaseHas('tasks', ['title' => 'Fix login bug', 'created_by' => $admin->id]);
    }

    public function test_manager_can_assign_task_to_developer_and_dispatches_job(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $manager = User::factory()->create(['role' => 'manager']);
        $developer = User::factory()->create(['role' => 'developer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $task = Task::create([
            'title' => 'Fix login bug',
            'status' => 'pending',
            'created_by' => $admin->id,
        ]);

        Livewire::actingAs($manager)
            ->test('tasks.assign-task')
            ->set("selectedDeveloper.{$task->id}", $developer->id)
            ->call('assign', $task->id)
            ->assertSee($developer->name);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'assigned_to' => $developer->id,
        ]);

        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ProcessTaskAssignmentJob::class);
    }

    public function test_developer_sees_only_their_assigned_tasks(): void
    {
        $developer = User::factory()->create(['role' => 'developer']);
        $otherDeveloper = User::factory()->create(['role' => 'developer']);
        $admin = User::factory()->create(['role' => 'admin']);

        Task::create([
            'title' => 'My task',
            'status' => 'pending',
            'created_by' => $admin->id,
            'assigned_to' => $developer->id,
        ]);

        Task::create([
            'title' => 'Not my task',
            'status' => 'pending',
            'created_by' => $admin->id,
            'assigned_to' => $otherDeveloper->id,
        ]);

        Livewire::actingAs($developer)
            ->test('tasks.developer-tasks')
            ->assertSee('My task')
            ->assertDontSee('Not my task');
    }

    public function test_login_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);

        Livewire::test('auth.login')
            ->set('email', $admin->email)
            ->set('password', 'password')
            ->call('login')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($admin);
    }
}
