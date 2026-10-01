{{-- filepath: resources/views/components/task-form.blade.php --}}
@props(['task' => null])

{{-- Título --}}
<div class="space-y-2">
    <div class="flex items-center justify-between">
        <label for="title" class="font-label-md text-label-md text-on-surface flex items-center gap-1 font-semibold">
            <span>Title</span>
            <span class="text-error font-bold">*</span>
        </label>
        <span class="font-label-xs text-label-xs text-on-surface-variant" id="title-counter">
            {{ strlen(old('title', $task?->title ?? '')) }} / 255
        </span>
    </div>
    <input id="title" name="title" type="text" maxlength="255" required
           value="{{ old('title', $task?->title) }}"
           oninput="document.getElementById('title-counter').innerText = this.value.length + ' / 255'"
           placeholder="e.g. Set up database backup cron job"
           class="w-full px-4 py-2.5 rounded-lg bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline border-0 focus:outline-none focus:ring-2 focus:ring-primary transition-all @error('title') ring-2 ring-error @enderror">
    @error('title')
        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
    @enderror
</div>

{{-- Descripción --}}
<div class="space-y-2">
    <label for="description" class="font-label-md text-label-md text-on-surface font-semibold">Description</label>
    <span class="font-label-xs text-label-xs text-outline font-normal">(Optional)</span>

    <textarea id="description" name="description" rows="4"
              placeholder="Provide additional details, acceptance criteria, or context..."
              class="w-full px-4 py-3 rounded-lg bg-surface font-body-md text-body-md text-on-surface placeholder:text-outline border-0 focus:outline-none focus:ring-2 focus:ring-primary transition-all resize-y min-h-[110px] @error('description') ring-2 ring-error @enderror">{{ old('description', $task?->description) }}</textarea>
    @error('description')
        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
    @enderror
</div>

{{-- Estado y Fecha --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div class="space-y-2">
        <label for="status" class="font-label-md text-label-md text-on-surface font-semibold flex items-center gap-1">
            <span>Status</span>
            <span class="text-error font-bold">*</span>
        </label>
        <select id="status" name="status"
                class="w-full appearance-none px-4 py-2.5 pr-10 rounded-lg bg-surface font-body-md text-body-md text-on-surface border-0 focus:outline-none focus:ring-2 focus:ring-primary transition-all cursor-pointer @error('status') ring-2 ring-error @enderror">
            <option value="pending" @selected(old('status', $task?->status ?? 'pending') === 'pending')>Pending</option>
            <option value="in_progress" @selected(old('status', $task?->status) === 'in_progress')>In Progress</option>
            <option value="completed" @selected(old('status', $task?->status) === 'completed')>Completed</option>
        </select>
        @error('status')
            <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-2">
        <label for="due_date" class="font-label-md text-label-md text-on-surface font-semibold">Due Date</label>
        <input id="due_date" name="due_date" type="date"
               value="{{ old('due_date', optional($task?->due_date)->format('Y-m-d')) }}"
               class="w-full px-4 py-2.5 rounded-lg bg-surface font-body-md text-body-md text-on-surface border-0 focus:outline-none focus:ring-2 focus:ring-primary transition-all cursor-pointer @error('due_date') ring-2 ring-error @enderror">
        @error('due_date')
            <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
        @else
            <p class="font-body-sm text-body-sm text-on-surface-variant">Leave blank if this task is open-ended.</p>
        @enderror
    </div>
</div>

{{-- Adjunto de Imagen --}}
<div class="space-y-3">
    <label class="font-label-md text-label-md text-on-surface flex items-center gap-1.5 font-semibold">
        <span>Attachment Image</span>
        <span class="font-label-xs text-label-xs text-outline font-normal">(Optional)</span>
    </label>

    @if ($task && $task->image)
        <div id="preview-panel" class="bg-surface-container-low rounded-xl p-4 flex flex-col sm:flex-row items-center gap-4">
            <div class="relative w-28 h-20 rounded-lg overflow-hidden shadow-sm flex-shrink-0 bg-surface-container">
                <img src="{{ asset('storage/' . $task->image) }}" alt="{{ $task->title }}" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0 text-center sm:text-left">
                <p class="font-label-md text-label-md text-on-surface font-semibold truncate">{{ basename($task->image) }}</p>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Current attachment</p>
            </div>
            <button type="button" class="px-3 py-1.5 rounded-lg bg-error-container/40 text-error font-label-sm text-label-sm"
                    onclick="document.getElementById('remove-image').value = '1'; document.getElementById('preview-panel').classList.add('hidden');">
                Remove
            </button>
        </div>
        <input type="hidden" name="remove_image" id="remove-image" value="0">
    @endif

    <div id="dropzone" onclick="document.getElementById('image').click()"
         class="relative rounded-xl p-8 bg-surface text-center hover:bg-surface-container transition-all cursor-pointer">
        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="hidden"
               onchange="
                   if (this.files[0]) {
                       document.getElementById('file-label').innerText = this.files[0].name;
                       document.getElementById('remove-image') && (document.getElementById('remove-image').value = '0');
                   }">
        <div class="space-y-2 pointer-events-none">
            <span class="material-symbols-outlined text-[28px] text-primary">cloud_upload</span>
            <p id="file-label" class="font-label-md text-label-md text-on-surface font-semibold">
                {{ ($task && $task->image) ? 'Click to replace image' : 'Click to upload an image' }}
            </p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">JPG, PNG or WEBP. Max size: 2 MB.</p>
        </div>
    </div>
    @error('image')
        <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
    @enderror
</div>