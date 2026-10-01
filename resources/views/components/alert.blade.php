@props(['type', 'message'])

<div class="flex items-center justify-between bg-{{ $type === 'success' ? 'green-100' : 'red-100' }} text-{{ $type === 'success' ? 'green-800' : 'red-800' }} p-4 rounded-lg mb-4">
    <span>{{ $message }}</span>
    <button class="text-{{ $type === 'success' ? 'green-800' : 'red-800' }} hover:text-{{ $type === 'success' ? 'green-600' : 'red-600' }}" onclick="this.parentElement.remove()">✕</button>
</div>