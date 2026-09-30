<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TaskController extends Controller
{
   
    
     public function index(Request $request): View
     {
         $user = $request->user();
 
         $tasks = $user->tasks()
             ->latest()
             ->paginate(10);
 
         return view('tasks.index', compact('tasks'));
     }

   
    public function create(): View
    {
        return view('tasks.create');
    }

   
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('tasks', 'public');
        }

        $request->user()->tasks()->create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea creada correctamente.');
    }

  
    public function edit(Task $task): View
    {
        $this->authorizeUserTask($task);

        return view('tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorizeUserTask($task);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
            $data['image'] = $request->file('image')->store('tasks', 'public');
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea actualizada correctamente.');
    }

   
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorizeUserTask($task);

        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Tarea eliminada correctamente.');
    }

    
    private function authorizeUserTask(Task $task): void
    {
        if ($task->user_id !== Auth::id()) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}