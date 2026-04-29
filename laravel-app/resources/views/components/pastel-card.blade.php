@props(['header' => null])

<div {{ $attributes->merge(['class' => 'bg-neutral-warm p-6 rounded-2xl shadow-soft border border-neutral-stone/50']) }}>
    @if($header)
        <h3 class="text-lg font-semibold text-text-primary mb-4">{{ $header }}</h3>
    @endif
    {{ $slot }}
</div>
