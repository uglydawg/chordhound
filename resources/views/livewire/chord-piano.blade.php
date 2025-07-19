<div class="w-full">
    @if(!empty($chord['tone']))
        <div class="mt-3 bg-gray-50 dark:bg-zinc-900 rounded p-2">
            <svg viewBox="0 0 {{ $totalWidth }} 100" class="w-full h-auto" style="max-height: 80px;">
                {{-- Draw white keys first --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'white')
                        <rect 
                            x="{{ $key['x'] }}" 
                            y="0" 
                            width="{{ $key['width'] - 1 }}" 
                            height="80"
                            fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#3B82F6' : '#10B981') : '#FFFFFF' }}"
                            stroke="#000000"
                            stroke-width="0.5"
                        />
                    @endif
                @endforeach
                
                {{-- Draw black keys on top --}}
                @foreach($pianoKeys as $key)
                    @if($key['type'] === 'black')
                        <rect 
                            x="{{ $key['x'] }}" 
                            y="0" 
                            width="{{ $key['width'] }}" 
                            height="50"
                            fill="{{ $key['isActive'] ? ($key['isBlueNote'] ? '#1E40AF' : '#059669') : '#000000' }}"
                            stroke="#000000"
                            stroke-width="0.5"
                        />
                    @endif
                @endforeach
            </svg>
        </div>
    @endif
</div>