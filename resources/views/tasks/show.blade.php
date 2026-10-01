{{-- filepath: resources/views/tasks/show.blade.php --}}
<x-app-layout>
    @php
        $status = strtolower($task->status);
        $isCompleted = $status === 'completed';
        $isOverdue = $task->due_date && $task->due_date->isPast() && ! $isCompleted;

        $dotClass = match ($status) {
            'completed' => 'bg-tertiary',
            'in_progress' => 'bg-secondary animate-pulse',
            default => 'bg-outline',
        };
    @endphp

    <div class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Volver --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors group">
                <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                <span>Back to all tasks</span>
            </a>
            <span class="font-label-xs text-label-xs bg-surface-container-high px-2.5 py-1 rounded-full text-on-surface">Task #{{ $task->id }}</span>
        </div>

        <article class="bg-surface-container-lowest rounded-xl shadow-md overflow-hidden">

            {{-- Cabecera --}}
            <header class="p-6 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm bg-surface-container text-on-surface">
                        <span class="w-2 h-2 rounded-full {{ $dotClass }}"></span>
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>

                    @if ($task->due_date)
                        <div class="inline-flex items-center gap-1.5 font-label-sm text-label-sm px-3 py-1 rounded-lg {{ $isOverdue ? 'bg-error-container/40 text-error' : 'bg-surface-container-low text-on-surface-variant' }}">
                            <span class="material-symbols-outlined text-[18px] {{ $isOverdue ? 'text-error' : 'text-primary' }}">
                                {{ $isOverdue ? 'schedule' : 'calendar_today' }}
                            </span>
                            <span>Due <strong class="font-semibold {{ $isOverdue ? '' : 'text-on-surface' }}">{{ $task->due_date->format('M d, Y') }}</strong></span>
                            @unless ($isCompleted)
                                <span class="font-label-xs text-label-xs">({{ $task->due_date->diffForHumans() }})</span>
                            @endunless
                        </div>
                    @else
                        <span class="font-body-sm text-body-sm text-outline italic">No due date</span>
                    @endif
                </div>

                <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight leading-snug">
                    {{ $task->title }}
                </h1>
            </header>

            {{-- Imagen adjunta --}}
        @if ($task->image)
        <section class="bg-surface-container-low px-6 sm:px-8 py-5">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2 text-on-surface font-label-sm text-label-sm">
                    <span class="material-symbols-outlined text-[20px] text-primary">attachment</span>
                    <span>Attached image</span>
                </div>
            </div>

            <div class="relative rounded-xl overflow-hidden shadow-sm bg-surface-container-high max-h-[500px] p-2 flex items-center justify-center">
                <img src="{{ asset('storage/' . $task->image) }}" alt="{{ $task->title }}" class="max-h-[480px] w-auto max-w-full object-contain rounded-lg shadow-inner">
            </div>
        </section>
        @endif

            {{-- Descripción --}}
            <section class="p-6 sm:p-8">
                <h2 class="font-title-sm text-title-sm text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">subject</span>
                    Description
                </h2>
                @if ($task->description)
                    <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                @else
                    <p class="font-body-md text-body-md text-outline italic">This task has no description.</p>
                @endif
            </section>

            {{-- Pie: fechas y acciones --}}
            <footer class="p-6 sm:p-8 bg-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="flex items-center gap-2 font-body-sm text-body-sm text-on-surface-variant">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    <span>Created {{ $task->created_at->diffForHumans() }} • Last updated {{ $task->updated_at->diffForHumans() }}</span>
                </div>

                <div class="flex items-center gap-3 self-end sm:self-auto">
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined text-[18px]">edit</span>
                        <span>Edit Task</span>
                    </a>
                    <button type="button" id="open-delete-dialog-btn"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-error text-on-error font-label-md text-label-md shadow-sm hover:opacity-90 active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined text-[18px]">delete</span>
                        <span>Delete Task</span>
                    </button>
                </div>
            </footer>
        </article>
    </div>

    {{-- Modal de confirmación de borrado --}}
    <div id="delete-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-inverse-surface/50 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-200">
        <div id="delete-modal-content"
             class="bg-surface-container-lowest rounded-2xl p-6 sm:p-8 max-w-lg w-full shadow-xl transform scale-95 transition-transform duration-200">
            <div class="w-12 h-12 rounded-full bg-error-container text-on-error-container flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[26px]">warning</span>
            </div>

            <h3 id="modal-title" class="font-headline-md text-headline-md text-on-surface mb-2 tracking-tight">
                Delete this task?
            </h3>
            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed mb-6">
                This will permanently remove <span class="font-semibold text-on-surface">{{ $task->title }}</span>
                @if ($task->image)
                    and its attached image
                @endif
                . This cannot be undone.
            </p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="close-delete-dialog-btn"
                        class="px-4 py-2.5 rounded-lg bg-surface-container-high text-on-surface font-label-md text-label-md hover:bg-surface-container-highest transition-colors active:scale-[0.98]">
                    Cancel
                </button>

                <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="inline m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-error text-on-error font-label-md text-label-md shadow-sm hover:opacity-90 active:scale-[0.98] transition-all">
                        <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                        <span>Confirm Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const openBtn = document.getElementById('open-delete-dialog-btn');
            const closeBtn = document.getElementById('close-delete-dialog-btn');
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');

            function openModal() {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                content.classList.remove('scale-95');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                modal.classList.add('opacity-0', 'pointer-events-none');
                content.classList.add('scale-95');
                document.body.style.overflow = '';
            }

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape' && !modal.classList.contains('pointer-events-none')) closeModal();
            });
        })();
    </script>
</x-app-layout>