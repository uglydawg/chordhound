@props(['size' => 'default', 'layout' => 'horizontal'])

@php
    $iconSize = match($size) {
        'small' => 'size-8',
        'large' => 'size-12',
        default => 'size-10'
    };
    $textSize = match($size) {
        'small' => 'text-sm',
        'large' => 'text-xl',
        default => 'text-base'
    };
@endphp

@if($layout === 'vertical')
    <div class="flex flex-col items-center">
        <div class="text-center {{ $textSize }} mb-1">
            <span class="truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Fredoka', sans-serif;">ChordHound</span>
        </div>
        <x-app-logo-icon class="{{ $iconSize }} fill-current text-orange-500 dark:text-orange-400" />
    </div>
@else
    <x-app-logo-icon class="{{ $iconSize }} fill-current text-orange-500 dark:text-orange-400" />
    <div class="ms-2 grid flex-1 text-start {{ $textSize }}">
        <span class="mb-0.5 truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Fredoka', sans-serif;">ChordHound</span>
    </div>
@endif
