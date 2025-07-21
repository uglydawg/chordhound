<x-layouts.app :title="__('Piano Chords')">
    <div class="min-h-screen">
        {{-- MIDI Player Bar --}}
        <div class="bg-zinc-900 border-b border-zinc-800 px-6 py-4">
            <div class="max-w-7xl mx-auto">
                <livewire:midi-player />
            </div>
        </div>

        <div class="p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                {{-- Chord Grid Editor --}}
                <livewire:chord-grid :chord-set-id="$chordSetId ?? null" />
                
                {{-- Chords Display (2x2 Grid) --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6 chords-section">
                    <livewire:chord-display wire:key="chord-display-main" />
                </div>
            </div>
        </div>
    </div>

    {{-- Save Dialog Modal --}}
    @auth
        <div id="save-chord-set-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-primary mb-4">Save Chord Set</h3>

                <form onsubmit="event.preventDefault(); Livewire.dispatch('save-chord-set', {
                    name: document.getElementById('chord-set-name').value,
                    description: document.getElementById('chord-set-description').value
                }); document.getElementById('save-chord-set-modal').classList.add('hidden');">
                    <div class="space-y-4 mt-4">
                        <div>
                            <label for="chord-set-name" class="block text-sm font-medium text-secondary">Name</label>
                            <input
                                id="chord-set-name"
                                type="text"
                                placeholder="My Chord Set"
                                required
                                class="mt-1 block w-full rounded-md border-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-zinc-700 text-primary placeholder-tertiary"
                            />
                        </div>

                        <div>
                            <label for="chord-set-description" class="block text-sm font-medium text-secondary">Description (Optional)</label>
                            <textarea
                                id="chord-set-description"
                                placeholder="Describe your chord set..."
                                rows="3"
                                class="mt-1 block w-full rounded-md border-zinc-700 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm bg-zinc-700 text-primary placeholder-tertiary"
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('save-chord-set-modal').classList.add('hidden')" class="px-4 py-2 border border-zinc-700 rounded-md shadow-sm text-sm font-medium text-secondary hover:text-primary bg-zinc-800 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-save-dialog', () => {
                document.getElementById('save-chord-set-modal').classList.remove('hidden');
            });

            Livewire.on('chord-set-saved', () => {
                document.getElementById('save-chord-set-modal').classList.add('hidden');
            });
            
            // Request chord state after all components are loaded
            setTimeout(() => {
                Livewire.dispatch('request-chord-state');
            }, 100);
        });
    </script>
    
    {{-- Print styles --}}
    <style>
    @media print {
        /* Hide specific components and elements */
        nav,
        .bg-zinc-900.border-b,
        [wire\\:id*="chord-grid"],
        [wire\\:id*="midi-player"],
        .print\\:hidden,
        #save-chord-set-modal {
            display: none !important;
        }
        
        /* Reset page styles for print */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: white !important;
            height: auto !important;
            overflow: visible !important;
        }
        
        /* Hide all content except chords section */
        .min-h-screen > .bg-zinc-900,
        .min-h-screen > .p-6 > .max-w-7xl > :not(.chords-section) {
            display: none !important;
        }
        
        /* Reset container styles */
        .min-h-screen,
        .p-6,
        .max-w-7xl {
            min-height: auto !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
            background: transparent !important;
        }
        
        /* Style the chords section for print */
        .chords-section {
            border: none !important;
            padding: 1rem !important;
            background: white !important;
            margin: 0 !important;
        }
        
        /* Ensure all child elements are visible with proper display types */
        .chords-section * {
            visibility: visible !important;
        }
        
        /* Maintain grid and flex layouts */
        .chords-section .grid {
            display: grid !important;
        }
        
        .chords-section .flex {
            display: flex !important;
        }
        
        /* Reset background colors for print */
        body, .bg-zinc-950, .bg-zinc-900, .bg-zinc-800 {
            background-color: white !important;
        }
        
        /* Make text more print-friendly */
        .text-primary, .text-secondary, .text-tertiary {
            color: black !important;
        }
        
        /* Add border to chord items for better visibility */
        .chord-item {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
            background: white !important;
        }
        
        /* Hide unselected chord sections */
        .chord-item.print-hide {
            display: none !important;
        }
        
        /* Hide checkboxes and buttons in print */
        input[type="checkbox"],
        button {
            display: none !important;
        }
        
        /* Optimize layout for print */
        .p-6 {
            padding: 0 !important;
        }
        
        .p-4 {
            padding: 0.5rem !important;
        }
        
        .max-w-7xl {
            max-width: 100% !important;
        }
        
        /* Make the 2x2 grid fill the page */
        .grid.grid-cols-2 {
            gap: 1rem !important;
        }
    }
    </style>
</x-layouts.app>