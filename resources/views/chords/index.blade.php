<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-xl font-semibold">Piano Chord Generator</h1>
            @auth
                <a href="{{ route('chords.my-sets') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                    My Chord Sets
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <livewire:chord-selector />
            <livewire:chord-display />

            <div class="flex justify-end space-x-4 print:hidden">
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-zinc-700 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Chord Sheet
                </button>
            </div>
        </div>
    </div>

    {{-- Save Dialog Modal --}}
    @auth
        <div id="save-chord-set-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium mb-4">Save Chord Set</h3>

                <form onsubmit="event.preventDefault(); Livewire.dispatch('save-chord-set', {
                    name: document.getElementById('chord-set-name').value,
                    description: document.getElementById('chord-set-description').value
                }); document.getElementById('save-chord-set-modal').classList.add('hidden');">
                    <div class="space-y-4 mt-4">
                        <div>
                            <label for="chord-set-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input
                                id="chord-set-name"
                                type="text"
                                placeholder="My Chord Set"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600"
                            />
                        </div>

                        <div>
                            <label for="chord-set-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description (Optional)</label>
                            <textarea
                                id="chord-set-description"
                                placeholder="Describe your chord set..."
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-zinc-700 dark:border-zinc-600"
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('save-chord-set-modal').classList.add('hidden')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-zinc-700 dark:text-gray-300 dark:border-zinc-600 dark:hover:bg-zinc-600">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
</x-app-layout>