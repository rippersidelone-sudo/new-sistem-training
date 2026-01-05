@extends('layouts.participant')

@section('content')
    <div name="header" class="px-2">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile Settings') }}
        </h1>
        <p class="text-[#737373] mt-2 font-medium">Kelola profile dan akun</p>
    </div>

    <div class="py-8">
        <div class="max-w-7xl px-2 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection