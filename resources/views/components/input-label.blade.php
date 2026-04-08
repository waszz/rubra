@props(['value'])

<label {{ $attributes->merge([
    'class' => 'block text-sm font-semibold mb-1 text-gray-700 dark:text-gray-300 uppercase tracking-wide'
]) }}>
    {{ $value ?? $slot }}
</label>
