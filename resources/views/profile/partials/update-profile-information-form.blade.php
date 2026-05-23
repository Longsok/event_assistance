<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf @method('patch')
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
        @error('name','profileInformation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
               class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
        @error('email','profileInformation')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
    <div class="flex items-center gap-4">
        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-xl transition">Save</button>
        @if(session('status')==='profile-updated')
        <p class="text-sm text-emerald-600">Saved.</p>
        @endif
    </div>
</form>
