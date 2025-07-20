<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <flux:heading size="xl">My Chord Sets</flux:heading>
            <flux:button href="{{ route('chords.index') }}" variant="primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Chord Set
            </flux:button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:chord-set-list />
        </div>
    </div>
</x-app-layout>