@props(['href'])

<a {{ $attributes->merge(['href' => $href ?? '#', 'class' => 'block px-4 py-2 text-sm text-muted hover:bg-primary-50 hover:text-text-main transition']) }}>
    {{ $slot }}
</a>
