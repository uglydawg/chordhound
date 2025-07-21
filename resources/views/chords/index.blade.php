<x-layouts.app :title="__('Piano Chords')">
    <div class="min-h-screen">
        <div class="p-6">
            <div class="max-w-7xl mx-auto space-y-6">
                {{-- Chord Grid Editor (includes Chord Palette) --}}
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
        <div id="save-chord-set-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md transform transition-all">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-orange-100 dark:bg-orange-900/30 rounded-lg p-2">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Save Chord Progression</h3>
                </div>

                <form onsubmit="event.preventDefault(); 
                    const name = document.getElementById('chord-set-name').value;
                    const description = document.getElementById('chord-set-description').value;
                    if (name.trim()) {
                        Livewire.dispatch('save-chord-set', { name: name, description: description }); 
                        document.getElementById('save-chord-set-modal').classList.add('hidden');
                        document.getElementById('chord-set-name').value = '';
                        document.getElementById('chord-set-description').value = '';
                    }">
                    <div class="space-y-4">
                        <div>
                            <label for="chord-set-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="chord-set-name"
                                type="text"
                                placeholder="e.g., Jazz Blues in G"
                                required
                                autofocus
                                class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-sm 
                                       focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                                       bg-white dark:bg-zinc-700 text-gray-900 dark:text-white 
                                       placeholder-gray-400 dark:placeholder-gray-500"
                            />
                        </div>

                        <div>
                            <label for="chord-set-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Description <span class="text-sm text-gray-500">(Optional)</span>
                            </label>
                            <textarea
                                id="chord-set-description"
                                placeholder="Add notes about this progression..."
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg shadow-sm 
                                       focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                                       bg-white dark:bg-zinc-700 text-gray-900 dark:text-white 
                                       placeholder-gray-400 dark:placeholder-gray-500"
                            ></textarea>
                        </div>

                        {{-- Chord Preview --}}
                        <div class="bg-gray-50 dark:bg-zinc-900 rounded-lg p-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Chords to save:</p>
                            <div class="flex flex-wrap gap-2" id="chord-preview">
                                {{-- Will be populated by JavaScript --}}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" 
                                onclick="document.getElementById('save-chord-set-modal').classList.add('hidden')" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 
                                       bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 
                                       rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 
                                       transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white 
                                       bg-orange-600 border border-transparent rounded-lg 
                                       hover:bg-orange-700 focus:outline-none focus:ring-2 
                                       focus:ring-offset-2 focus:ring-orange-500 transition-colors">
                            Save Chord Set
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth

    <script>
        document.addEventListener('livewire:initialized', () => {
            let currentChords = [];
            
            // Listen for chord updates to keep track of current chords
            Livewire.on('chordsUpdated', (event) => {
                if (event.chords) {
                    currentChords = event.chords;
                }
            });
            
            Livewire.on('show-save-dialog', () => {
                const modal = document.getElementById('save-chord-set-modal');
                const preview = document.getElementById('chord-preview');
                const nameInput = document.getElementById('chord-set-name');
                
                // Clear and populate chord preview
                preview.innerHTML = '';
                Object.values(currentChords).forEach(chord => {
                    if (chord.tone) {
                        const chordName = chord.tone + (chord.semitone === 'minor' ? 'm' : (chord.semitone === 'diminished' ? 'dim' : ''));
                        const inversionText = chord.inversion !== 'root' ? ` (${chord.inversion.substr(0, 3)})` : '';
                        
                        const span = document.createElement('span');
                        span.className = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
                        span.textContent = chordName + inversionText;
                        preview.appendChild(span);
                    }
                });
                
                if (preview.children.length === 0) {
                    preview.innerHTML = '<span class="text-sm text-gray-500">No chords selected</span>';
                }
                
                modal.classList.remove('hidden');
                
                // Focus the name input after a small delay
                setTimeout(() => {
                    nameInput.focus();
                }, 100);
            });

            Livewire.on('chord-set-saved', (event) => {
                document.getElementById('save-chord-set-modal').classList.add('hidden');
                // Clear form fields
                document.getElementById('chord-set-name').value = '';
                document.getElementById('chord-set-description').value = '';
                
                // Show success notification
                showNotification('Chord set saved successfully!', 'success');
            });
            
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                
                const icon = type === 'success' ? 
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                
                notification.innerHTML = `${icon}<span class="font-medium">${message}</span>`;
                document.body.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-x-full');
                }, 10);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }
            
            // Close modal on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.getElementById('save-chord-set-modal').classList.add('hidden');
                }
            });
            
            // Close modal on background click
            document.getElementById('save-chord-set-modal').addEventListener('click', (e) => {
                if (e.target === e.currentTarget) {
                    e.currentTarget.classList.add('hidden');
                }
            });
            
            // Request chord state after all components are loaded
            setTimeout(() => {
                Livewire.dispatch('request-chord-state');
            }, 100);
            
            // Listen for chord updates to ensure piano player stays in sync
            Livewire.on('chordsUpdated', (event) => {
                console.log('Chords updated in main view:', event);
            });
        });
    </script>
    
    {{-- Print styles --}}
    <style>
    @media print {
        /* Hide specific components and elements */
        nav,
        .bg-zinc-900.border-b,
        [wire\\:id*="chord-grid"],
        [wire\\:id*="piano-player"],
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