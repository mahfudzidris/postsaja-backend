@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-primary-50 text-primary-600 transition'
            : 'inline-flex items-center px-3 py-2 text-sm font-medium text-muted hover:text-text-main hover:bg-primary-50 rounded-lg transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
