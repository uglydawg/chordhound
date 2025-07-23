@props(['size' => 'default', 'layout' => 'horizontal'])

@php
    $textSize = match($size) {
        'small' => 'text-2xl',
        'large' => 'text-4xl',
        default => 'text-3xl'
    };
@endphp

@if($layout === 'vertical')
    <div class="flex flex-col items-center">
        <div class="text-center {{ $textSize }} mb-1">
            <span class="truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Henny Penny', cursive;">ChordHound</span>
        </div>
        <img src="{{ asset('images/chordhound-logo-optimized.png') }}" alt="ChordHound Logo" class="w-32 h-32">
    </div>
@else
    <img src="{{ asset('images/chordhound-logo-optimized.png') }}" alt="ChordHound Logo" class="w-32 h-32 mr-2">
    <div class="ms-2 grid flex-1 text-start {{ $textSize }}">
        <span class="mb-0.5 truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Henny Penny', cursive;">ChordHound</span>
    </div>
@endif
