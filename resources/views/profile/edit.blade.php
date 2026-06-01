<x-app-layout>
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto space-y-6">
    <h2 class="text-2xl font-bold" style="color:var(--text-strong)">Profile Settings</h2>
    <div class="rounded-2xl border shadow-sm p-6" style="background:var(--panel);border-color:var(--border)">
        <h3 class="text-base font-semibold mb-5" style="color:var(--text-strong)">Profile Information</h3>
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="rounded-2xl border shadow-sm p-6" style="background:var(--panel);border-color:var(--border)">
        <h3 class="text-base font-semibold mb-5" style="color:var(--text-strong)">Update Password</h3>
        @include('profile.partials.update-password-form')
    </div>
    <div class="rounded-2xl border shadow-sm p-6" style="background:var(--panel);border-color:rgba(239,68,68,.25)">
        <h3 class="text-base font-semibold mb-5" style="color:#f87171">Delete Account</h3>
        @include('profile.partials.delete-user-form')
    </div>
</div>
</x-app-layout>