<div class="space-y-4">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($chords as $position => $chord)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-sm p-4 {{ $chord['is_blue_note'] ? 'ring-2 ring-blue-500' : '' }}">
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-medium">Chord {{ $position }}</h3>
                        @if($chord['is_blue_note'])
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                Blue Note
                            </span>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tone</label>
                        <select 
                            wire:model.live="chords.{{ $position }}.tone"
                            wire:change="updateChord({{ $position }}, 'tone', $event.target.value)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600"
                        >
                            <option value="">Select tone</option>
                            @foreach($tones as $tone)
                                <option value="{{ $tone }}">{{ $tone }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if(!empty($chord['tone']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semitone</label>
                            <select 
                                wire:model.live="chords.{{ $position }}.semitone"
                                wire:change="updateChord({{ $position }}, 'semitone', $event.target.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600"
                            >
                                @foreach($semitones as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Inversion</label>
                            <select 
                                wire:model.live="chords.{{ $position }}.inversion"
                                wire:change="updateChord({{ $position }}, 'inversion', $event.target.value)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600"
                            >
                                @foreach($inversions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button 
                            wire:click="clearChord({{ $position }})" 
                            type="button"
                            class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-zinc-700 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-600"
                        >
                            Clear
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @auth
        <div class="flex justify-end space-x-2">
            <button 
                wire:click="$dispatch('show-save-dialog')"
                type="button"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Save Chord Set
            </button>
        </div>
    @endauth
</div>