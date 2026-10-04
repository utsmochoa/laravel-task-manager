<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ningún test escribe en el disco real.
        Storage::fake('public');
    }

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Tarea de prueba',
            'description' => 'Descripción de prueba',
            'status' => 'pending',
            'due_date' => now()->addDays(3)->toDateString(),
        ], $overrides);
    }

    private function makeTask(User $user, array $overrides = []): Task
    {
        return Task::factory()->create(array_merge(['user_id' => $user->id], $overrides));
    }

    public function test_guests_are_redirected_to_login_on_every_task_route(): void
    {
        $task = $this->makeTask(User::factory()->create());

        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('tasks.create'))->assertRedirect(route('login'));
        $this->post(route('tasks.store'), $this->validData())->assertRedirect(route('login'));
        $this->get(route('tasks.show', $task))->assertRedirect(route('login'));
        $this->get(route('tasks.edit', $task))->assertRedirect(route('login'));
        $this->put(route('tasks.update', $task), $this->validData())->assertRedirect(route('login'));
        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('login'));
    }

    public function test_dashboard_only_lists_own_tasks(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->makeTask($user, ['title' => 'Mi tarea visible']);
        $this->makeTask($other, ['title' => 'Tarea ajena oculta']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mi tarea visible')
            ->assertDontSee('Tarea ajena oculta');
    }

    public function test_user_can_view_own_task(): void
    {
        $user = User::factory()->create();
        $task = $this->makeTask($user, ['title' => 'Detalle de mi tarea']);

        $this->actingAs($user)
            ->get(route('tasks.show', $task))
            ->assertOk()
            ->assertSee('Detalle de mi tarea');
    }

    public function test_user_can_create_task_and_sees_confirmation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('tasks.store'), $this->validData())
            ->assertOk()
            ->assertSee('Task successfully created.');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Tarea de prueba',
        ]);
    }

    public function test_creating_a_task_with_an_image_stores_the_file(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('tasks.store'), $this->validData([
                'image' => UploadedFile::fake()->image('photo.jpg'),
            ]))
            ->assertRedirect(route('dashboard'));

        $task = Task::firstWhere('title', 'Tarea de prueba');

        $this->assertNotNull($task->image);
        $this->assertTrue(Storage::disk('public')->exists($task->image));
    }

    public function test_due_date_cannot_be_in_the_past_when_creating(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('tasks.store'), $this->validData([
                'due_date' => now()->subDay()->toDateString(),
            ]))
            ->assertSessionHasErrors('due_date');

        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_user_can_update_own_task_and_sees_confirmation(): void
    {
        $user = User::factory()->create();
        $task = $this->makeTask($user, ['title' => 'Título viejo']);

        $this->actingAs($user)
            ->followingRedirects()
            ->put(route('tasks.update', $task), $this->validData([
                'title' => 'Título nuevo',
                'status' => 'in_progress',
            ]))
            ->assertOk()
            ->assertSee('Task successfully updated.');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Título nuevo',
            'status' => 'in_progress',
        ]);
    }

    public function test_an_overdue_task_can_still_be_edited(): void
    {
        $user = User::factory()->create();
        $pastDate = now()->subDays(5)->toDateString();
        $task = $this->makeTask($user, ['due_date' => $pastDate]);

        $this->actingAs($user)
            ->put(route('tasks.update', $task), $this->validData([
                'title' => 'Editada aunque vencida',
                'due_date' => $pastDate,
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Editada aunque vencida']);
    }

    public function test_user_can_delete_own_task_and_sees_confirmation(): void
    {
        $user = User::factory()->create();
        $task = $this->makeTask($user);

        $this->actingAs($user)
            ->followingRedirects()
            ->delete(route('tasks.destroy', $task))
            ->assertOk()
            ->assertSee('Task successfully deleted.');

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_replacing_the_image_deletes_the_previous_one(): void
    {
        $user = User::factory()->create();
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('tasks', 'public');
        $task = $this->makeTask($user, ['image' => $oldPath]);

        $this->actingAs($user)
            ->put(route('tasks.update', $task), $this->validData([
                'image' => UploadedFile::fake()->image('new.jpg'),
            ]))
            ->assertRedirect(route('dashboard'));

        $task->refresh();

        $this->assertNotSame($oldPath, $task->image);
        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($task->image));
    }

    public function test_removing_the_image_deletes_the_file_and_clears_the_column(): void
    {
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('photo.jpg')->store('tasks', 'public');
        $task = $this->makeTask($user, ['image' => $path]);

        $this->actingAs($user)
            ->put(route('tasks.update', $task), $this->validData(['remove_image' => '1']))
            ->assertRedirect(route('dashboard'));

        $this->assertNull($task->fresh()->image);
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_deleting_a_task_deletes_its_image_file(): void
    {
        $user = User::factory()->create();
        $path = UploadedFile::fake()->image('photo.jpg')->store('tasks', 'public');
        $task = $this->makeTask($user, ['image' => $path]);

        $this->assertTrue(Storage::disk('public')->exists($path));

        $this->actingAs($user)
            ->delete(route('tasks.destroy', $task))
            ->assertRedirect(route('dashboard'));

        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_user_cannot_view_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = $this->makeTask($owner);

        $this->actingAs($intruder)
            ->get(route('tasks.show', $task))
            ->assertForbidden();
    }

    public function test_user_cannot_edit_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = $this->makeTask($owner);

        $this->actingAs($intruder)
            ->get(route('tasks.edit', $task))
            ->assertForbidden();
    }

    public function test_user_cannot_update_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $task = $this->makeTask($owner, ['title' => 'Original']);

        $this->actingAs($intruder)
            ->put(route('tasks.update', $task), $this->validData(['title' => 'Hackeada']))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Original']);
    }

    public function test_user_cannot_delete_another_users_task(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $path = UploadedFile::fake()->image('photo.jpg')->store('tasks', 'public');
        $task = $this->makeTask($owner, ['image' => $path]);

        $this->actingAs($intruder)
            ->delete(route('tasks.destroy', $task))
            ->assertForbidden();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
        $this->assertTrue(Storage::disk('public')->exists($path));
    }
}
