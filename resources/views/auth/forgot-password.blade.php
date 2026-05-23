<x-guest-layout>
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Reset password</h1>
        <p class="text-gray-400 text-sm mt-1">Enter your email to receive a reset link</p>
    </div>
    @if(session('status'))
    <div class="mb-5 bg-green-900/40 border border-green-700/50 text-green-300 text-sm px-4 py-3 rounded-lg">{{ session('status') }}</div>
    @endif
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-lg text-sm transition">
            Send Reset Link
        </button>
    </form>
    <p class="text-center text-sm text-gray-500 mt-6">
        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 transition">Back to sign in</a>
    </p>
</x-guest-layout>
