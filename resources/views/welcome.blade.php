<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Smart Event Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{font-family:'Outfit',sans-serif}
        .btn-glow{background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 16px rgba(79,70,229,.4);transition:all .2s ease}
        .btn-glow:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(79,70,229,.5)}
        .card-hover{transition:transform .2s ease,box-shadow .2s ease}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.25)}
        .gradient-text{background:linear-gradient(135deg,#a5b4fc,#c4b5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        @keyframes pulse{0%,100%{opacity:.5}50%{opacity:1}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .fade-up{animation:fadeUp .7s ease both}
        .fade-up-2{animation:fadeUp .7s ease .15s both}
        .fade-up-3{animation:fadeUp .7s ease .3s both}
    </style>
</head>
<body class="antialiased overflow-x-hidden" style="background:#080b14;color:#e2e8f0">

<div class="fixed inset-0 pointer-events-none" style="z-index:0">
    <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:900px;height:500px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.18),transparent 70%)"></div>
    <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.025) 1px,transparent 1px);background-size:64px 64px"></div>
</div>

<nav class="relative z-10 border-b sticky top-0" style="border-color:rgba(255,255,255,.08);background:rgba(8,11,20,.85);backdrop-filter:blur(12px)">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <span class="font-bold text-white">{{ config('app.name') }}</span>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}"
                       class="text-sm text-slate-300 hover:text-white transition px-4 py-2">Dashboard</a>
                @else
                    @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition px-4 py-2">Sign in</a>
                    @endif
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-glow text-sm text-white font-medium px-5 py-2 rounded-xl">Get started free</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 text-center">
    <div class="fade-up inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium mb-8 border"
         style="background:rgba(79,70,229,.12);border-color:rgba(99,102,241,.3);color:#a5b4fc">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400" style="animation:pulse 2s infinite"></span>
        Smart Event Assistance System
    </div>
    <h1 class="fade-up-2 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight tracking-tight mb-6">
        Plan events with<br><span class="gradient-text">zero stress</span>
    </h1>
    <p class="fade-up-3 text-lg leading-relaxed mb-10 max-w-lg mx-auto" style="color:#94a3b8">
        From guest lists to budgets, schedules to QR check-ins — everything you need to run flawless events, all in one place.
    </p>
    <div class="fade-up-3 flex flex-col sm:flex-row gap-3 justify-center">
        @auth
        <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}"
           class="btn-glow inline-flex items-center justify-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
            Go to Dashboard
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        @else
        <a href="{{ route('register') }}" class="btn-glow inline-flex items-center justify-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
            Start for free
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 font-medium px-7 py-3.5 rounded-xl text-sm border transition"
           style="background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.12);color:#e2e8f0">Sign in</a>
        @endauth
    </div>
</section>

<section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php
        $features=[
            ['title'=>'Guest Management','desc'=>'Invite guests, track RSVPs, and generate QR code invitations instantly.','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','accent'=>'#6366f1','glow'=>'rgba(99,102,241,.15)'],
            ['title'=>'Budget Tracking','desc'=>'Real-time budget vs. actual spending across all categories.','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','accent'=>'#10b981','glow'=>'rgba(16,185,129,.12)'],
            ['title'=>'Smart Scheduling','desc'=>'Build detailed timelines with automatic warnings when at risk.','icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','accent'=>'#8b5cf6','glow'=>'rgba(139,92,246,.15)'],
            ['title'=>'Task Checklists','desc'=>'Auto-generated tasks by group with due dates and priorities.','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','accent'=>'#0ea5e9','glow'=>'rgba(14,165,233,.12)'],
            ['title'=>'QR Check-in','desc'=>'Scan guest QR codes for instant check-in and live attendance logs.','icon'=>'M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z','accent'=>'#f59e0b','glow'=>'rgba(245,158,11,.12)'],
            ['title'=>'Event Reports','desc'=>'Post-event summaries: attendance rates and spending breakdown.','icon'=>'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','accent'=>'#ef4444','glow'=>'rgba(239,68,68,.12)'],
        ];
        @endphp
        @foreach($features as $f)
        <div class="card-hover rounded-2xl p-6 border" style="background:rgba(255,255,255,.04);border-color:rgba(255,255,255,.08)">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" style="background:{{ $f['glow'] }};border:1px solid {{ $f['accent'] }}30">
                <svg class="w-5 h-5" style="color:{{ $f['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                </svg>
            </div>
            <h3 class="font-semibold text-white text-base mb-2">{{ $f['title'] }}</h3>
            <p class="text-sm leading-relaxed" style="color:#94a3b8">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

<section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="rounded-2xl px-8 py-14 text-center border" style="background:linear-gradient(135deg,rgba(79,70,229,.15),rgba(124,58,237,.08));border-color:rgba(99,102,241,.25)">
        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Ready to plan your next event?</h2>
        <p class="text-sm mb-8 max-w-md mx-auto" style="color:#94a3b8">
            Join event organizers who use {{ config('app.name') }} to deliver unforgettable experiences.
        </p>
        @guest
        <a href="{{ route('register') }}" class="btn-glow inline-flex items-center gap-2 text-white font-semibold px-7 py-3.5 rounded-xl text-sm">
            Get started free
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        @endguest
    </div>
</section>

<footer class="relative z-10 border-t px-4 py-6 text-center text-xs" style="border-color:rgba(255,255,255,.08);color:#94a3b8">
    &copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel &amp; Tailwind CSS.
</footer>

</body>
</html>
