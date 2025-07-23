@props(['size' => 'default', 'layout' => 'horizontal'])

@php
    $textSize = match($size) {
        'small' => 'text-2xl',
        'large' => 'text-4xl',
        default => 'text-3xl'
    };
    
    $logoSize = match($size) {
        'small' => ['file' => 'chordhound-logo-64x64.png', 'class' => 'w-16 h-16'],
        'large' => ['file' => 'chordhound-logo-256x256.png', 'class' => 'w-64 h-64'],
        default => ['file' => 'chordhound-logo-128x128.png', 'class' => 'w-32 h-32']
    };
@endphp

@if($layout === 'vertical')
    <div class="flex flex-col items-center">
        <div class="text-center {{ $textSize }} mb-1">
            <span class="truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Henny Penny', cursive;">ChordHound</span>
        </div>
        <img src="{{ asset('images/' . $logoSize['file']) }}" alt="ChordHound Logo" class="{{ $logoSize['class'] }}">
    </div>
@else
    <img src="{{ asset('images/' . $logoSize['file']) }}" alt="ChordHound Logo" class="{{ $logoSize['class'] }} mr-2">
    <div class="ms-2 grid flex-1 text-start {{ $textSize }}">
        <span class="mb-0.5 truncate leading-tight font-bold text-orange-700 dark:text-orange-300" style="font-family: 'Henny Penny', cursive;">ChordHound</span>
    </div>
@endif
