<x-admin-layout title="Create User">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-400 text-sm hover:text-indigo-300">
            &larr; Back to Users
        </a>
    </div>

    <div class="max-w-lg rounded-xl border border-gray-800 p-6" style="background:#111827">
        <h2 class="text-lg font-semibold text-white mb-6">Create New User</h2>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                              focus:outline-none focus:border-indigo-500 transition">
                @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                              focus:outline-none focus:border-indigo-500 transition">
                @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Password *</label>
                <input type="password" name="password" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                              focus:outline-none focus:border-indigo-500 transition"
                       placeholder="Min. 8 characters">
                @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                              focus:outline-none focus:border-indigo-500 transition">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Role *</label>
                <select name="role" required
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-3 py-2.5 text-white text-sm
                               focus:outline-none focus:border-indigo-500 transition">
                    <option value="organizer" {{ old('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="send_welcome_email" id="send_welcome" value="1"
                       class="rounded border-gray-600 bg-gray-800 text-indigo-600 focus:ring-indigo-500">
                <label for="send_welcome" class="text-sm text-gray-400">Send welcome email to user</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-sm font-medium transition">
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-sm transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
