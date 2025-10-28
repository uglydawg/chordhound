<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12"
         x-data="{
             pianoPlayer: null,
             init() {
                 console.log('Direct test init');
                 if (window.MultiInstrumentPlayer) {
                     this.pianoPlayer = new window.MultiInstrumentPlayer();
                     console.log('Piano player initialized');
                 }
             },
             testPlay() {
                 console.log('Test play clicked');
                 if (!this.pianoPlayer || !this.pianoPlayer.isLoaded) {
                     console.error('Piano not ready');
                     alert('Piano samples still loading, please wait...');
                     return;
                 }

                 console.log('Playing C major chord');
                 const notes = ['C4', 'E4', 'G4'];

                 // Highlight keys
                 notes.forEach(note => {
                     const keyId = 'key-' + note;
                     const key = document.getElementById(keyId);
                     if (key) {
                         key.classList.add('pressed', 'active');
                         console.log('Key highlighted:', note);
                     }
                 });

                 // Play audio
                 this.pianoPlayer.playChord(notes, 2.0);
                 console.log('Audio triggered');

                 // Remove highlight after 2 seconds
                 setTimeout(() => {
                     notes.forEach(note => {
                         const keyId = 'key-' + note;
                         const key = document.getElementById(keyId);
                         if (key) {
                             key.classList.remove('pressed', 'active');
                         }
                     });
                     console.log('Keys un-highlighted');
                 }, 2000);
             }
         }">
        <h1 class="text-3xl font-bold mb-6">Direct Piano Test (No Livewire)</h1>

        <button @click="testPlay()"
                class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md mb-8">
            Play C Major Chord
        </button>

        <div class="mb-4 text-sm text-gray-600">
            Status: <span x-text="pianoPlayer?.isLoaded ? 'Ready' : 'Loading...'"></span>
        </div>

        <!-- Mini Piano -->
        <livewire:piano-player :chords="[]" />
    </div>

    @push('scripts')
        <script src="{{ asset('js/multi-instrument-player.js') }}"></script>
    @endpush
</x-layouts.app>
