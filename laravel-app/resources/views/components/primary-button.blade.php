<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-pastel-sage hover:bg-pastel-sage-dark text-text-primary rounded-xl font-semibold text-xs uppercase tracking-widest focus:bg-pastel-sage-dark active:bg-pastel-sage-dark focus:outline-none focus:ring-2 focus:ring-pastel-sage focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
