<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Event Assistance') }} — Smart Event Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Outfit', sans-serif; }
        .hero-in { animation: hi .7s ease both; }
        .hero-in-2 { animation: hi .7s ease .15s both; }
        .hero-in-3 { animation: hi .7s ease .3s both; }
        @keyframes hi { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,.25); }
        .gradient-text { background: linear-gradient(135deg, #a5b4fc, #c4b5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: linear-gradient(135deg,#4f46e5,#7c3aed); box-shadow: 0 4px 16px rgba(79,70,229,.4); transition: all .2s ease; }
        .btn-primary:hover { transform:translateY(-1px); box-shadow: 0 8px 24px rgba(79,70,229,.5); }
    </style>
</head>
<body class="antialiased overflow-x-hidden"
      style="background:#080b14;color:#e2e8f0">

    {{-- Background --}}
    <div class="fixed inset-0 pointer-events-none" style="z-index:0">
        <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:900px;height:500px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.18),transparent 70%);"></div>
        <div style="position:absolute;top:30%;right:-10%;width:600px;height:600px;background:radial-gradient(circle,rgba(124,58,237,.08),transparent 70%);"></div>
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:64px 64px;"></div>
    </div>

    {{-- Navbar --}}
    <nav class="relative z-10 border-b sticky top-0" style="border-color:rgba(255,255,255,.08);background:rgba(8,11,20,.85);backdrop-filter:blur(12px)">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="/" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center btn-primary">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-white text-base">Event Assistance</span>
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}"
                               class="text-sm text-slate-300 hover:text-white transition px-4 py-2 rounded-lg hover:bg-white/8">
                                Admin Panel
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}"
                               class="text-sm text-slate-300 hover:text-white transition px-4 py-2 rounded-lg hover:bg-white/8">
                                Dashboard
                            </a>
                        @endif
                    @else
                        @if(Route::has('login'))
                            <a href="{{ route('login') }}"
                               class="text-sm text-slate-400 hover:text-white transition px-4 py-2">
                                Sign in
                            </a>
                        @endif
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="btn-primary text-sm text-white font-medium px-5 py-2 rounded-xl">
                                Get started free
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 text-center">

        <div class="hero-in inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium mb-8 border"
             style="background:rgba(79,70,229,.12);border-color:rgba(99,102,241,.3);color:#a5b4fc">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400" style="animation:pulse 2s infinite"></span>
            Smart Event Assistance System
        </div>

        <h1 class="hero-in-2 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-6">
            Plan events with<br>
            <span class="gradient-text">zero stress</span>
        </h1>

        <p class="hero-in-3 text-lg leading-relaxed mb-10 max-w-lg mx-auto" style="color:#94a3b8">
            From guest lists to budgets, schedules to QR check-ins —
            everything you need to run flawless events, all in one place.
        </p>

        <div class="hero-in-3 flex flex-col sm:flex-row gap-3 justify-center">
            @auth
                <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}"
                   class="btn-primary inline-flex items-center justify-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
                    Go to Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="btn-primary inline-flex items-center justify-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
                    Start for free
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 font-medium px-7 py-3.5 rounded-xl text-sm border transition"
                   style="background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.12);color:#e2e8f0">
                    Sign in
                </a>
            @endauth
        </div>
    </section>

    {{-- Features grid --}}
    <section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
            $features = [
                ['title'=>'Guest Management',   'desc'=>'Invite guests, track RSVPs, and generate QR code invitations with a single click.', 'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','accent'=>'#6366f1','glow'=>'rgba(99,102,241,.15)'],
                ['title'=>'Budget Tracking',    'desc'=>'Keep your finances in check with real-time budget vs. actual spending across categories.','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','accent'=>'#10b981','glow'=>'rgba(16,185,129,.12)'],
                ['title'=>'Smart Scheduling',   'desc'=>'Build detailed timelines and get automatic warnings when your schedule is at risk.','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','accent'=>'#8b5cf6','glow'=>'rgba(139,92,246,.15)'],
                ['title'=>'Task Checklists',    'desc'=>'Organize every task by group, assign due dates, and track completion before the big day.','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','accent'=>'#0ea5e9','glow'=>'rgba(14,165,233,.12)'],
                ['title'=>'QR Check-in',        'desc'=>'Scan guest QR codes for instant check-in and live attendance logs, no paper lists needed.','icon'=>'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5a.5.5 0 11-1 0 .5.5 0 011 0zm-5 0a.5.5 0 11-1 0 .5.5 0 011 0zm-5 0a.5.5 0 11-1 0 .5.5 0 011 0z','accent'=>'#f59e0b','glow'=>'rgba(245,158,11,.12)'],
                ['title'=>'Event Reports',      'desc'=>'Get post-event summaries: attendance rates, spending breakdown, and contribution totals.','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','accent'=>'#ef4444','glow'=>'rgba(239,68,68,.12)'],
            ];
            @endphp

            @foreach($features as $f)
            <div class="card-hover rounded-2xl p-6 border"
                 style="background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08)">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5"
                     style="background:{{ $f['glow'] }};border:1px solid {{ $f['accent'] }}30">
                    <svg class="w-5 h-5" style="color:{{ $f['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-white text-base mb-2">{{ $f['title'] }}</h3>
                <p class="text-sm leading-relaxed" style="color:#64748b">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
        <div class="rounded-2xl px-8 py-14 text-center border"
             style="background:linear-gradient(135deg,rgba(79,70,229,.15),rgba(124,58,237,.08));border-color:rgba(99,102,241,.25)">
            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Ready to plan your next event?</h2>
            <p class="text-sm mb-8 max-w-md mx-auto" style="color:#94a3b8">
                Join event organizers who use {{ config('app.name') }} to deliver unforgettable experiences.
            </p>
            @guest
            <a href="{{ route('register') }}"
               class="btn-primary inline-flex items-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
                Get started — it is free
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            @endguest
        </div>
    </section>

    {{-- Footer --}}
    <footer class="relative z-10 border-t px-4 py-6 text-center text-xs"
            style="border-color:rgba(255,255,255,.08);color:#475569">
        &copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel and Tailwind CSS.
    </footer>

    <style>
        @keyframes pulse { 0%,100%{opacity:.5} 50%{opacity:1} }
    </style>
</body>
</html>
