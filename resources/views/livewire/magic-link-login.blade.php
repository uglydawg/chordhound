<div>
    @if($linkSent)
        <flux:card class="text-center p-6">
            <flux:icon.mail class="w-16 h-16 mx-auto text-green-500 mb-4" />
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