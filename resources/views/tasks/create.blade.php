{{-- filepath: resources/views/tasks/create.blade.php --}}
<x-app-layout>
    <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center gap-2 font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors group">
            <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
            <span>Back to Tasks</span>
        </a>

        <div class="space-y-2">
            <h1 class="font-headline-xl text-headline-xl tracking-tight text-on-surface">Create New Task</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
                Add a new task with details, status, due date, and image attachment.
            </p>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden">
            <div class="h-1.5 w-full bg-gradient-to-r from-primary to-secondary"></div>

            <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
                @csrf

                <x-task-form />

                <div class="pt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-surface hover:bg-surface-container text-on-surface font-label-md text-label-md transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-primary-container hover:bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:shadow-md transition-all">
                        <span class="material-symbols-outlined text-[18px]">add_circle</span>
                        <span>Create Task</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>