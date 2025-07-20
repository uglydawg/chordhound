<div class="w-full">
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-primary">Chords</h2>
    </div>
    
    {{-- Individual chord displays in 2x2 grid --}}
    <div class="grid grid-cols-2 gap-4">
        @foreach($chords as $position => $chord)
            <div 
                data-chord-position="{{ $position }}"
                class="bg-zinc-950 rounded-lg p-4 border border-zinc-800 chord-item"
            >
                {{-- Chord header --}}
                <div class="flex items-center justify-between mb-3">
                    <div class="text-lg font-bold text-primary">
                        @if($chord['tone'])
                            {{ $chord['tone'] }}{{ $chord['semitone'] === 'minor' ? 'm' : ($chord['semitone'] === 'diminished' ? 'dim' : '') }}
                            @if($chord['inversion'] !== 'root')
                                <span class="text-sm text-secondary">({{ ucfirst($chord['inversion']) }})</span>
                            @endif
                        @else
                            <span class="text-tertiary">Empty</span>
                        @endif
                    </div>
                    <div class="text-xs text-tertiary">
                        Chord {{ $position }}
                    </div>
                </div>
                
                {{-- Piano display for this chord --}}
                @if($chord['tone'])
                    <div class="bg-zinc-900 rounded p-3">
                        <livewire:chord-piano 
                            :chord="$chord" 
                            :position="$position" 
                            :wire:key="'display-piano-' . $position"
                            :showLabels="true"
                            :larger="true"
                        />
                    </div>
                    
                    {{-- Note information --}}
                    <div class="mt-3 text-sm text-secondary font-medium">
                        @php
                            $notes = app(\App\Services\ChordService::class)->getChordNotes(
                                $chord['tone'],
                                $chord['semitone'] ?? 'major',
                                $chord['inversion'] ?? 'root'
                            );
                        @endphp
                        Notes: {{ implode(' - ', array_slice($notes, 0, 3)) }}
                    </div>
                @else
                    <div class="h-24 flex items-center justify-center text-tertiary text-sm">
                        No chord selected
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    {{-- Legend --}}
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