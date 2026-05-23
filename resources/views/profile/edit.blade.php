<x-app-layout>
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">
    <h2 class="text-2xl font-bold text-slate-900">Profile Settings</h2>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-semibold text-slate-800 mb-5">Profile Information</h3>
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <h3 class="text-base font-semibold text-slate-800 mb-5">Update Password</h3>
        @include('profile.partials.update-password-form')
    </div>
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
        <h3 class="text-base font-semibold text-red-600 mb-5">Delete Account</h3>
        @include('profile.partials.delete-user-form')
    </div>
</div>
</x-app-layout>
