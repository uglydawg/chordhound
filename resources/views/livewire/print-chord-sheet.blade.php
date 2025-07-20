<div>
    <button 
        onclick="printChordSheet()"
        class="inline-flex items-center px-4 py-2 border border-zinc-700 rounded-md shadow-sm text-sm font-medium text-secondary hover:text-primary bg-zinc-800 hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Print Chord Sheet
    </button>
    
    <script>
        window.selectedChordsForPrint = @json($selectedChords);
        
        function printChordSheet() {
            // Apply print-specific classes to hide unselected chords
            document.querySelectorAll('[data-chord-position]').forEach(el => {
                const position = parseInt(el.dataset.chordPosition);
                if (!window.selectedChordsForPrint.includes(position)) {
                    el.classList.add('print-hide');
                } else {
                    el.classList.remove('print-hide');
                }
            });
            
            // Hide voice leading animations for unselected chords
            document.querySelectorAll('[data-voice-position]').forEach(el => {
                const position = parseInt(el.dataset.voicePosition);
                if (!window.selectedChordsForPrint.includes(position)) {
                    el.classList.add('print-hide');
                } else {
                    el.classList.remove('print-hide');
                }
            });
            
            // Trigger print
            window.print();
        }
        
        // Update selected chords when changed
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('selected-chords-updated', (event) => {
                window.selectedChordsForPrint = event.selectedChords;
            });
        });
    </script>
</div>