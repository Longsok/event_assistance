<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EventEase') }} — Smart Event Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-950 text-gray-100 overflow-x-hidden">

    {{-- Background decorations --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[500px] bg-indigo-600/8 rounded-full blur-3xl"></div>
        <div class="absolute top-1/3 -left-32 w-[400px] h-[400px] bg-violet-600/6 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[500px] h-[400px] bg-indigo-900/10 rounded-full blur-3xl"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.025)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.025)_1px,transparent_1px)] bg-[size:64px_64px]"></div>
    </div>

    {{-- ── Navbar ──────────────────────────────────────────────────────────── --}}
    <nav class="relative z-10 border-b border-gray-800/60 bg-gray-950/80 backdrop-blur-sm sticky top-0">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Brand --}}
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-900/50 group-hover:bg-indigo-500 transition">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="font-bold text-white text-lg tracking-tight">{{ config('Event Assistance', 'Event Assistance') }}</span>
                </a>

                {{-- Auth links --}}
                <div class="flex items-center gap-3">
                    @auth
                        @if (auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-sm text-gray-300 hover:text-white transition px-3 py-1.5">
                                Admin Panel
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="text-sm text-gray-300 hover:text-white transition px-3 py-1.5">
                                Dashboard
                            </a>
                        @endif
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}"
                               class="text-sm text-gray-400 hover:text-white transition px-3 py-1.5">
                                Sign in
                            </a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-4 py-1.5 rounded-lg transition shadow-lg shadow-indigo-900/30">
                                Get started free
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ── Hero ────────────────────────────────────────────────────────────── --}}
    <section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 bg-indigo-600/15 border border-indigo-600/30 rounded-full px-4 py-1.5 text-xs font-medium text-indigo-300 mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
            Smart Event Assistance System
        </div>

        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-6">
            Plan events with<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-violet-400">
                zero stress
            </span>
        </h1>

        <p class="text-lg text-gray-400 max-w-xl mx-auto mb-10 leading-relaxed">
            From guest lists to budgets, schedules to check-ins —
            everything you need to run flawless events, all in one place.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}"
                   class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500
                          text-white font-semibold px-6 py-3 rounded-xl text-sm transition
                          shadow-xl shadow-indigo-900/40">
                    Go to Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500
                          text-white font-semibold px-6 py-3 rounded-xl text-sm transition
                          shadow-xl shadow-indigo-900/40">
                    Start for free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 bg-gray-800 hover:bg-gray-700
                          border border-gray-700 text-white font-medium px-6 py-3 rounded-xl text-sm transition">
                    Sign in
                </a>
            @endauth
        </div>
    </section>

    {{-- ── Feature cards ──────────────────────────────────────────────────── --}}
    <section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            @php
                $features = [
                    [
                        'icon'  => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                        'color' => 'indigo',
                        'title' => 'Guest Management',
                        'desc'  => 'Invite guests, track RSVPs, and generate QR code invitations with a single click.',
                    ],
                    [
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'emerald',
                        'title' => 'Budget Tracking',
                        'desc'  => 'Keep your finances in check with real-time budget vs. actual spending across categories.',
                    ],
                    [
                        'icon'  => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        'color' => 'violet',
                        'title' => 'Smart Scheduling',
                        'desc'  => 'Build detailed timelines and get automatic warnings when your schedule is at risk.',
                    ],
                    [
                        'icon'  => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                        'color' => 'sky',
                        'title' => 'Task Checklists',
                        'desc'  => 'Organize every task by group, assign owners, and track completion before the big day.',
                    ],
                    [
                        'icon'  => 'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 11-1 0 .5.5 0 011 0zm-5 0a.5.5 0 11-1 0 .5.5 0 011 0zm-5 0a.5.5 0 11-1 0 .5.5 0 011 0z',
                        'color' => 'amber',
                        'title' => 'QR Check-in',
                        'desc'  => 'Scan guest QR codes for instant check-in and attendance logs, no paper lists needed.',
                    ],
                    [
                        'icon'  => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'color' => 'rose',
                        'title' => 'Event Reports',
                        'desc'  => 'Get post-event summaries: attendance rates, spending breakdown, and guest feedback.',
                    ],
                ];
                $colorMap = [
                    'indigo' => ['bg' => 'bg-indigo-600/15', 'border' => 'border-indigo-600/25', 'icon' => 'text-indigo-400'],
                    'emerald'=> ['bg' => 'bg-emerald-600/15','border' => 'border-emerald-600/25','icon' => 'text-emerald-400'],
                    'violet' => ['bg' => 'bg-violet-600/15', 'border' => 'border-violet-600/25', 'icon' => 'text-violet-400'],
                    'sky'    => ['bg' => 'bg-sky-600/15',    'border' => 'border-sky-600/25',    'icon' => 'text-sky-400'],
                    'amber'  => ['bg' => 'bg-amber-600/15',  'border' => 'border-amber-600/25',  'icon' => 'text-amber-400'],
                    'rose'   => ['bg' => 'bg-rose-600/15',   'border' => 'border-rose-600/25',   'icon' => 'text-rose-400'],
                ];
            @endphp

            @foreach ($features as $f)
                @php $c = $colorMap[$f['color']]; @endphp
                <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 hover:border-gray-700 transition group">
                    <div class="w-11 h-11 {{ $c['bg'] }} border {{ $c['border'] }} rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-white mb-2">{{ $f['title'] }}</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ── CTA banner ──────────────────────────────────────────────────────── --}}
    <section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="bg-gradient-to-br from-indigo-600/20 to-violet-600/10 border border-indigo-600/25 rounded-2xl px-8 py-12 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Ready to plan your next event?</h2>
            <p class="text-gray-400 text-sm mb-7 max-w-md mx-auto">
                Join event organizers who use {{ config('app.name') }} to deliver unforgettable experiences.
            </p>
            @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white
                          font-semibold px-6 py-3 rounded-xl text-sm transition shadow-xl shadow-indigo-900/40">
                    Get started — it's free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            @endguest
        </div>
    </section>

    {{-- ── Footer ──────────────────────────────────────────────────────────── --}}
    <footer class="relative z-10 border-t border-gray-800 px-4 py-6 text-center text-xs text-gray-600">
        &copy; {{ date('Y') }} {{ config('app.name', 'EventEase') }}. Built with Laravel &amp; Tailwind CSS.
    </footer>

</body>
</html>
