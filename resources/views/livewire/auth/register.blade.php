<div class="flex flex-col gap-6">
    <x-auth-header 
        :title="$step === 'email' ? __('Create an account') : ($step === 'details' ? __('Tell us about yourself') : __('Verify you\'re human'))" 
        :description="$step === 'email' ? __('Enter your email to get started') : ($step === 'details' ? __('Set up your profile and password') : __('Complete your registration'))" 
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <!-- Step 1: Email -->
    @if ($step === 'email')
        <form wire:submit="validateEmail" class="flex flex-col gap-6">
            <flux:input
                wire:model="email"
                :label="__('Email address')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Continue') }}
            </flux:button>
        </form>
    @endif

    <!-- Step 2: Details -->
    @if ($step === 'details')
        <form wire:submit="validateDetails" class="flex flex-col gap-6">
            <!-- Name -->
            <flux:input
                wire:model="name"
                :label="__('Full Name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('John Doe')"
            />

            <!-- Username -->
            <div>
                <flux:input
                    wire:model.blur="username"
                    wire:blur="checkUsername"
                    :label="__('Username (optional)')"
                    type="text"
                    autocomplete="username"
                    :placeholder="__('johndoe')"
                    description="Start with letter/number, then letters, numbers, underscore, dash, or dot (max 48 chars)"
                />
                
                @if(!empty($usernameSuggestions))
                    <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-sm text-yellow-800 mb-2">Suggested usernames:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($usernameSuggestions as $suggestion)
                                <button
                                    type="button"
                                    wire:click="selectUsername('{{ $suggestion }}')"
                                    class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full hover:bg-blue-200 transition-colors"
                                >
                                    {{ $suggestion }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Display Name -->
            <flux:input
                wire:model="display_name"
                :label="__('Display Name (optional)')"
                type="text"
                :placeholder="__('How others will see you')"
                description="Leave blank to use your full name"
            />

            <!-- Password -->
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex gap-3">
                <flux:button wire:click="goBack" variant="ghost" class="flex-1">
                    {{ __('Back') }}
                </flux:button>
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ __('Continue') }}
                </flux:button>
            </div>
        </form>
    @endif

    <!-- Step 3: Robot Verification -->
    @if ($step === 'verification')
        <form wire:submit="verifyRobot" class="flex flex-col gap-6">
            <div class="text-center p-6 border border-gray-200 rounded-lg bg-gray-50">
                <flux:heading size="lg" class="mb-4">Almost there!</flux:heading>
                <p class="text-gray-600 mb-6">
                    Please confirm you're not a robot to complete your registration.
                </p>
                
                <label class="flex items-center justify-center gap-3 cursor-pointer">
                    <input 
                        type="checkbox" 
                        wire:model="robot_verification" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    >
                    <span class="text-sm font-medium">I'm not a robot</span>
                </label>
                
                @error('robot_verification')
                    <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <flux:button wire:click="goBack" variant="ghost" class="flex-1">
                    {{ __('Back') }}
                </flux:button>
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ __('Create Account') }}
                </flux:button>
            </div>
        </form>
    @endif

    @if (false) {{-- Google OAuth temporarily hidden --}}
    <!-- Google OAuth -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
        </div>
    </div>

    <a href="{{ route('auth.google') }}" 
       class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Continue with Google
    </a>
    @endif

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>
