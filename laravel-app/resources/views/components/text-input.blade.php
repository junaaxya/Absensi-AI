@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-neutral-stone focus:border-pastel-sage focus:ring-pastel-sage/20 rounded-xl shadow-sm']) }}>
