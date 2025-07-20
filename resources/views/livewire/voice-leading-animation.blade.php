<div>
    @if($showAnimation && count($movements) > 0)
        <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-2 my-1">
            {{-- Compact header --}}
            <div class="flex items-center justify-between mb-2">
                <div class="text-xs font-medium text-secondary">
                    {{ $fromChord['tone'] }}{{ $fromChord['semitone'] === 'minor' ? 'm' : '' }} 
                    →
                    {{ $toChord['tone'] }}{{ $toChord['semitone'] === 'minor' ? 'm' : '' }}
                </div>
                <div class="text-xs text-tertiary">
                    {{ ucfirst($variation['style']) }}
                </div>
            </div>
            
            {{-- Compact animation area --}}
            <div class="relative bg-zinc-950 rounded p-2 overflow-hidden" style="height: 80px;">
                {{-- SVG for voice leading arrows --}}
                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 200 80" style="z-index: 10;">
                    @foreach($movements as $index => $movement)
                        @php
                            $startX = 20 + ($index * 15); // Start positions spread out
                            $endX = 160 + ($index * 15); // End positions spread out
                            $startY = 65; // Lower in compact view
                            $endY = 65;
                            $controlY = 20 + ($index * 10); // Tighter curves
                            $voiceColors = ['#EF4444', '#F59E0B', '#10B981']; // Red, Yellow, Green for bass, middle, treble
                            $color = $voiceColors[$index] ?? '#6B7280';
                            $delay = $variation['delays'][$index] ?? 0;
                        @endphp
                        
                        {{-- Curved arrow path with dynamic styling --}}
                        <path
                            d="M {{ $startX }} {{ $startY }} Q {{ ($startX + $endX) / 2 }} {{ $controlY }} {{ $endX }} {{ $endY }}"
                            stroke="{{ $color }}"
                            stroke-width="2"
                            fill="none"
                            opacity="0.9"
                            class="voice-path voice-{{ $index + 1 }} {{ $variation['style'] }}"
                            style="animation-delay: {{ $delay }}s; animation-duration: {{ $variation['duration'] }};"
                        />
                        
                        {{-- Arrow head --}}
                        <polygon
                            points="{{ $endX - 4 }},{{ $endY - 3 }} {{ $endX }},{{ $endY }} {{ $endX - 4 }},{{ $endY + 3 }}"
                            fill="{{ $color }}"
                            opacity="0.9"
                            class="arrow-head voice-{{ $index + 1 }} {{ $variation['style'] }}"
                            style="animation-delay: {{ $delay }}s; animation-duration: {{ $variation['duration'] }};"
                        />
                        
                        {{-- Compact note labels --}}
                        <text
                            x="{{ ($startX + $endX) / 2 }}"
                            y="{{ $controlY - 3 }}"
                            text-anchor="middle"
                            font-size="8"
                            fill="{{ $color }}"
                            class="voice-label {{ $variation['style'] }}"
                            style="animation-delay: {{ $delay }}s; animation-duration: {{ $variation['duration'] }};"
                        >
                            {{ $movement['from']['note'] }}→{{ $movement['to']['note'] }}
                        </text>
                    @endforeach
                </svg>
            </div>
            
            {{-- Compact movement indicators --}}
            <div class="mt-2 flex flex-wrap gap-1 text-xs">
                @foreach($movements as $index => $movement)
                    @php
                        $voiceNames = ['B', 'M', 'T']; // Compact: Bass, Middle, Treble
                        $voiceName = $voiceNames[$index] ?? 'V' . ($index + 1);
                        $direction = $movement['to']['position'] > $movement['from']['position'] ? '↑' : ($movement['to']['position'] < $movement['from']['position'] ? '↓' : '=');
                        $distance = $movement['distance'];
                    @endphp
                    <div class="flex items-center space-x-1 text-tertiary">
                        <div class="w-1.5 h-1.5 rounded-full" style="background-color: {{ ['#EF4444', '#F59E0B', '#10B981'][$index] ?? '#6B7280' }};"></div>
                        <span>{{ $voiceName }}:{{ $direction }}{{ $distance > 0 ? $distance : '' }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <style>
    /* Base CSS Animations for voice leading */
    .voice-path {
        stroke-dasharray: 150;
        stroke-dashoffset: 150;
    }

    .arrow-head {
        opacity: 0;
    }

    .voice-label {
        opacity: 0;
    }

    /* Sequential animation style (default) */
    .voice-path.sequential {
        animation: drawPath 2s ease-in-out infinite;
    }
    .arrow-head.sequential {
        animation: showArrow 2s ease-in-out infinite;
    }
    .voice-label.sequential {
        animation: fadeInLabel 2s ease-in-out infinite;
    }

    /* Simultaneous animation style */
    .voice-path.simultaneous {
        animation: drawPathFast 1.5s ease-in-out infinite;
    }
    .arrow-head.simultaneous {
        animation: showArrowFast 1.5s ease-in-out infinite;
    }
    .voice-label.simultaneous {
        animation: fadeInLabelFast 1.5s ease-in-out infinite;
    }

    /* Cascade animation style */
    .voice-path.cascade {
        animation: drawPathSlow 2.5s ease-in-out infinite;
    }
    .arrow-head.cascade {
        animation: showArrowSlow 2.5s ease-in-out infinite;
    }
    .voice-label.cascade {
        animation: fadeInLabelSlow 2.5s ease-in-out infinite;
    }

    /* Reverse animation style */
    .voice-path.reverse {
        animation: drawPathReverse 2s ease-in-out infinite;
    }
    .arrow-head.reverse {
        animation: showArrowReverse 2s ease-in-out infinite;
    }
    .voice-label.reverse {
        animation: fadeInLabelReverse 2s ease-in-out infinite;
    }

    /* Standard timing keyframes */
    @keyframes drawPath {
        0% { stroke-dashoffset: 150; }
        50% { stroke-dashoffset: 0; }
        100% { stroke-dashoffset: 0; }
    }

    @keyframes showArrow {
        0% { opacity: 0; }
        50% { opacity: 0.9; }
        100% { opacity: 0.9; }
    }

    @keyframes fadeInLabel {
        0% { opacity: 0; }
        30% { opacity: 0; }
        50% { opacity: 1; }
        100% { opacity: 1; }
    }

    /* Fast timing keyframes */
    @keyframes drawPathFast {
        0% { stroke-dashoffset: 150; }
        40% { stroke-dashoffset: 0; }
        100% { stroke-dashoffset: 0; }
    }

    @keyframes showArrowFast {
        0% { opacity: 0; }
        40% { opacity: 0.9; }
        100% { opacity: 0.9; }
    }

    @keyframes fadeInLabelFast {
        0% { opacity: 0; }
        20% { opacity: 0; }
        40% { opacity: 1; }
        100% { opacity: 1; }
    }

    /* Slow timing keyframes */
    @keyframes drawPathSlow {
        0% { stroke-dashoffset: 150; }
        60% { stroke-dashoffset: 0; }
        100% { stroke-dashoffset: 0; }
    }

    @keyframes showArrowSlow {
        0% { opacity: 0; }
        60% { opacity: 0.9; }
        100% { opacity: 0.9; }
    }

    @keyframes fadeInLabelSlow {
        0% { opacity: 0; }
        40% { opacity: 0; }
        60% { opacity: 1; }
        100% { opacity: 1; }
    }

    /* Reverse timing keyframes */
    @keyframes drawPathReverse {
        0% { stroke-dashoffset: 150; }
        35% { stroke-dashoffset: 0; }
        100% { stroke-dashoffset: 0; }
    }

    @keyframes showArrowReverse {
        0% { opacity: 0; }
        35% { opacity: 0.9; }
        100% { opacity: 0.9; }
    }

    @keyframes fadeInLabelReverse {
        0% { opacity: 0; }
        20% { opacity: 0; }
        35% { opacity: 1; }
        100% { opacity: 1; }
    }
    </style>
</div>