<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">🎹 Canvas 2D Piano Test</h1>
            <p class="text-gray-600">Testing high-performance Canvas-based piano keyboard rendering</p>
        </div>

        <!-- Canvas Piano -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Piano Keyboard (C3 - B5)</h2>

            <div class="border-2 border-gray-300 rounded-lg overflow-hidden bg-gray-50">
                <canvas
                    id="piano-canvas"
                    class="w-full"
                    style="height: 180px;"
                    x-data="pianoCanvas()"
                    x-init="init()"
                    wire:ignore
                ></canvas>
            </div>

            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Active Notes:</strong>
                    @if(empty($activeNotes))
                        <span class="text-gray-400">None</span>
                    @else
                        <span class="font-mono text-blue-600">{{ implode(', ', $activeNotes) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Test Controls -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Single Hand Chords (Legacy API)</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($testChords as $name => $notes)
                    <button
                        wire:click="setChord('{{ $name }}')"
                        class="px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-medium transition-colors"
                    >
                        {{ $name }}
                    </button>
                @endforeach

                <button
                    wire:click="clearChord"
                    class="px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors"
                >
                    Clear All
                </button>
            </div>
        </div>

        <!-- Two-Handed Examples -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Two-Handed Examples (New API)</h2>
            <p class="text-sm text-gray-600 mb-4">
                <span class="inline-block w-4 h-4 bg-blue-500 rounded mr-2"></span> Left Hand (Bass)
                <span class="inline-block w-4 h-4 bg-green-500 rounded ml-4 mr-2"></span> Right Hand (Treble)
                <span class="inline-block w-4 h-4 bg-purple-500 rounded ml-4 mr-2"></span> Both Hands
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($twoHandedExamples as $name => $example)
                    <button
                        wire:click="setTwoHanded('{{ $name }}')"
                        class="px-4 py-3 bg-gradient-to-r from-blue-500 via-purple-500 to-green-500 hover:from-blue-600 hover:via-purple-600 hover:to-green-600 text-white rounded-lg font-medium transition-colors"
                    >
                        {{ $name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Performance Info -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Features</h2>

            <div class="space-y-2 text-sm text-gray-700">
                <p>✅ <strong>Canvas Rendering:</strong> Direct pixel manipulation (~16ms updates)</p>
                <p>✅ <strong>3D Visual Effects:</strong> Realistic piano key depth and shadows</p>
                <p>🎹 <strong>Real Piano Samples:</strong> 88 high-quality MP3 piano recordings (C1-C8)</p>
                <p>✅ <strong>Tone.js Audio:</strong> Professional sampler with polyphonic playback</p>
                <p>🎨 <strong>Two-Handed Support:</strong> Separate colors for left (blue) and right (green) hands</p>
                <p>✅ <strong>High-DPI Support:</strong> Automatic scaling for Retina displays</p>
                <p>✅ <strong>Event Handling:</strong> Efficient click detection with layering</p>
                <p>✅ <strong>Touch Support:</strong> Mobile-friendly touch interactions</p>
                <p>✅ <strong>Responsive:</strong> Auto-resize on window changes</p>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">How to Test</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>🎵 <strong>Click chord buttons</strong> to play and visualize full chords</li>
                <li>🎹 <strong>Click individual piano keys</strong> to play single notes and toggle them on/off</li>
                <li>👀 <strong>Watch the 3D effects</strong> - keys have realistic depth and lighting</li>
                <li>🔊 <strong>Listen to the audio</strong> - real piano samples, not synthesized</li>
                <li>📱 <strong>Test on mobile</strong> for touch support</li>
                <li>↔️ <strong>Resize your browser</strong> to test responsiveness</li>
            </ul>
        </div>
    </div>
</div>

@script
<script>
    Alpine.data('pianoCanvas', () => ({
        piano: null,

        async init() {
            // Wait for next tick to ensure canvas is in DOM
            await this.$nextTick();

            // Initialize piano with custom options using globally available PianoCanvas
            this.piano = new window.PianoCanvas(this.$el, {
                startNote: 'C3',
                endNote: 'B5',
                whiteKeyWidth: 40,
                whiteKeyHeight: 150,
                blackKeyWidth: 24,
                blackKeyHeight: 100,
                activeColor: '#3B82F6',
                activeBlackColor: '#60A5FA',
            });

            // Listen for Livewire chord-changed events (Legacy API)
            Livewire.on('chord-changed', (event) => {
                const notes = event.notes || [];
                this.piano.setActiveNotes(notes);
            });

            // Listen for two-handed piano events (New API)
            Livewire.on('set-both-hands', (event) => {
                console.log('🎹 set-both-hands event:', event);
                const leftNotes = event.leftNotes || [];
                const rightNotes = event.rightNotes || [];
                this.piano.setBothHands(leftNotes, rightNotes);
            });

            Livewire.on('clear-all-hands', () => {
                console.log('🎹 clear-all-hands event');
                this.piano.clearAll();
            });

            // Listen for audio playback events
            Livewire.on('play-chord', (event) => {
                console.log('🎵 play-chord event received:', event);
                const notes = event.notes || [];
                if (notes.length > 0) {
                    console.log('Playing chord with notes:', notes);
                    window.pianoAudio.playChord(notes);
                } else {
                    console.warn('No notes to play in chord');
                }
            });

            Livewire.on('play-note', (event) => {
                console.log('🎹 play-note event received:', event);
                const note = event.note;
                if (note) {
                    console.log('Playing single note:', note);
                    window.pianoAudio.playNote(note);
                } else {
                    console.warn('No note provided');
                }
            });

            Livewire.on('stop-audio', () => {
                console.log('🛑 stop-audio event received');
                window.pianoAudio.stopAll();
            });

            // Handle piano key clicks
            this.$el.addEventListener('piano-key-click', (e) => {
                this.$wire.handleKeyClick(e.detail.note);
            });
        },

        destroy() {
            if (this.piano) {
                this.piano.destroy();
            }
        }
    }));
</script>
@endscript
