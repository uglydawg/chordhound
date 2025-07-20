<div class="w-full">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-primary">Chords</h2>
        <div class="flex items-center space-x-2">
            <button 
                wire:click="selectAllChords"
                class="text-xs text-secondary hover:text-primary transition-colors"
            >
                Select All
            </button>
            <span class="text-tertiary">|</span>
            <button 
                wire:click="deselectAllChords"
                class="text-xs text-secondary hover:text-primary transition-colors"
            >
                Deselect All
            </button>
        </div>
    </div>
    
    {{-- Individual chord displays --}}
    <div class="grid grid-cols-4 gap-4">
        @foreach($chords as $position => $chord)
            <div 
                data-chord-position="{{ $position }}"
                class="bg-zinc-950 rounded-lg p-3 border border-zinc-800 {{ in_array($position, $selectedChords) ? 'ring-2 ring-blue-500' : '' }} chord-item"
            >
                {{-- Chord header with checkbox --}}
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <input 
                            type="checkbox" 
                            wire:click="toggleChordSelection({{ $position }})"
                            {{ in_array($position, $selectedChords) ? 'checked' : '' }}
                            class="rounded border-zinc-600 text-blue-600 focus:ring-blue-500 bg-zinc-800"
                        />
                        <label class="text-sm font-medium text-primary">
                            @if($chord['tone'])
                                {{ $chord['tone'] }}{{ $chord['semitone'] === 'minor' ? 'm' : ($chord['semitone'] === 'diminished' ? 'dim' : '') }}
                                @if($chord['inversion'] !== 'root')
                                    <span class="text-xs text-secondary">({{ ucfirst($chord['inversion']) }})</span>
                                @endif
                            @else
                                <span class="text-tertiary">Empty</span>
                            @endif
                        </label>
                    </div>
                    <div class="text-xs text-tertiary">
                        Chord {{ $position }}
                    </div>
                </div>
                
                {{-- Mini piano for this chord --}}
                @if($chord['tone'])
                    <div class="bg-zinc-900 rounded p-2">
                        <livewire:chord-piano 
                            :chord="$chord" 
                            :position="$position" 
                            :wire:key="'display-piano-' . $position"
                            :showLabels="true"
                        />
                    </div>
                    
                    {{-- Note information --}}
                    <div class="mt-2 text-xs text-secondary">
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
        <div class="flex items-center space-x-2">
            <div class="w-3 h-3 border-2 border-blue-500 rounded"></div>
            <span class="text-secondary">Selected for Print</span>
        </div>
    </div>
</div>