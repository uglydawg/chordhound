<x-app-layout>
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
                <livewire:chord-grid />
                
                {{-- Full Piano Display --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-lg p-6">
                    <livewire:chord-display />
                </div>

            <div class="flex justify-end space-x-4 print:hidden">
                <livewire:print-chord-sheet />
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
        });
    </script>
    
    {{-- Print styles --}}
    <style>
    @media print {
        /* Hide navigation and unnecessary elements */
        nav, .print\\:hidden, .bg-zinc-900.border-b.border-zinc-800 {
            display: none !important;
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
        }
        
        /* Hide unselected chord sections */
        .chord-item.print-hide {
            display: none !important;
        }
        
        /* Optimize layout for print */
        .p-6 {
            padding: 0.5rem !important;
        }
        
        .max-w-7xl {
            max-width: 100% !important;
        }
    }
    </style>
</x-app-layout>