<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold text-white mb-3">Verify your email</h1>
        <p class="text-gray-400 text-sm mb-6">We sent a verification link to your email. Click the link to continue.</p>
        @if(session('status') == 'verification-link-sent')
        <div class="mb-5 bg-green-900/40 border border-green-700/50 text-green-300 text-sm px-4 py-3 rounded-lg">A new link has been sent.</div>
        @endif
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2.5 rounded-lg text-sm transition mb-4">Resend link</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-300 transition">Sign out</button>
        </form>
    </div>
</x-guest-layout>
