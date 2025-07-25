<div class="w-full">
    @if(!empty($chord['tone']))
        <div class="bg-zinc-950 rounded-lg p-3 shadow-inner">
            <svg viewBox="0 0 {{ $totalWidth }} {{ $larger ? 120 : 100 }}" class="w-full h-auto" style="max-height: {{ $larger ? '150px' : '100px' }}">
                {{-- Piano background --}}
                <rect x="0" y="0" width="{{ $totalWidth }}" height="{{ $larger ? 120 : 100 }}" fill="#1a1a1a" />
                
                {{-- Draw white keys first --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'white')
                        <g>
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="0" 
                                width="{{ $key['width'] - 1 }}" 
                                height="{{ $larger ? 118 : 98 }}"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#60A5FA' : '#34D399') : '#FAFAFA' }}"
                                stroke="#333333"
                                stroke-width="0.5"
                                rx="2"
                            />
                            {{-- Key shadow/3D effect --}}
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="{{ $larger ? 114 : 94 }}" 
                                width="{{ $key['width'] - 1 }}" 
                                height="4"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#E5E5E5' }}"
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
                                y="0" 
                                width="{{ $key['width'] }}" 
                                height="{{ $larger ? 75 : 60 }}"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#2563EB' : '#059669') : '#171717' }}"
                                stroke="#000000"
                                stroke-width="1"
                                rx="2"
                            />
                            {{-- Black key highlight --}}
                            <rect 
                                x="{{ $key['x'] + 2 }}" 
                                y="2" 
                                width="{{ $key['width'] - 4 }}" 
                                height="{{ $larger ? 10 : 8 }}"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#262626' }}"
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
                            fill="{{ $key['isBlueNote'] ? '#1E40AF' : '#047857' }}"
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
                                font-size="8"
                                font-weight="bold"
                                fill="{{ $key['type'] === 'white' ? '#666666' : '#FFFFFF' }}"
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