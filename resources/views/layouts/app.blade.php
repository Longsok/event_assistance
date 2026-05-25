<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        *{font-family:'Outfit',sans-serif}
        ::-webkit-scrollbar{width:4px}
        ::-webkit-scrollbar-thumb{background:#374151;border-radius:4px}
        ::-webkit-scrollbar-track{background:transparent}
        .nav-active{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff !important}
        .nav-inactive{color:#6b7280}
        .nav-inactive:hover{background:rgba(255,255,255,.06);color:#e5e7eb}
        .sidebar-glow{box-shadow:inset -1px 0 0 rgba(255,255,255,.06)}
    </style>
</head>
<body class="antialiased overflow-hidden" style="height:100vh;background:#080b14;color:#e2e8f0">

<div class="flex overflow-hidden" style="height:100vh" x-data="{mob:false}">

    {{-- Mobile overlay --}}
    <div x-show="mob" @click="mob=false"
         class="fixed inset-0 bg-black/60 z-20 lg:hidden backdrop-blur-sm"
         x-transition:enter="transition duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 w-64 z-30 flex flex-col overflow-hidden
                  transition-transform duration-300 lg:relative lg:translate-x-0 sidebar-glow"
           style="background:#0d1117;border-right:1px solid rgba(255,255,255,.07)"
           :class="mob?'translate-x-0 shadow-2xl':'-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-5 flex-shrink-0" style="border-bottom:1px solid rgba(255,255,255,.06)">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                     style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-white text-sm leading-tight">{{ config('app.name') }}</p>
                    <p class="text-xs leading-tight" style="color:#6b7280">Organizer Portal</p>
                </div>
            </div>
        </div>

        {{-- Nav links --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
            $links = [
                ['route'=>'dashboard',     'label'=>'Dashboard',  'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['route'=>'events.index',  'label'=>'My Events',  'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route'=>'guests.index',  'label'=>'Guest Book', 'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ];
            @endphp

            @foreach($links as $l)
            @php $active = request()->routeIs($l['route'].'*'); @endphp
            <a href="{{ route($l['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $active ? 'nav-active' : 'nav-inactive' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $l['icon'] }}"/>
                </svg>
                {{ $l['label'] }}
            </a>
            @endforeach

            <div class="py-3"><div class="h-px" style="background:rgba(255,255,255,.06)"></div></div>

            <a href="{{ route('events.create') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-indigo-300 border"
               style="background:rgba(79,70,229,.12);border-color:rgba(99,102,241,.25)">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Event
            </a>
        </nav>

        {{-- User profile --}}
        <div class="p-3 flex-shrink-0" style="border-top:1px solid rgba(255,255,255,.06)">
            <div x-data="{open:false}" class="relative">
                <button @click="open=!open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition nav-inactive text-left">
                    <div class="w-8 h-8 rounded-full text-white text-xs font-bold flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs truncate" style="color:#6b7280">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open=false"
                     class="absolute bottom-full left-0 right-0 mb-1 rounded-xl overflow-hidden shadow-2xl border"
                     style="background:#161b27;border-color:rgba(255,255,255,.08)"
                     x-transition:enter="transition duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm nav-inactive hover:bg-white/5 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm nav-inactive hover:bg-white/5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top bar (mobile only) --}}
        <div class="flex items-center gap-3 px-4 h-14 flex-shrink-0 lg:hidden"
             style="background:#0d1117;border-bottom:1px solid rgba(255,255,255,.06)">
            <button @click="mob=!mob" class="nav-inactive p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <p class="font-semibold text-white text-sm">{{ config('app.name') }}</p>
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
