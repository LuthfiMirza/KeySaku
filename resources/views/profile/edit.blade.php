<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="px-4 pt-6 pb-28 lg:px-8 lg:pt-8 lg:pb-8" style="background:#0A1628 !important;">
        <div class="max-w-5xl mx-auto space-y-5">
            <div class="p-5 sm:p-8" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-5 sm:p-8" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-5 sm:p-8" style="background:#132044 !important;border:1px solid rgba(255,255,255,0.08) !important;border-radius:24px !important;box-shadow:0 8px 32px rgba(0,0,0,0.3) !important;">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
