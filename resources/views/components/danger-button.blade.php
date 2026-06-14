<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-danger-500 border border-transparent rounded-xl font-semibold text-sm text-white hover:opacity-90 transition']) }}>
    {{ $slot }}
</button>
