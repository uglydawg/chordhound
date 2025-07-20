<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your account information and settings')">
        <livewire:settings.profile-edit />
        
        <div class="mt-8">
            <livewire:settings.delete-user-form />
        </div>
    </x-settings.layout>
</section>
