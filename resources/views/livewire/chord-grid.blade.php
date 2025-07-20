<div class="space-y-6">
    {{-- Chord Timeline/Grid --}}
    <div class="timeline-grid p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-primary">Chord Progression</h2>
            <button
                wire:click="toggleSuggestions"
                class="text-sm text-secondary hover:text-primary transition-colors flex items-center space-x-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>AI Suggestions</span>
            </button>
        </div>
        
        {{-- Chord Blocks --}}
        <div class="grid grid-cols-4 gap-4">
            @foreach($chords as $position => $chord)
                <div 
                    wire:click="selectChord({{ $position }})"
                    class="chord-block {{ $activePosition === $position ? 'chord-block-active' : '' }} {{ $chord['is_blue_note'] ? 'ring-2 ring-purple-500' : '' }} relative group"
                >
                    <div class="text-center">
                        @if($chord['tone'])
                            <div class="text-2xl font-bold mb-1">
                                {{ $chord['tone'] }}{{ $chord['semitone'] === 'minor' ? 'm' : ($chord['semitone'] === 'diminished' ? 'dim' : '') }}
                            </div>
                            @if($chord['inversion'] !== 'root')
                                <div class="text-xs text-secondary">{{ ucfirst($chord['inversion']) }}</div>
                            @endif
                        @else
                            <div class="text-2xl text-tertiary">+</div>
                        @endif
                    </div>
                    
                    {{-- Beat indicator --}}
                    <div class="absolute -bottom-6 left-0 right-0 text-center text-xs text-tertiary">
                        {{ ($position - 1) * 2 + 1 }}-{{ ($position - 1) * 2 + 2 }}
                    </div>
                    
                    {{-- Clear button --}}
                    @if($chord['tone'])
                        <button
                            wire:click.stop="clearChord({{ $position }})"
                            class="absolute -top-2 -right-2 w-6 h-6 bg-zinc-700 hover:bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Chord Palette --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6">
        <h3 class="text-sm font-medium text-secondary mb-4">Chord Palette</h3>
        
        {{-- Root Notes --}}
        <div class="grid grid-cols-12 gap-2 mb-4">
            @foreach($tones as $tone)
                <button
                    wire:click="setChord('{{ $tone }}')"
                    class="chord-suggestion text-center"
                >
                    {{ $tone }}
                </button>
            @endforeach
        </div>
        
        {{-- Chord Types --}}
        <div class="flex space-x-2 mb-4">
            <span class="text-xs text-tertiary">Quick add:</span>
            @foreach(['major' => '', 'minor' => 'm', 'diminished' => 'dim', 'augmented' => 'aug'] as $type => $suffix)
                <button
                    wire:click="setChord('{{ $chords[$activePosition]['tone'] ?? 'C' }}', '{{ $type }}')"
                    class="text-xs px-3 py-1 bg-zinc-800 hover:bg-zinc-700 rounded text-secondary hover:text-primary transition-colors"
                >
                    {{ ucfirst($type) }}
                </button>
            @endforeach
        </div>
    </div>
    
    {{-- AI Suggestions Dropdown --}}
    @if($showSuggestions)
        <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-4 space-y-2">
            <h4 class="text-sm font-medium text-secondary mb-3">Common Progressions</h4>
            @foreach($chordSuggestions as $name => $progression)
                <button
                    wire:click="applySuggestion('{{ $name }}')"
                    class="w-full text-left px-4 py-2 bg-zinc-800 hover:bg-zinc-700 rounded transition-colors"
                >
                    <span class="text-primary font-medium">{{ $name }}</span>
                    <span class="text-secondary text-sm ml-2">
                        ({{ collect($progression)->map(fn($c) => $c['tone'] . ($c['semitone'] === 'minor' ? 'm' : ''))->join(' - ') }})
                    </span>
                </button>
            @endforeach
        </div>
    @endif
    
    {{-- Piano Display for Active Chord --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-4">
        <h3 class="text-sm font-medium text-secondary mb-3">Chord Piano</h3>
        <livewire:chord-piano :chord="$chords[$activePosition]" :position="$activePosition" wire:key="active-chord-piano-{{ $activePosition }}" />
    </div>
</div>