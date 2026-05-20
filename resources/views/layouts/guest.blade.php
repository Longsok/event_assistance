<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD
    <title>{{ config('app.name', 'Event Assistance') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .dot { animation: pulse 2s infinite; }
        .dot:nth-child(2) { animation-delay:.4s; }
        .dot:nth-child(3) { animation-delay:.8s; }
        @keyframes pulse { 0%,100%{opacity:.3;transform:scale(.8)} 50%{opacity:1;transform:scale(1)} }
        .form-in { animation: fi .35s ease; }
        @keyframes fi { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
    </style>
</head>
<body class="antialiased font-sans min-h-screen flex bg-slate-950">

    {{-- Left — Branding panel --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 relative flex-col overflow-hidden"
         style="background:linear-gradient(160deg,#1e1b4b 0%,#312e81 40%,#4c1d95 100%)">

        {{-- Grid pattern --}}
        <div class="absolute inset-0 pointer-events-none"
             style="background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:48px 48px"></div>

        {{-- Glow orbs --}}
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full pointer-events-none"
             style="background:radial-gradient(circle,rgba(139,92,246,.35),transparent 70%)"></div>
        <div class="absolute -bottom-40 -left-20 w-96 h-96 rounded-full pointer-events-none"
             style="background:radial-gradient(circle,rgba(79,70,229,.25),transparent 70%)"></div>

        <div class="relative z-10 flex flex-col flex-1 p-12">

            {{-- Logo --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center border border-white/20"
                     style="background:rgba(255,255,255,.12);backdrop-filter:blur(8px)">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="font-semibold text-white text-lg">{{ config('app.name') }}</span>
            </div>

            {{-- Main content --}}
            <div class="flex-1 flex flex-col justify-center">
                <p class="text-violet-300 text-sm font-medium mb-3 tracking-wide uppercase">Organizer Platform</p>
                <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-6">
                    Run events<br>without the chaos
                </h1>
                <p class="text-indigo-200 text-base leading-relaxed mb-10 max-w-xs">
                    From invites to check-ins, tasks to budgets — manage everything in one focused workspace.
                </p>

                <div class="space-y-5">
                    @foreach([
                        ['bg'=>'rgba(79,70,229,.25)','c'=>'#a5b4fc','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','text'=>'Guest management with QR check-in'],
                        ['bg'=>'rgba(124,58,237,.25)','c'=>'#c4b5fd','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','text'=>'Auto-generated task timelines'],
                        ['bg'=>'rgba(99,102,241,.25)','c'=>'#818cf8','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','text'=>'Real-time budget tracking'],
                    ] as $f)
                    <div class="flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:{{ $f['bg'] }}">
                            <svg class="w-4 h-4" style="color:{{ $f['c'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-indigo-100 text-sm">{{ $f['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom stat strip --}}
            <div class="rounded-2xl p-4 border border-white/10"
                 style="background:rgba(255,255,255,.06);backdrop-filter:blur(8px)">
                <div class="flex items-center justify-between text-center">
                    @foreach([['Events','100+'],['Guests','1K+'],['Tasks','500+']] as $s)
                    <div>
                        <p class="text-xl font-bold text-white">{{ $s[1] }}</p>
                        <p class="text-xs text-indigo-300 mt-0.5">{{ $s[0] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Right — Form panel --}}
    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 relative">

        <div class="absolute inset-0 pointer-events-none"
             style="background-image:linear-gradient(rgba(99,102,241,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.025) 1px,transparent 1px);background-size:48px 48px"></div>

        {{-- Mobile logo --}}
        <div class="lg:hidden flex items-center gap-2.5 mb-10">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-semibold text-white">{{ config('app.name') }}</span>
        </div>

        <div class="relative w-full max-w-sm form-in">
            <div class="rounded-2xl px-8 py-9 border border-slate-800"
                 style="background:rgba(15,23,42,.8);backdrop-filter:blur(12px)">
                {{ $slot }}
            </div>
            <p class="text-center text-xs text-slate-600 mt-6">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
=======

    <title>{{ config('app.name', 'EventEase') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen bg-gray-950 text-gray-100 flex items-center justify-center p-4">

    {{-- Subtle background grid --}}
    <div class="fixed inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] pointer-events-none"></div>

    {{-- Glow blob --}}
    <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[600px] h-[300px] bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-md">

        {{-- Brand header --}}
        <div class="flex flex-col items-center mb-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-900/50 group-hover:bg-indigo-500 transition">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">{{ config('app.name', 'EventEase') }}</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl shadow-black/40 px-8 py-8">
            {{ $slot }}
        </div>

        {{-- Footer links --}}
        <p class="text-center text-xs text-gray-600 mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    </div>

</body>
</html>
