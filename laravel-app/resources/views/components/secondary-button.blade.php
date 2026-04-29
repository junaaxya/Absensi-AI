<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-neutral-warm border border-neutral-stone text-text-secondary rounded-xl font-semibold text-xs uppercase tracking-widest shadow-sm hover:bg-neutral-stone focus:outline-none focus:ring-2 focus:ring-pastel-sage focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
