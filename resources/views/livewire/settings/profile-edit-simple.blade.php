<div class="space-y-6">
    @if($successMessage)
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ $successMessage }}
        </div>
    @endif

    <!-- Profile Information -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Update your account's profile information.</p>
        </div>

        <form wire:submit="updateProfile" class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                <input 
                    wire:model="name" 
                    id="name"
                    type="text" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <input 
                    wire:model.blur="username"
                    wire:blur="checkUsername"
                    id="username"
                    type="text" 
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <p class="text-sm text-gray-500 mt-1">Start with letter/number, then letters, numbers, underscore, dash, or dot (max 48 chars)</p>
                @error('username') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                
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
            <div>
                <label for="display_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name</label>
                <input 
                    wire:model="display_name" 
                    id="display_name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                />
                <p class="text-sm text-gray-500 mt-1">Leave blank to use your full name</p>
                @error('display_name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Email Change Section -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Email Address</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Change your account's email address.</p>
        </div>

        @if(!$showEmailChange)
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Email</label>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $email }}</div>
                </div>

                <div class="flex justify-end">
                    <button 
                        wire:click="showEmailChangeForm" 
                        class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 font-medium py-2 px-4 transition-colors"
                    >
                        Change Email
                    </button>
                </div>
            </div>
        @else
            <form wire:submit="initiateEmailChange" class="space-y-6">
                <!-- Current Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Email</label>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $email }}</div>
                </div>

                <!-- New Email -->
                <div>
                    <label for="new_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Email Address</label>
                    <input 
                        wire:model="new_email" 
                        id="new_email"
                        type="email" 
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    @error('new_email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                    <input 
                        wire:model="password" 
                        id="password"
                        type="password" 
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    />
                    <p class="text-sm text-gray-500 mt-1">For security, please confirm your current password</p>
                    @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelEmailChange" 
                        class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 font-medium py-2 px-4 transition-colors"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors"
                    >
                        Update Email
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>