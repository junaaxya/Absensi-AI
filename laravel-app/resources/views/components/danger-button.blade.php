<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-pastel-rose hover:bg-pastel-rose/80 text-text-primary rounded-xl font-semibold text-xs uppercase tracking-widest active:bg-pastel-rose-dark focus:outline-none focus:ring-2 focus:ring-pastel-rose focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
