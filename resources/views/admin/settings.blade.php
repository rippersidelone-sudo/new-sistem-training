@extends('layouts.admin')

@section('content')
    <div name="header" class="px-2">
        <h1 class="text-2xl font-semibold text-gray-800 leading-tight">
            {{ __('Profile Settings') }}
        </h1>
        <p class="text-[#737373] mt-2 font-medium">Kelola profile dan akun Anda</p>
    </div>

    <div class="py-8">
        <div class="max-w-7xl px-2 space-y-6">
            {{-- Update Profile Information --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    @if(session('success'))
        <x-notification type="success">
            {{ session('success') }}
        </x-notification>
    @endif

    @if(session('error'))
        <x-notification type="error">
            {{ session('error') }}
        </x-notification>
    @endif

    @if(session('warning'))
        <x-notification type="warning">
            {{ session('warning') }}
        </x-notification>
    @endif

    @if(session('info'))
        <x-notification type="info">
            {{ session('info') }}
        </x-notification>
    @endif

    @if($errors->any())
        <x-notification type="error">
            @foreach($errors->all() as $error)
                {{ $error }}
                @if(!$loop->last)<br>@endif
            @endforeach
        </x-notification>
    @endif
@endsection
