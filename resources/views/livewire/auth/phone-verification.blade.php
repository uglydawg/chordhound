<flux:card class="w-full max-w-md">
    <flux:heading size="lg">Verify Your Phone Number</flux:heading>
    
    <flux:subheading class="mb-6">
        To complete your registration and enhance account security, please verify your phone number.
    </flux:subheading>

    <form wire:submit="{{ $codeSent ? 'verifyCode' : 'sendVerificationCode' }}" class="space-y-6">
        @if (!$codeSent)
            <!-- Phone Number Input -->
            <div>
                <flux:input 
                    wire:model="phoneNumber" 
                    label="Phone Number" 
                    type="tel" 
                    placeholder="+1234567890"
                    description="Include country code (e.g., +1 for US)"
                />
            </div>

            @if ($error)
                <flux:error>{{ $error }}</flux:error>
            @endif

            <flux:button type="submit" variant="primary" class="w-full">
                Send Verification Code
            </flux:button>
        @else
            <!-- Verification Code Input -->
            <div class="text-center">
                <flux:subheading>
                    We've sent a 6-digit code to {{ $phoneNumber }}
                </flux:subheading>
                <p class="text-sm text-gray-600 mt-2">
                    Enter the code below to verify your phone number.
                </p>
            </div>

            <div>
                <flux:input 
                    wire:model="verificationCode" 
                    label="Verification Code" 
                    type="text" 
                    placeholder="123456"
                    maxlength="6"
                    class="text-center tracking-widest text-lg"
                />
            </div>

            <flux:button type="submit" variant="primary" class="w-full">
                Verify Code
            </flux:button>

            <div class="text-center">
                <flux:button wire:click="resendCode" variant="ghost" size="sm">
                    Resend Code
                </flux:button>
            </div>
        @endif

        @if (!$codeSent)
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-800">
                    Skip for now
                </a>
            </div>
        @endif
    </form>
</flux:card>