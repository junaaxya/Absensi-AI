@props(['type' => 'default'])

@php
$classes = match($type) {
    'success' => 'bg-pastel-sage/30 text-green-800',
    'warning' => 'bg-pastel-peach/50 text-amber-700',
    'danger' => 'bg-pastel-rose/50 text-red-700',
    'info' => 'bg-pastel-sky/30 text-blue-700',
    'late' => 'bg-pastel-rose text-red-800',
    'overtime' => 'bg-pastel-peach text-text-primary',
    default => 'bg-neutral-stone/50 text-text-secondary',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold $classes"]) }}>
    {{ $slot }}
</span>
