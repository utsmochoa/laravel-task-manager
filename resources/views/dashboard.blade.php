<x-app-layout>
    {{-- Encabezado de la página --}}
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Tasks</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                    Manage assignments, track status deadlines.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a class="inline-flex items-center gap-2 bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md px-4 py-2.5 rounded-lg shadow-sm hover:shadow transition-all duration-150"
                   href="{{ route('tasks.create') }}">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>New Task</span>
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Contenido principal --}}
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="flex items-center justify-between bg-surface-container-lowest shadow-sm rounded-lg p-4 mb-6 border-l-4 border-tertiary-fixed-dim transition-all duration-300" id="flash-message">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-tertiary-fixed/40 flex items-center justify-center text-tertiary-container">
                        <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    </div>
                    <div>
                        <p class="font-label-md text-label-md text-on-surface font-semibold">{{ session('success') }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant"></p>
                    </div>
                </div>
                <button class="p-1 rounded-md text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors" onclick="document.getElementById('flash-message').classList.add('hidden')" type="button">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        @endif

        {{-- Tabla de tareas --}}
        <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low font-label-sm text-label-sm text-on-surface-variant tracking-wider uppercase">
                            <th class="py-3.5 pl-6 pr-3" scope="col">Preview</th>
                            <th class="py-3.5 px-3" scope="col">Task Details</th>
                            <th class="py-3.5 px-3" scope="col">Status</th>
                            <th class="py-3.5 px-3" scope="col">Due Date</th>
                            <th class="py-3.5 pl-3 pr-6 text-right" scope="col"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md text-on-surface">
                        @forelse ($tasks as $task)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                {{-- Preview / Thumbnail --}}
                                <td class="py-4 pl-6 pr-3 whitespace-nowrap align-middle">
                                    @if ($task->image)
                                        <div class="relative w-12 h-12 rounded-lg overflow-hidden bg-surface-container shadow-sm flex-shrink-0">
                                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $task->image) }}" alt="{{ $task->title }}">
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center text-outline flex-shrink-0">
                                            <span class="material-symbols-outlined text-[24px]">image_not_supported</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- Detalle de Tarea --}}
                                <td class="py-4 px-3 align-middle">
                                    <div class="flex flex-col">
                                        <a class="font-title-sm text-title-sm text-on-surface hover:text-primary transition-colors font-medium"
                                           href="{{ route('tasks.show', $task) }}">
                                            {{ $task->title }}
                                        </a>
                                        @if ($task->description)
                                            <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1 mt-0.5">
                                                {{ $task->description }}
                                            </p>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status con Badges adaptados dinámicamente --}}
                                <td class="py-4 px-3 whitespace-nowrap align-middle">
                                    @php
                                        $statusClasses = match(strtolower($task->status)) {
                                            'completed' => 'bg-tertiary-fixed text-on-tertiary-fixed-variant',
                                            'in_progress' => 'bg-secondary-fixed text-on-secondary-fixed-variant',
                                            default => 'bg-surface-container text-on-surface-variant',
                                        };

                                        $dotClasses = match(strtolower($task->status)) {
                                            'completed' => 'bg-tertiary',
                                            'in_progress' => 'bg-secondary',
                                            default => 'bg-outline',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-label-xs text-label-xs {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dotClasses }}"></span>
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>

                                {{-- Fecha límite --}}
                                <td class="py-4 px-3 whitespace-nowrap align-middle">
                                    @if ($task->due_date)
                                        @php
                                            $isOverdue = $task->due_date->isPast() && strtolower($task->status) !== 'completed';
                                        @endphp
                                        <div class="flex items-center gap-1.5 font-label-sm text-label-sm {{ $isOverdue ? 'text-error' : 'text-on-surface' }}">
                                            <span class="material-symbols-outlined text-[16px] {{ $isOverdue ? 'text-error' : 'text-on-surface-variant' }}">
                                                {{ $isOverdue ? 'schedule' : 'calendar_today' }}
                                            </span>
                                            <span>
                                                {{ $task->due_date->format('M d, Y') }}
                                                @if($isOverdue) (Overdue) @endif
                                            </span>
                                        </div>
                                    @else
                                        <span class="font-body-sm text-body-sm text-outline italic">No due date</span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="py-4 pl-3 pr-6 whitespace-nowrap text-right align-middle">
                                    <a href="{{ route('tasks.show', $task) }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-container/10 text-primary hover:bg-primary-container hover:text-on-primary font-label-sm text-label-sm transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        <span>Details</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-on-surface-variant font-body-md">
                                    No tasks found. Create one to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if ($tasks->hasPages())
                <div class="px-6 py-4 bg-surface-container-lowest border-t border-surface-container-low">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>