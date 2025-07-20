<div class="w-full overflow-x-auto dark-scrollbar">
    <div>
        <h2 class="text-lg font-semibold text-primary mb-4">Piano Keyboard</h2>
        
        <div class="bg-zinc-950 rounded-lg p-4 shadow-inner">
            <div class="relative" style="width: {{ $totalWidth }}px; height: 200px;">
                <svg viewBox="0 0 {{ $totalWidth }} 200" style="width: {{ $totalWidth }}px; height: 200px;">
                    {{-- Piano background --}}
                    <rect x="0" y="0" width="{{ $totalWidth }}" height="200" fill="#0a0a0a" />
                    
                    {{-- Draw white keys first --}}
                    @foreach($pianoKeys as $key)
                        @if($key['type'] === 'white')
                            <g>
                                <rect 
                                    x="{{ $key['x'] }}" 
                                    y="0" 
                                    width="{{ $key['width'] - 2 }}" 
                                    height="175"
                                    fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#60A5FA' : '#34D399') : '#FAFAFA' }}"
                                    stroke="#333333"
                                    stroke-width="1"
                                    rx="3"
                                    class="cursor-pointer transition-all hover:opacity-90"
                                />
                                {{-- Key shadow/3D effect --}}
                                <rect 
                                    x="{{ $key['x'] }}" 
                                    y="170" 
                                    width="{{ $key['width'] - 2 }}" 
                                    height="5"
                                    fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#E5E5E5' }}"
                                    rx="1"
                                />
                                @if($key['isActive'])
                                    <circle
                                        cx="{{ $key['x'] + $key['width'] / 2 }}"
                                        cy="140"
                                        r="8"
                                        fill="{{ $key['isBlueNote'] ? '#1E40AF' : '#047857' }}"
                                    />
                                    <text 
                                        x="{{ $key['x'] + $key['width'] / 2 }}" 
                                        y="145" 
                                        text-anchor="middle"
                                        font-size="11"
                                        font-weight="bold"
                                        fill="#FFFFFF"
                                    >
                                        {{ $key['chordPosition'] }}
                                    </text>
                                @endif
                                <text 
                                    x="{{ $key['x'] + $key['width'] / 2 }}" 
                                    y="195" 
                                    text-anchor="middle"
                                    font-size="9"
                                    fill="#737373"
                                    font-weight="500"
                                >
                                    {{ $key['note'] }}{{ $key['octave'] }}
                                </text>
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
                                height="110"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#2563EB' : '#059669') : '#171717' }}"
                                stroke="#000000"
                                stroke-width="1"
                                rx="2"
                                class="cursor-pointer transition-all hover:opacity-90"
                            />
                            {{-- Black key highlight for 3D effect --}}
                            <rect 
                                x="{{ $key['x'] + 2 }}" 
                                y="2" 
                                width="{{ $key['width'] - 4 }}" 
                                height="15"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#262626' }}"
                                rx="1"
                                opacity="0.5"
                            />
                            @if($key['isActive'])
                                <circle
                                    cx="{{ $key['x'] + $key['width'] / 2 }}"
                                    cy="80"
                                    r="7"
                                    fill="{{ $key['isBlueNote'] ? '#1E40AF' : '#047857' }}"
                                />
                                <text 
                                    x="{{ $key['x'] + $key['width'] / 2 }}" 
                                    y="84" 
                                    text-anchor="middle"
                                    font-size="10"
                                    font-weight="bold"
                                    fill="#FFFFFF"
                                >
                                    {{ $key['chordPosition'] }}
                                </text>
                            @endif
                        </g>
                    @endif
                @endforeach
            </svg>
        </div>
        
        <div class="mt-4 flex items-center space-x-6 text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span class="text-secondary">Active Notes</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span class="text-secondary">Blue Notes</span>
            </div>
        </div>
        </div>
    </div>
</div>

