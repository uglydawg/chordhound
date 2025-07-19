<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">Edit: {{ $chordSet->name }}</flux:heading>
            <flux:button href="{{ route('chords.my-sets') }}" variant="ghost">
                Back to My Sets
            </flux:button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <livewire:chord-selector :chord-set-id="$chordSet->id" />
            <livewire:chord-display />
            
            <div class="flex justify-end space-x-4 print:hidden">
                <flux:button onclick="window.print()" variant="ghost">
                    <flux:icon.printer class="w-4 h-4" />
                    Print Chord Sheet
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Save Dialog Modal --}}
    <flux:modal name="save-chord-set">
        <flux:card class="w-full max-w-md">
            <flux:heading size="lg">Update Chord Set</flux:heading>
            
            <form wire:submit="$dispatch('save-chord-set', { 
                name: document.getElementById('chord-set-name').value,
                description: document.getElementById('chord-set-description').value 
            })">
                <div class="space-y-4 mt-4">
                    <flux:input 
                        id="chord-set-name"
                        label="Name"
                        value="{{ $chordSet->name }}"
                        required
                    />
                    
                    <flux:textarea 
                        id="chord-set-description"
                        label="Description (Optional)"
                        rows="3"
                    >{{ $chordSet->description }}</flux:textarea>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <flux:button type="button" variant="ghost" onclick="document.querySelector('[data-flux-modal=save-chord-set]').close()">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Update
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </flux:modal>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-save-dialog', () => {
                document.querySelector('[data-flux-modal=save-chord-set]').showModal();
            });
            
            Livewire.on('chord-set-saved', () => {
                document.querySelector('[data-flux-modal=save-chord-set]').close();
            });
        });
    </script>
</x-app-layout>