<x-layouts.app>
    <livewire:math-chord-test />

    @push('scripts')
        <script src="{{ asset('js/multi-instrument-player.js') }}"></script>
    @endpush
</x-layouts.app>