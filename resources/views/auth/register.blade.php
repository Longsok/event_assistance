<x-guest-layout>

    {{-- Heading --}}
    <div class="mb-7">
        <h1 class="text-2xl font-bold text-white">Create your account</h1>
        <p class="text-gray-400 text-sm mt-1">Start organizing events for free</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="Your full name"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                       {{ $errors->has('name') ? 'border-red-500' : 'border-gray-700' }}"
            >
            @error('name')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                placeholder="you@example.com"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition
                       {{ $errors->has('email') ? 'border-red-500' : 'border-gray-700' }}"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Min. 8 characters"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            >
            @error('password')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1.5">Confirm password</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Repeat your password"
                class="w-full px-4 py-2.5 rounded-lg bg-gray-800 border border-gray-700 text-white text-sm placeholder-gray-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
            >
            @error('password_confirmation')
                <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Terms note --}}
        <p class="text-xs text-gray-600 leading-relaxed">
            By registering, you agree that your account will be created as an
            <span class="text-gray-500 font-medium">event organizer</span>.
        </p>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700
                   text-white font-semibold py-2.5 px-4 rounded-lg text-sm
                   transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-900
                   shadow-lg shadow-indigo-900/40"
        >
            Create account
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-gray-800"></div>
        <span class="text-xs text-gray-600">already have an account?</span>
        <div class="flex-1 h-px bg-gray-800"></div>
    </div>

    {{-- Login link --}}
    <p class="text-center text-sm text-gray-500">
        <a href="{{ route('login') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition">
            Sign in instead
        </a>
    </p>

</x-guest-layout>
