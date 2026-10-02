<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_tasks(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_cannot_access_other_users_task(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $taskUserA = Task::factory()->create(['user_id' => $userA->id]);

        $response = $this->actingAs($userB)->get(route('tasks.show', $taskUserA));

        $response->assertStatus(403);
    }
}