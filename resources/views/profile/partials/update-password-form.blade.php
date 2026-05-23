<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf @method('put')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
        <input type="password" name="current_password"
               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
        @error('current_password','updatePassword')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
        <input type="password" name="password"
               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
        @error('password','updatePassword')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
        <input type="password" name="password_confirmation"
               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
    </div>
    <div class="flex items-center gap-4">
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Update Password</button>
        @if(session('status')==='password-updated')
        <p class="text-sm text-emerald-600">Updated.</p>
        @endif
    </div>
</form>
