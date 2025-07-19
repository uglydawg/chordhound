@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 border-gray-300 dark:border-gray-600',
    'danger' => 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 border-red-300 dark:border-red-600',
    'success' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 border-green-300 dark:border-green-600',
];
@endphp

<div {{ $attributes->merge(['class' => 'px-4 py-3 rounded border ' . ($variants[$variant] ?? $variants['default'])]) }}>
    {{ $slot }}
</div>