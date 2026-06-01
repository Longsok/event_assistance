<x-admin-layout title="Create User">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">
            &larr; Back to Users
        </a>
    </div>

    <div class="max-w-lg rounded-xl border p-6" style="background:var(--panel);border-color:var(--border)">
        <h2 class="text-lg font-semibold mb-6" style="color:var(--text-strong)">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Password *</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)"
                       placeholder="Min. 8 characters">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                       style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
            </div>

            <div>
                <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Role *</label>
                <select name="role" required
                        class="w-full rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-indigo-500 transition"
                        style="background:var(--input-bg);border:1px solid var(--input-border);color:var(--text)">
                    <option value="organizer" {{ old('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="send_welcome_email" id="send_welcome" value="1"
                       class="rounded" style="accent-color:#4f46e5">
                <label for="send_welcome" class="text-sm" style="color:var(--text-soft)">Send welcome email to user</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-lg text-sm transition text-center"
                   style="background:var(--input-bg);color:var(--text-soft)"
                   onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='var(--input-bg)'">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>