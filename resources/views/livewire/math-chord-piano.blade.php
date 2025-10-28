<div class="w-full">
    @if(!empty($calculatedNotes))
        <div class="bg-zinc-950 rounded-lg p-3 shadow-inner">
            @if($chordName)
                <h3 class="text-white text-center mb-2 font-semibold">{{ $chordName }}</h3>
            @endif
            <svg viewBox="0 0 {{ $totalWidth }} {{ $larger ? 120 : 100 }}" class="w-full h-auto" style="max-height: {{ $larger ? '200px' : '150px' }}">
                {{-- Piano background --}}
                <rect x="0" y="0" width="{{ $totalWidth }}" height="{{ $larger ? 120 : 100 }}" fill="#1a1a1a" />
                
                {{-- Draw white keys first --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'white')
                        <g>
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="{{ $key['isPressed'] ? '2' : '0' }}" 
                                width="{{ $key['width'] - 1 }}" 
                                height="{{ $key['isPressed'] ? ($larger ? 116 : 96) : ($larger ? 118 : 98) }}"
                                fill="{{ $key['isPressed'] ? '#DC2626' : ($key['isHighlighted'] ? '#FBBF24' : ($key['isActive'] ? '#34D399' : '#FAFAFA')) }}"
                                stroke="#333333"
                                stroke-width="0.5"
                                rx="2"
                            />
                            {{-- Key shadow/3D effect --}}
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="{{ $key['isPressed'] ? ($larger ? 116 : 96) : ($larger ? 114 : 94) }}" 
                                width="{{ $key['width'] - 1 }}" 
                                height="{{ $key['isPressed'] ? '2' : '4' }}"
                                fill="{{ $key['isPressed'] ? '#B91C1C' : ($key['isHighlighted'] ? '#F59E0B' : ($key['isActive'] ? '#10B981' : '#E5E5E5')) }}"
                                rx="1"
                            />
                        </g>
                    @endif
                @endforeach
                
                {{-- Draw black keys on top --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'black')
                        <g>
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="{{ $key['isPressed'] ? '1' : '0' }}" 
                                width="{{ $key['width'] }}" 
                                height="{{ $key['isPressed'] ? ($larger ? 74 : 59) : ($larger ? 75 : 60) }}"
                                fill="{{ $key['isPressed'] ? '#7F1D1D' : ($key['isHighlighted'] ? '#DC2626' : ($key['isActive'] ? '#059669' : '#171717')) }}"
                                stroke="#000000"
                                stroke-width="1"
                                rx="2"
                            />
                            {{-- Black key highlight --}}
                            <rect 
                                x="{{ $key['x'] + 2 }}" 
                                y="{{ $key['isPressed'] ? '3' : '2' }}" 
                                width="{{ $key['width'] - 4 }}" 
                                height="{{ $larger ? 10 : 8 }}"
                                fill="{{ $key['isPressed'] ? '#450A0A' : ($key['isHighlighted'] ? '#EF4444' : ($key['isActive'] ? '#10B981' : '#262626')) }}"
                                rx="1"
                                opacity="0.6"
                            />
                        </g>
                    @endif
                @endforeach
                
                {{-- Active note indicators --}}
                @foreach($pianoKeys as $key)
                    @if($key['isActive'])
                        <circle
                            cx="{{ $key['x'] + $key['width'] / 2 }}"
                            cy="{{ $key['type'] === 'white' ? ($larger ? 90 : 75) : ($larger ? 55 : 45) }}"
                            r="4"
                            fill="#047857"
                            opacity="0.8"
                        />
                    @endif
                @endforeach
                
                {{-- Note labels if enabled --}}
                @if($showLabels)
                    @foreach($pianoKeys as $key)
                        @if($key['isActive'])
                            <text 
                                x="{{ $key['x'] + $key['width'] / 2 }}" 
                                y="{{ $key['type'] === 'white' ? ($larger ? 105 : 88) : ($larger ? 65 : 50) }}" 
                                text-anchor="middle"
                                font-size="10"
                                font-weight="bold"
                                fill="{{ $key['type'] === 'white' ? '#047857' : '#FFFFFF' }}"
                            >
                                {{ $key['note'] }}{{ $key['octave'] }}
                            </text>
                        @endif
                    @endforeach
                @endif
            </svg>
        </div>
    @else
        <div class="bg-zinc-950 rounded-lg p-3 shadow-inner">
            <div class="text-center text-zinc-600 text-sm py-4">
                Select a chord to display
            </div>
        </div>
    @endif
</div>