<div>
    @if($codeSent)
        <form wire:submit="verifyCode" class="space-y-4">
            <flux:text class="text-center">We've sent a 6-digit code to {{ $email }}</flux:text>
            
            <flux:input
                wire:model="code"
                type="text"
                label="Enter 6-digit code"
                placeholder="000000"
                maxlength="6"
                pattern="[0-9]{6}"
                autocomplete="one-time-code"
                required
                autofocus
                class="text-center text-2xl tracking-widest"
            />
            
            @if($error)
                <flux:alert variant="danger">{{ $error }}</flux:alert>
            @endif
            
            <flux:button type="submit" variant="primary" class="w-full">
                Verify Code
            </flux:button>
            
            <div class="text-center space-y-2">
                <flux:button wire:click="resendCode" variant="ghost" size="sm">
                    Send new code
                </flux:button>
                <flux:text class="text-xs text-gray-600">Code expires in 10 minutes</flux:text>
            </div>
        </form>
    @else
        <form wire:submit="sendCode" class="space-y-4">
            <flux:input
                wire:model="email"
                type="email"
                label="Email address"
                placeholder="email@example.com"
                required
                autofocus
            />
            
            <flux:button type="submit" variant="primary" class="w-full">
                Send Login Code
            </flux:button>
        </form>
    @endif
</div>