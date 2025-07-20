<div class="space-y-6">
    {{-- Chord Timeline/Grid --}}
    <div class="timeline-grid p-6">
        {{-- Key and Progression Selectors --}}
        <div class="flex flex-wrap items-center gap-4 mb-6 pb-4 border-b border-zinc-800">
            <div class="flex flex-wrap items-center gap-2">
                <label class="text-sm font-medium text-secondary mr-2">Key:</label>
                <div class="flex space-x-1">
                    @foreach($availableKeys as $key)
                        <button
                            wire:click="setKey('{{ $key }}')"
                            class="px-3 py-1.5 text-sm font-medium rounded transition-all
                                {{ $selectedKey === $key 
                                    ? 'bg-blue-600 text-white border border-blue-500 shadow-lg' 
                                    : (str_contains($key, '#') 
                                        ? 'bg-zinc-900 text-gray-400 border border-zinc-800 hover:bg-zinc-800 hover:text-white' 
                                        : 'bg-zinc-800 text-gray-300 border border-zinc-700 hover:bg-zinc-700 hover:text-white') }}"
                        >
                            {{ $key }}
                        </button>
                    @endforeach
                </div>
                
                <div class="h-6 w-px bg-zinc-700 mx-3"></div>
                
                <div class="flex space-x-1">
                    <button
                        wire:click="setKeyType('major')"
                        class="px-3 py-1.5 text-sm font-medium rounded transition-all
                            {{ $selectedKeyType === 'major' 
                                ? 'bg-blue-600 text-white border border-blue-500' 
                                : 'bg-zinc-800 text-gray-300 border border-zinc-700 hover:bg-zinc-700 hover:text-white' }}"
                    >
                        Major
                    </button>
                    <button
                        wire:click="setKeyType('minor')"
                        class="px-3 py-1.5 text-sm font-medium rounded transition-all
                            {{ $selectedKeyType === 'minor' 
                                ? 'bg-blue-600 text-white border border-blue-500' 
                                : 'bg-zinc-800 text-gray-300 border border-zinc-700 hover:bg-zinc-700 hover:text-white' }}"
                    >
                        Minor
                    </button>
                </div>
                
                <div class="h-6 w-px bg-zinc-700 mx-3"></div>
                
                <label class="text-sm font-medium text-secondary">Progression:</label>
                <select
                    wire:change="setProgression($event.target.value)"
                    class="bg-zinc-800 border border-zinc-700 text-white rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[200px]"
                >
                    <option value="" @if(!$selectedProgression) selected @endif>Custom</option>
                    @foreach($progressions as $romanNumerals => $progression)
                        <option value="{{ $romanNumerals }}" @if($selectedProgression === $romanNumerals) selected @endif>
                            {{ $romanNumerals }}
                            @if(isset($progressionDescriptions[$romanNumerals]))
                                - {{ $progressionDescriptions[$romanNumerals] }}
                            @endif
                        </option>
                    @endforeach
                </select>
                
                @if($selectedProgression)
                    @php
                        $transposedChords = app(\App\Services\ChordService::class)->transposeProgression($selectedKey, $selectedKeyType, $progressions[$selectedProgression]);
                    @endphp
                    <div class="text-sm text-blue-400">
                        = {{ collect($transposedChords)->map(fn($c) => $c['tone'] . ($c['semitone'] === 'minor' ? 'm' : ($c['semitone'] === 'diminished' ? 'dim' : '')))->join(' - ') }}
                    </div>
                @endif
            </div>
        </div>
        
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <h2 class="text-lg font-semibold text-primary">Chord Progression</h2>
                @if($showRomanNumerals && is_array($romanNumerals) && count(array_filter($romanNumerals)) > 0)
                    <div class="text-sm text-blue-400 font-medium">
                        {{ collect($romanNumerals)->filter()->join('-') }}
                    </div>
                @endif
            </div>
            <div class="flex items-center space-x-3">
                <button
                    wire:click="toggleRomanNumerals"
                    class="text-sm {{ $showRomanNumerals ? 'text-blue-500' : 'text-secondary' }} hover:text-primary transition-colors flex items-center space-x-2"
                    title="Show roman numeral analysis"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Analysis</span>
                </button>
                <button
                    wire:click="toggleVoiceLeading"
                    class="text-sm {{ $showVoiceLeading ? 'text-green-500' : 'text-secondary' }} hover:text-primary transition-colors flex items-center space-x-2"
                    title="Show voice leading animations"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span>Voice Leading</span>
                </button>
                <button
                    wire:click="optimizeVoiceLeading"
                    class="text-sm text-secondary hover:text-primary transition-colors flex items-center space-x-2"
                    title="Optimize inversions for smooth voice leading"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                    </svg>
                    <span>Optimize</span>
                </button>
            </div>
        </div>
        
        {{-- Roman Numeral Analysis --}}
        @if($showRomanNumerals)
            <div class="grid grid-cols-4 gap-4 mb-4">
                @foreach($chords as $position => $chord)
                    <div class="text-center">
                        <div class="bg-zinc-800 rounded-lg px-3 py-2 border border-zinc-700">
                            <span class="text-sm font-medium {{ $chord['tone'] ? 'text-blue-400' : 'text-tertiary' }}">
                                {{ $romanNumerals[$position] ?? '' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Chord Blocks --}}
        <div class="space-y-4">
            {{-- First row: all four chords with voice leading animations below each --}}
            <div class="grid grid-cols-4 gap-4">
                @foreach($chords as $pos => $ch)
                    <div class="space-y-2" data-chord-position="{{ $pos }}">
                        {{-- Chord Button --}}
                        <div 
                            wire:click="selectChord({{ $pos }})"
                            class="chord-block {{ $activePosition === $pos ? 'chord-block-active' : '' }} {{ $ch['is_blue_note'] ? 'ring-2 ring-purple-500' : '' }} relative group"
                        >
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                @if($ch['tone'])
                                    <div class="text-5xl lg:text-6xl font-bold text-center leading-none">
                                        {{ $ch['tone'] }}{{ $ch['semitone'] === 'minor' ? 'm' : ($ch['semitone'] === 'diminished' ? 'dim' : '') }}
                                    </div>
                                    @if($ch['inversion'] !== 'root')
                                        <div class="text-sm lg:text-base text-secondary text-center mt-1">{{ substr(ucfirst($ch['inversion']), 0, 3) }}</div>
                                    @endif
                                @else
                                    <div class="text-5xl lg:text-6xl text-tertiary text-center">+</div>
                                @endif
                            </div>
                            
                            {{-- Clear button --}}
                            @if($ch['tone'])
                                <button
                                    wire:click.stop="clearChord({{ $pos }})"
                                    class="absolute -top-2 -right-2 w-6 h-6 bg-zinc-700 hover:bg-red-600 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                >
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        
                        {{-- Mini Piano Display --}}
                        <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-2">
                            <livewire:chord-piano :chord="$ch" :position="$pos" :wire:key="'grid-piano-' . $pos" />
                        </div>
                        
                        {{-- Voice Leading Animation below this chord --}}
                        @if($showVoiceLeading)
                            @php
                                $nextChord = null;
                                $nextPosition = null;
                                
                                // Determine what chord comes next for this position
                                if ($pos < 4 && !empty($chords[$pos + 1]['tone'])) {
                                    $nextChord = $chords[$pos + 1];
                                    $nextPosition = $pos + 1;
                                } elseif ($pos == 4 && !empty($chords[1]['tone'])) {
                                    // Loop back to first chord
                                    $nextChord = $chords[1];
                                    $nextPosition = 1;
                                }
                            @endphp
                            
                            @if($ch['tone'] && $nextChord)
                                <div data-voice-position="{{ $pos }}">
                                    <livewire:voice-leading-animation 
                                        :fromChord="$ch" 
                                        :toChord="$nextChord" 
                                        :position="$pos"
                                        :nextPosition="$nextPosition"
                                        :wire:key="'voice-below-' . $pos . '-to-' . $nextPosition" 
                                    />
                                </div>
                            @else
                                {{-- Empty space to maintain layout --}}
                                <div class="h-16"></div>
                            @endif
                        @endif
                        
                        {{-- Beat indicator --}}
                        <div class="text-center text-xs text-tertiary">
                            Beat {{ ($pos - 1) * 2 + 1 }}-{{ ($pos - 1) * 2 + 2 }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    {{-- Chord Palette --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-secondary">Chord Palette</h3>
            @if($chords[$activePosition]['tone'])
                <div class="text-lg font-bold text-primary">
                    Selected: {{ $chords[$activePosition]['tone'] }}{{ $chords[$activePosition]['semitone'] === 'minor' ? 'm' : ($chords[$activePosition]['semitone'] === 'diminished' ? 'dim' : '') }}
                    @if($chords[$activePosition]['inversion'] !== 'root')
                        <span class="text-sm text-secondary ml-1">({{ ucfirst($chords[$activePosition]['inversion']) }})</span>
                    @endif
                </div>
            @endif
        </div>
        
        {{-- Root Notes --}}
        <div class="grid grid-cols-12 gap-2 mb-4">
            @foreach($tones as $tone)
                <button
                    wire:click="setChord('{{ $tone }}')"
                    class="chord-suggestion text-center {{ $chords[$activePosition]['tone'] === $tone ? 'bg-blue-600 text-white border-blue-500' : '' }}"
                >
                    {{ $tone }}
                </button>
            @endforeach
        </div>
        
        {{-- Chord Types --}}
        <div class="flex space-x-2 mb-4">
            <span class="text-xs text-tertiary">Type:</span>
            @foreach(['major' => '', 'minor' => 'm', 'diminished' => 'dim', 'augmented' => 'aug'] as $type => $suffix)
                <button
                    wire:click="setChord('{{ $chords[$activePosition]['tone'] ?? 'C' }}', '{{ $type }}')"
                    class="text-xs px-3 py-1 rounded transition-colors {{ $chords[$activePosition]['semitone'] === $type ? 'bg-blue-600 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-secondary hover:text-primary' }}"
                >
                    {{ ucfirst($type) }}
                </button>
            @endforeach
        </div>
        
        {{-- Inversion Controls --}}
        <div class="flex space-x-2">
            <span class="text-xs text-tertiary">Inversion:</span>
            @foreach(['root' => 'Root', 'first' => 'First', 'second' => 'Second'] as $inv => $label)
                <button
                    wire:click="setInversion('{{ $inv }}')"
                    class="text-xs px-3 py-1 rounded transition-colors {{ $chords[$activePosition]['inversion'] === $inv ? 'bg-blue-600 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-secondary hover:text-primary' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>
    
    
    {{-- Piano Display for Active Chord --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-4">
        <h3 class="text-sm font-medium text-secondary mb-3">Chord Piano</h3>
        <livewire:chord-piano :chord="$chords[$activePosition]" :position="$activePosition" wire:key="active-chord-piano-{{ $activePosition }}" />
    </div>
</div>