<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-brand text-white font-semibold rounded-xl hover:opacity-90 transition text-sm']) }}>
    {{ $slot }}
</button>
