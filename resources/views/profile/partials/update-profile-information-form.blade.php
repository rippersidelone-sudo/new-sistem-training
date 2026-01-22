<!-- resources/views/profile/partials/update-profile-information-form.blade.php -->
<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        @php
            $currentUser = $user ?? auth()->user();
        @endphp

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $currentUser->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $currentUser->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Additional fields for Branch Coordinator and Participant -->
        @if($currentUser->role && in_array($currentUser->role->name, ['Branch Coordinator', 'Participant']))
            <div>
                <x-input-label for="branch" :value="__('Branch')" />
                <x-text-input id="branch" name="branch" type="text" class="mt-1 block w-full bg-gray-100" :value="$currentUser->branch ? $currentUser->branch->name : '-'" disabled />
                <p class="mt-1 text-xs text-gray-500">{{ __('Branch information cannot be changed') }}</p>
            </div>
        @endif

        <!-- Role (for all users) -->
        <div>
            <x-input-label for="role" :value="__('Role')" />
            <x-text-input id="role" name="role" type="text" class="mt-1 block w-full bg-gray-100" :value="$currentUser->role ? $currentUser->role->name : '-'" disabled />
            <p class="mt-1 text-xs text-gray-500">{{ __('Role cannot be changed. Contact admin if needed.') }}</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>