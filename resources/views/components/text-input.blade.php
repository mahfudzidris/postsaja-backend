@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-border focus:border-primary-500 focus:ring-primary-500 rounded-xl shadow-sm']) }}>
