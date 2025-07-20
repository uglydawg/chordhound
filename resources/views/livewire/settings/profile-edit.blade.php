<div>
    @if($successMessage)
        <flux:alert variant="success" class="mb-6" wire:listen:clear-success-message="clearSuccessMessage">
            {{ $successMessage }}
        </flux:alert>
    @endif

    <flux:card>
        <flux:card.header>
            <flux:heading size="lg">Profile Information</flux:heading>
            <flux:subheading>Update your account's profile information.</flux:subheading>
        </flux:card.header>

        <form wire:submit="updateProfile" class="space-y-6">
            <!-- Name -->
            <flux:input
                wire:model="name"
                label="Full Name"
                type="text"
                required
                autocomplete="name"
                placeholder="John Doe"
            />

            <!-- Username -->
            <div>
                <flux:input
                    wire:model.blur="username"
                    wire:blur="checkUsername"
                    label="Username"
                    type="text"
                    required
                    autocomplete="username"
                    placeholder="johndoe"
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
                label="Display Name"
                type="text"
                autocomplete="name"
                placeholder="How others will see you"
                description="Leave blank to use your full name"
            />

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    Save Changes
                </flux:button>
            </div>
        </form>
    </flux:card>

    <!-- Email Change Section -->
    <flux:card class="mt-6">
        <flux:card.header>
            <flux:heading size="lg">Email Address</flux:heading>
            <flux:subheading>Change your account's email address.</flux:subheading>
        </flux:card.header>

        @if(!$showEmailChange)
            <div class="space-y-4">
                <div>
                    <flux:field label="Current Email">
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $email }}</div>
                    </flux:field>
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="showEmailChangeForm" variant="ghost">
                        Change Email
                    </flux:button>
                </div>
            </div>
        @else
            <form wire:submit="initiateEmailChange" class="space-y-6">
                <!-- Current Email -->
                <div>
                    <flux:field label="Current Email">
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $email }}</div>
                    </flux:field>
                </div>

                <!-- New Email -->
                <flux:input
                    wire:model="new_email"
                    label="New Email Address"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="new@example.com"
                />

                <!-- Password Confirmation -->
                <flux:input
                    wire:model="password"
                    label="Current Password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your current password to confirm"
                    description="For security, please confirm your current password"
                    viewable
                />

                <div class="flex justify-end gap-3">
                    <flux:button type="button" wire:click="cancelEmailChange" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Update Email
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:card>
</div>