<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm p-6 border border-zinc-200 dark:border-zinc-700">
    <div class="text-center mb-6">
        <h2 class="text-lg font-semibold text-orange-600 dark:text-orange-300 mb-2">
            ❤️ Support ChordHound
        </h2>
        <p class="text-sm text-zinc-600 dark:text-zinc-300">
            Help keep ChordHound free and support future development
        </p>
    </div>

    <div class="space-y-4">
        <!-- Preset Amounts -->
        <div class="grid grid-cols-3 gap-2">
            @foreach($amounts as $amount)
                <button 
                    type="button"
                    wire:click="selectAmount({{ $amount }})"
                    class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors
                        {{ $selectedAmount === $amount && !$showCustomInput 
                            ? 'bg-orange-50 dark:bg-orange-900/30 border-orange-200 dark:border-orange-600 text-orange-700 dark:text-orange-200' 
                            : 'bg-white dark:bg-zinc-700 border-zinc-200 dark:border-zinc-600 text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-600' 
                        }}"
                >
                    ${{ $amount }}
                </button>
            @endforeach
        </div>

        <!-- Custom Amount Option -->
        <div class="flex items-center justify-center">
            <button 
                type="button"
                wire:click="showCustom"
                class="text-sm text-orange-600 dark:text-orange-300 hover:text-orange-700 dark:hover:text-orange-200 font-medium"
            >
                Enter custom amount
            </button>
        </div>

        <!-- Custom Amount Input -->
        @if($showCustomInput)
            <div class="space-y-2">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-200">
                    Custom Amount (USD)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-zinc-500 dark:text-zinc-300 text-sm">$</span>
                    </div>
                    <input 
                        type="number" 
                        min="1" 
                        step="1"
                        wire:model.live="customAmount"
                        class="block w-full pl-7 pr-3 py-2 border border-zinc-200 dark:border-zinc-600 rounded-lg 
                               bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100
                               placeholder-zinc-500 dark:placeholder-zinc-300
                               focus:ring-2 focus:ring-orange-500 focus:border-orange-500 
                               dark:focus:ring-orange-400 dark:focus:border-orange-400"
                        placeholder="Enter amount"
                    >
                </div>
            </div>
        @endif

        <!-- Error Display -->
        @error('amount')
            <div class="text-sm text-red-600 dark:text-red-400">
                {{ $message }}
            </div>
        @enderror

        @error('donation')
            <div class="text-sm text-red-600 dark:text-red-400">
                {{ $message }}
            </div>
        @enderror

        <!-- Donate Button -->
        <button 
            wire:click="donate"
            class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 
                   text-white font-medium py-3 px-4 rounded-lg transition-colors
                   disabled:opacity-50 disabled:cursor-not-allowed"
            {{ (!$selectedAmount && !$customAmount) || ($customAmount && $customAmount < 1) ? 'disabled' : '' }}
        >
            @if($selectedAmount || $customAmount)
                Donate ${{ $selectedAmount ?: $customAmount }}
            @else
                Choose Amount to Donate
            @endif
        </button>

        <!-- Secure Payment Notice -->
        <div class="text-center">
            <p class="text-xs text-zinc-500 dark:text-zinc-300">
                🔒 Secure payment powered by Stripe
            </p>
        </div>
    </div>
</div>