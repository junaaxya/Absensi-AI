@props(['name', 'title' => '', 'maxWidth' => 'lg'])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    default => 'max-w-lg',
};
@endphp

<div x-data="{ open: false }" 
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false"
     class="relative z-50">
    
    <!-- Backdrop -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40" 
         @click="open = false"></div>
    
    <!-- Modal -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div class="bg-neutral-warm rounded-2xl shadow-soft-lg w-full {{ $maxWidthClass }} max-h-[90vh] overflow-hidden" @click.stop>
            <!-- Header -->
            <div class="flex justify-between items-center px-6 py-4 border-b border-neutral-stone/50">
                <h3 class="text-lg font-bold text-text-primary">{{ $title }}</h3>
                <button @click="open = false" class="text-text-secondary hover:text-text-primary transition-colors p-1 rounded-lg hover:bg-neutral-stone/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <!-- Body -->
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
