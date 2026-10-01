{{-- filepath: resources/views/tasks/edit.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 pb-16">

        {{-- Encabezado --}}
        <div class="mb-8">
            <a href="{{ route('tasks.show', $task) }}"
               class="inline-flex items-center gap-1.5 font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors group mb-3">
                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:-translate-x-1">arrow_back</span>
                <span>Back to task</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">Edit Task</h1>
                <span class="px-2.5 py-0.5 rounded-full font-label-xs text-label-xs bg-surface-container-high text-on-surface-variant font-semibold tracking-wider">#{{ $task->id }}</span>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Update task properties, deadline, and attached image.
            </p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-6 sm:p-10">
            <form method="POST" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <x-task-form :task="$task" />

                {{-- Acciones --}}
                <div class="pt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('tasks.show', $task) }}"
                       class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high font-label-md text-label-md text-on-surface-variant hover:text-on-surface text-center transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md font-semibold transition-all shadow-sm hover:shadow flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        <span>Update Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>