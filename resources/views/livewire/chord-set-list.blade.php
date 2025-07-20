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
                    
                    <div class="mt-6 flex space-x-2">
                        <flux:button 
                            href="{{ route('chords.edit', $chordSet) }}" 
                            variant="primary"
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