<p class="text-sm text-slate-500 mb-4">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
<button onclick="document.getElementById('delete-modal').classList.remove('hidden')"
        class="px-5 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-medium rounded-xl transition">
    Delete Account
</button>
<div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-xl">
        <h3 class="text-lg font-semibold text-slate-900 mb-2">Delete Account</h3>
        <p class="text-slate-500 text-sm mb-5">Are you sure? This action cannot be undone.</p>
        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-3">
            @csrf @method('delete')
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm your password</label>
                <input type="password" name="password" placeholder="Password"
                       class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400">
                @error('password','userDeletion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm rounded-xl transition">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm rounded-xl transition">Delete</button>
            </div>
        </form>
    </div>
</div>
