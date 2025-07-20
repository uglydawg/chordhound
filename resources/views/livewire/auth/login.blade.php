<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute end-0 top-0 text-sm" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('Remember me')" />

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>
    
    <!-- Social Login Options -->
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <span class="w-full border-t border-zinc-300 dark:border-zinc-700"></span>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="bg-white dark:bg-zinc-900 px-2 text-zinc-500 dark:text-zinc-400">{{ __('Or continue with') }}</span>
        </div>
    </div>
    
    <div class="flex flex-col gap-3">
        <flux:button href="{{ route('auth.google') }}" variant="ghost" class="w-full">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            {{ __('Continue with Google') }}
        </flux:button>
        
        <flux:modal name="magic-link">
            <flux:card class="w-full max-w-md">
                <flux:heading size="lg">{{ __('Sign in with Magic Link') }}</flux:heading>
                <flux:text class="mt-2 text-gray-600">{{ __('Enter your email and we\'ll send you a login link.') }}</flux:text>
                
                <div class="mt-6">
                    <livewire:magic-link-login />
                </div>
            </flux:card>
        </flux:modal>
        
        <flux:button onclick="document.querySelector('[data-flux-modal=magic-link]').showModal()" variant="ghost" class="w-full">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            {{ __('Continue with Magic Link') }}
        </flux:button>
        
        <flux:modal name="auth-code">
            <flux:card class="w-full max-w-md">
                <flux:heading size="lg">{{ __('Sign in with Auth Code') }}</flux:heading>
                <flux:text class="mt-2 text-gray-600">{{ __('Enter your email and we\'ll send you a login code.') }}</flux:text>
                
                <div class="mt-6">
                    <livewire:auth-code-login />
                </div>
            </flux:card>
        </flux:modal>
        
        <flux:button onclick="document.querySelector('[data-flux-modal=auth-code]').showModal()" variant="ghost" class="w-full">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
            {{ __('Continue with Auth Code') }}
        </flux:button>
    </div>

    @if (Route::has('register'))
        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
    @endif
</div>
