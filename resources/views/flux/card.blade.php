@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm ' . $class]) }}>
    {{ $slot }}
</div>