<div>
    {{-- Chord Timeline/Grid --}}
    <div class="timeline-grid p-2">
        {{-- Key and Progression Selectors --}}
        <div class="space-y-3 mb-3 pb-2 border-b border-zinc-800">
            {{-- Key Selection Line --}}
            <div class="flex flex-wrap items-center gap-2">
                <label class="text-xs font-medium text-secondary mr-2">Key:</label>
                <div class="flex space-x-1">
                    @foreach($availableKeys as $key)
                        <button
                            wire:click="setKey('{{ $key }}')"
                            class="px-2 py-0.5 text-xs font-medium rounded transition-all
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
                
                <div class="h-4 w-px bg-zinc-700 mx-2"></div>
                
                <div class="flex space-x-1">
                    <button
                        wire:click="setKeyType('major')"
                        class="px-2 py-0.5 text-xs font-medium rounded transition-all
                            {{ $selectedKeyType === 'major' 
                                ? 'bg-blue-600 text-white border border-blue-500' 
                                : 'bg-zinc-800 text-gray-300 border border-zinc-700 hover:bg-zinc-700 hover:text-white' }}"
                    >
                        Major
                    </button>
                    <button
                        wire:click="setKeyType('minor')"
                        class="px-2 py-0.5 text-xs font-medium rounded transition-all
                            {{ $selectedKeyType === 'minor' 
                                ? 'bg-blue-600 text-white border border-blue-500' 
                                : 'bg-zinc-800 text-gray-300 border border-zinc-700 hover:bg-zinc-700 hover:text-white' }}"
                    >
                        Minor
                    </button>
                </div>
            </div>
            
            {{-- Progression Selection Line --}}
            <div class="flex flex-wrap items-center gap-2">
                <label class="text-xs font-medium text-secondary">Progression:</label>
                <select
                    wire:change="setProgression($event.target.value)"
                    class="bg-zinc-800 border border-zinc-700 text-white rounded px-2 py-0.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[200px]"
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
                    <div class="text-xs text-blue-400">
                        = {{ collect($transposedChords)->map(fn($c) => $c['tone'] . ($c['semitone'] === 'minor' ? 'm' : ($c['semitone'] === 'diminished' ? 'dim' : '')))->join(' - ') }}
                    </div>
                @endif
            </div>
        </div>
        
        <div class="flex items-center justify-between mb-2">
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
                    @auth
                        onclick="printChordSheet()"
                    @else
                        onclick="window.location.href = '{{ route('login') }}'"
                    @endauth
                    class="text-sm bg-zinc-700 text-white px-4 py-2 rounded-lg hover:bg-zinc-600 transition-colors flex items-center space-x-2"
                    title="Print chord sheet"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Print Chord Sheet</span>
                </button>
                @auth
                    <button
                        wire:click="$dispatch('show-save-dialog')"
                        class="text-sm bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors flex items-center space-x-2"
                        title="Save chord progression"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        <span>Save</span>
                    </button>
                @endauth
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
        {{-- Chord Blocks --}}
        <div class="space-y-2">
            {{-- First row: all four chords with voice leading animations below each --}}
            <div class="grid grid-cols-4 gap-3">
                @foreach($chords as $pos => $ch)
                    <div class="space-y-2" data-chord-position="{{ $pos }}">
                        {{-- Chord Button --}}
                        <div 
                            wire:click="selectChord({{ $pos }})"
                            role="button"
                            tabindex="0"
                            aria-label="Select {{ $ch['tone'] ? $ch['tone'] . ' ' . ($ch['semitone'] === 'minor' ? 'minor' : ($ch['semitone'] === 'diminished' ? 'diminished' : 'major')) . ' chord, ' . $ch['inversion'] . ' position' : 'empty chord slot' }}"
                            aria-pressed="{{ $activePosition === $pos ? 'true' : 'false' }}"
                            @keydown.enter="$wire.selectChord({{ $pos }})"
                            @keydown.space.prevent="$wire.selectChord({{ $pos }})"
                            class="relative rounded border-2 {{ $playingPosition === $pos ? 'border-orange-500 bg-orange-600 animate-pulse' : ($activePosition === $pos ? 'border-blue-500 bg-blue-600' : ($ch['is_blue_note'] ? 'border-purple-500 bg-zinc-800' : 'border-zinc-700 bg-zinc-800')) }} hover:border-blue-400 transition-all cursor-pointer p-2 min-h-[80px] flex group select-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-zinc-900"
                        >
                            {{-- Left side content --}}
                            <div class="flex-1 flex flex-col justify-between">
                                {{-- Roman Numeral --}}
                                @if($ch['tone'] && $showRomanNumerals && isset($romanNumerals[$pos]))
                                    <div class="text-center mb-0.5">
                                        <span class="text-xs font-medium select-none {{ $activePosition === $pos ? 'text-blue-200' : 'text-blue-400' }}">
                                            {{ $romanNumerals[$pos] }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mb-0.5">&nbsp;</div>
                                @endif
                                
                                {{-- Chord Name --}}
                                <div class="flex-1 flex items-center justify-center">
                                    @if($ch['tone'])
                                        <div class="text-center">
                                            <div class="text-lg font-bold select-none {{ $activePosition === $pos ? 'text-white' : 'text-white' }}">
                                                {{ $ch['tone'] }}{{ $ch['semitone'] === 'minor' ? 'm' : ($ch['semitone'] === 'diminished' ? 'dim' : '') }}
                                            </div>
                                            <div class="text-xs select-none {{ $activePosition === $pos ? 'text-blue-200' : 'text-zinc-400' }} mt-0.5">
                                                {{ ucfirst($ch['inversion']) }} Inversion
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-lg text-zinc-600">+</div>
                                    @endif
                                </div>
                                
                                {{-- Chord Notes --}}
                                @if($ch['tone'])
                                    @php
                                        $chordService = app(\App\Services\ChordService::class);
                                        // Use a public method to get notes with octaves
                                        $notesWithOctaves = $chordService->getChordNotesForDisplay(
                                            $ch['tone'],
                                            $ch['semitone'] ?? 'major',
                                            $ch['inversion'] ?? 'root'
                                        );
                                    @endphp
                                    <div class="text-center mt-0.5">
                                        <span class="text-xs select-none {{ $activePosition === $pos ? 'text-blue-200' : 'text-zinc-400' }}">
                                            {{ implode(', ', $notesWithOctaves) }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-0.5">&nbsp;</div>
                                @endif
                            </div>
                            
                            {{-- Inversion Controls (vertically stacked on the right) --}}
                            @if($ch['tone'])
                                <div class="flex flex-col justify-center space-y-2 ml-2" wire:click.stop>
                                    @foreach(['root' => 'R', 'first' => 'I', 'second' => 'II'] as $inv => $label)
                                        <button
                                            wire:click="setChordInversion({{ $pos }}, '{{ $inv }}')"
                                            class="relative text-xs w-8 h-8 flex items-center justify-center rounded transition-all transform {{ $ch['inversion'] === $inv ? 'bg-gradient-to-b from-blue-400 to-blue-600 text-white font-bold shadow-lg scale-105 border-b-4 border-blue-700' : 'bg-gradient-to-b from-zinc-600 to-zinc-700 text-gray-200 hover:from-zinc-500 hover:to-zinc-600 hover:text-white border-b-4 border-zinc-800 hover:translate-y-[1px] hover:border-b-2' }} active:translate-y-[2px] active:border-b-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 focus:ring-offset-zinc-900"
                                            title="{{ ucfirst($inv) }} Inversion"
                                            aria-label="{{ ucfirst($inv) }} inversion"
                                            aria-pressed="{{ $ch['inversion'] === $inv ? 'true' : 'false' }}"
                                        >
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            
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
                        
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    {{-- Chord Palette --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-2">
        <div class="flex items-center justify-between mb-1">
            <h3 class="text-xs font-medium text-secondary">Chord Palette</h3>
            @if($chords[$activePosition]['tone'])
                <div class="text-sm font-bold text-primary">
                    Selected: {{ $chords[$activePosition]['tone'] }}{{ $chords[$activePosition]['semitone'] === 'minor' ? 'm' : ($chords[$activePosition]['semitone'] === 'diminished' ? 'dim' : '') }}
                    @if($chords[$activePosition]['inversion'] !== 'root')
                        <span class="text-xs text-secondary ml-1">({{ ucfirst($chords[$activePosition]['inversion']) }})</span>
                    @endif
                </div>
            @endif
        </div>
        
        {{-- Root Notes --}}
        <div class="grid grid-cols-12 gap-1 mb-1">
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
        <div class="flex space-x-1">
            <span class="text-xs text-tertiary">Type:</span>
            @foreach(['major' => '', 'minor' => 'm', 'diminished' => 'dim', 'augmented' => 'aug'] as $type => $suffix)
                <button
                    wire:click="setChord('{{ $chords[$activePosition]['tone'] ?? 'C' }}', '{{ $type }}')"
                    class="text-xs px-2 py-0.5 rounded transition-colors {{ $chords[$activePosition]['semitone'] === $type ? 'bg-blue-600 text-white' : 'bg-zinc-800 hover:bg-zinc-700 text-secondary hover:text-primary' }}"
                >
                    {{ ucfirst($type) }}
                </button>
            @endforeach
        </div>
    </div>
    
    {{-- Piano Player - Main Feature --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                </svg>
                Piano Player
            </h2>
        </div>
        <div>
            <livewire:piano-player />
        </div>
    </div>
    
    <script>
        function printChordSheet() {
            window.print();
        }
    </script>
</div>