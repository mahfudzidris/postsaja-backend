<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-border rounded-xl font-semibold text-sm text-text-main hover:bg-primary-50 transition']) }}>
    {{ $slot }}
</button>
