<div>
    @if($chordSets->isEmpty())
        <flux:card class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
            </svg>
            <flux:heading size="lg" class="mb-2">No chord sets yet</flux:heading>
            <flux:text class="text-gray-600 mb-6">Create your first chord set to get started.</flux:text>
            <flux:button href="{{ route('chords.index') }}" variant="primary">
                Create Chord Set
            </flux:button>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($chordSets as $chordSet)
                <flux:card class="p-6">
                    <flux:heading size="lg">{{ $chordSet->name }}</flux:heading>
                    
                    @if($chordSet->description)
                        <flux:text class="text-gray-600 mt-2">{{ $chordSet->description }}</flux:text>
                    @endif
                    
                    <div class="mt-4 text-sm text-gray-500">
                        {{ $chordSet->chords->count() }} chords • 
                        Created {{ $chordSet->created_at->diffForHumans() }}
                    </div>
                    
                    {{-- Display chords preview --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($chordSet->chords->take(4) as $chord)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                {{ $chord->tone }}{{ $chord->semitone === 'minor' ? 'm' : ($chord->semitone === 'diminished' ? 'dim' : '') }}
                                @if($chord->inversion !== 'root')
                                    <span class="text-[10px] ml-0.5">({{ substr($chord->inversion, 0, 3) }})</span>
                                @endif
                            </span>
                        @endforeach
                        @if($chordSet->chords->count() > 4)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                +{{ $chordSet->chords->count() - 4 }} more
                            </span>
                        @endif
                    </div>
                    
                    <div class="mt-6 flex space-x-2">
                        <flux:button 
                            href="{{ route('chords.index', ['load' => $chordSet->id]) }}" 
                            variant="primary"
                            size="sm"
                        >
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Play
                        </flux:button>
                        
                        <flux:button 
                            href="{{ route('chords.edit', $chordSet) }}" 
                            variant="ghost"
                            size="sm"
                        >
                            Edit
                        </flux:button>
                        
                        <flux:button 
                            wire:click="deleteChordSet({{ $chordSet->id }})"
                            wire:confirm="Are you sure you want to delete this chord set?"
                            variant="ghost"
                            size="sm"
                        >
                            Delete
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $chordSets->links() }}
        </div>
    @endif
</div>