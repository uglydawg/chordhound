<div class="w-full overflow-x-auto">
    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium mb-4">Piano Keyboard</h2>
        
        <div class="relative" style="width: {{ $totalWidth }}px; height: 200px;">
            <svg viewBox="0 0 {{ $totalWidth }} 200" style="width: {{ $totalWidth }}px; height: 200px;">
                {{-- Draw white keys first --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'white')
                        <g>
                            <rect 
                                x="{{ $key['x'] }}" 
                                y="0" 
                                width="{{ $key['width'] - 1 }}" 
                                height="180"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#FFFFFF' }}"
                                stroke="#000000"
                                stroke-width="1"
                                class="cursor-pointer transition-colors hover:opacity-80"
                            />
                            @if($key['isActive'])
                                <text 
                                    x="{{ $key['x'] + $key['width'] / 2 }}" 
                                    y="160" 
                                    text-anchor="middle"
                                    font-size="10"
                                    fill="{{ $key['isBlueNote'] ? '#FFFFFF' : '#FFFFFF' }}"
                                >
                                    {{ $key['chordPosition'] }}
                                </text>
                            @endif
                            <text 
                                x="{{ $key['x'] + $key['width'] / 2 }}" 
                                y="195" 
                                text-anchor="middle"
                                font-size="8"
                                fill="#666666"
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
                                height="120"
                                fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#1E40AF' : '#059669') : '#000000' }}"
                                stroke="#000000"
                                stroke-width="1"
                                class="cursor-pointer transition-colors hover:opacity-80"
                            />
                            @if($key['isActive'])
                                <text 
                                    x="{{ $key['x'] + $key['width'] / 2 }}" 
                                    y="100" 
                                    text-anchor="middle"
                                    font-size="10"
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
                <span>Active Notes</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span>Blue Notes</span>
            </div>
        </div>
    </div>
</div>

