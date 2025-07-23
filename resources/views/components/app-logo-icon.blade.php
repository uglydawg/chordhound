{{-- Check if uploaded dog logo exists, otherwise use SVG fallback --}}
@if(file_exists(public_path('images/chordhound-logo.png')))
    <img src="{{ asset('images/chordhound-logo.png') }}" alt="ChordHound Logo" {{ $attributes->merge(['class' => 'object-contain w-full h-full']) }} />
@else
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" {{ $attributes }}>
        <!-- Enhanced Dog head with musical notes -->
        <path 
            fill="currentColor" 
            d="M24 6c-7 0-12 5-14 10-1 2-1 4 0 6l1 2c0 4 3 6 5 8 3 3 6 5 8 5s5-2 8-5c2-2 5-4 5-8l1-2c1-2 1-4 0-6-2-5-7-10-14-10z"
        />
        <!-- Enhanced Left ear -->
        <path 
            fill="currentColor" 
            d="M13 10c-2-1-4 0-6 2-1 3 0 5 2 6l4 2c1 0 2-1 2-3l-2-7z"
        />
        <!-- Enhanced Right ear -->
        <path 
            fill="currentColor" 
            d="M35 10c2-1 4 0 6 2 1 3 0 5-2 6l-4 2c-1 0-2-1-2-3l2-7z"
        />
        <!-- Eyes with sparkle -->
        <circle fill="currentColor" cx="19" cy="20" r="2.5"/>
        <circle fill="currentColor" cx="29" cy="20" r="2.5"/>
        <circle fill="white" cx="19.5" cy="19.5" r="0.8"/>
        <circle fill="white" cx="29.5" cy="19.5" r="0.8"/>
        <!-- Heart-shaped nose -->
        <path 
            fill="currentColor" 
            d="M24 24c-1.5 0-2.5 1-2.5 2.5c0 1 0.5 1.5 1 2l1.5 1.5l1.5-1.5c0.5-0.5 1-1 1-2c0-1.5-1-2.5-2.5-2.5z"
        />
        <!-- Smiling mouth -->
        <path 
            fill="currentColor" 
            d="M24 30c-3 0-5 1-5 3c0 2 1 3 3 3h4c2 0 3-1 3-3c0-2-2-3-5-3z"
        />
        <!-- Musical notes floating around -->
        <circle fill="currentColor" cx="8" cy="15" r="1"/>
        <path fill="currentColor" d="M8 15v-4h1v4"/>
        <circle fill="currentColor" cx="40" cy="18" r="1"/>
        <path fill="currentColor" d="M40 18v-4h1v4"/>
        <!-- Piano keys at bottom -->
        <rect fill="currentColor" x="10" y="40" width="4" height="6"/>
        <rect fill="currentColor" x="16" y="40" width="4" height="6"/>
        <rect fill="currentColor" x="22" y="40" width="4" height="6"/>
        <rect fill="currentColor" x="28" y="40" width="4" height="6"/>
        <rect fill="currentColor" x="34" y="40" width="4" height="6"/>
        <!-- Black keys -->
        <rect fill="currentColor" x="13" y="40" width="2.5" height="4"/>
        <rect fill="currentColor" x="19" y="40" width="2.5" height="4"/>
        <rect fill="currentColor" x="31" y="40" width="2.5" height="4"/>
    </svg>
@endif
