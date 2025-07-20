<div>
    @if($linkSent)
        <flux:card class="text-center p-6">
            <svg class="w-16 h-16 mx-auto text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <flux:heading size="lg">Check your email!</flux:heading>
            <flux:text class="mt-2">We've sent a login link to {{ $email }}</flux:text>
            <flux:text class="text-sm text-gray-600 mt-4">The link will expire in 30 minutes.</flux:text>
        </flux:card>
    @else
        <form wire:submit="sendMagicLink" class="space-y-4">
            <flux:input
                wire:model="email"
                type="email"
                label="Email address"
                placeholder="email@example.com"
                required
                autofocus
            />
            
            <flux:button type="submit" variant="primary" class="w-full">
                Send Magic Link
            </flux:button>
        </form>
    @endif
</div>