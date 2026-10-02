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
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
   
    
    public function index(Request $request): View{
        $user = $request->user();
 
        $tasks = $user->tasks()
            ->latest()
            ->paginate(10);
        return view('dashboard', compact('tasks'));
    }

   
    public function create(): View{
        return view('tasks.create');
    }

    public function show(Task $task): View{
        Gate::authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

   
    public function store(StoreTaskRequest $request): RedirectResponse{
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('tasks', 'public');
        }

        $request->user()->tasks()->create($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Task successfully created.');
    }

  
    public function edit(Task $task): View{
        Gate::authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse{
        Gate::authorize('update', $task);
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($task->image) {
                Storage::disk('public')->delete($task->image);
            }
            $data['image'] = $request->file('image')->store('tasks', 'public');
        }

        $task->update($data);

        return redirect()->route('tasks.index')
            ->with('success', 'Tast successfully updated.');
    }

   
    public function destroy(Task $task): RedirectResponse
    {
        Gate::authorize('delete', $task);

        if ($task->image) {
            Storage::disk('public')->delete($task->image);
        }

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task successfully deleted.');
    }

    

}