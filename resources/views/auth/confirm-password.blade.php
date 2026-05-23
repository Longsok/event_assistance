<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Confirm password</h1>
        <p class="text-gray-400 text-sm mt-1">Please confirm your password before continuing.</p>
    </div>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-lg text-sm transition">Confirm</button>
    </form>
</x-guest-layout>
