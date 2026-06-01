<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Apply saved theme before paint to avoid flash --}}
    <script>
        (function(){
            if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
            else document.documentElement.classList.add('dark');
        })();
    </script>
    <style>
        *{font-family:'Outfit',sans-serif}

        /* ---- Theme variables ---- */
        :root{
            --bg:#080b14; --panel:#0d1117; --panel-2:#161b27;
            --text:#e2e8f0; --text-strong:#ffffff; --text-soft:#6b7280;
            --input-bg:rgba(255,255,255,.06); --input-border:rgba(255,255,255,.1);
            --border:rgba(255,255,255,.07); --border-soft:rgba(255,255,255,.06);
            --hover:rgba(255,255,255,.06); --scroll:#374151;
        }
        html:not(.dark){
            --bg:#f6f7fb; --panel:#ffffff; --panel-2:#ffffff;
            --text:#1e293b; --text-strong:#0f172a; --text-soft:#64748b;
            --input-bg:rgba(15,23,42,.05); --input-border:rgba(15,23,42,.15);
            --border:rgba(15,23,42,.10); --border-soft:rgba(15,23,42,.07);
            --hover:rgba(15,23,42,.04); --scroll:#cbd5e1;
        }
        body{background:var(--bg);color:var(--text);transition:background .3s ease,color .3s ease}

        ::-webkit-scrollbar{width:4px}
        ::-webkit-scrollbar-thumb{background:var(--scroll);border-radius:4px}
        ::-webkit-scrollbar-track{background:transparent}
        .nav-active{background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff !important}
        .nav-inactive{color:var(--text-soft)}
        .nav-inactive:hover{background:var(--hover);color:var(--text)}
        .sidebar-glow{box-shadow:inset -1px 0 0 var(--border-soft)}

        /* ---- Entrance animation for page content ---- */
        @keyframes fadeUp{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
        .page-enter{animation:fadeUp .5s ease both}
    </style>
</head>
<body class="antialiased overflow-hidden" style="height:100vh">

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
           style="background:var(--panel);border-right:1px solid var(--border)"
           :class="mob?'translate-x-0 shadow-2xl':'-translate-x-full lg:translate-x-0'">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-5 flex-shrink-0" style="border-bottom:1px solid var(--border-soft)">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                     style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm leading-tight" style="color:var(--text-strong)">{{ config('app.name') }}</p>
                    <p class="text-xs leading-tight" style="color:var(--text-soft)">Organizer Portal</p>
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

            <div class="py-3"><div class="h-px" style="background:var(--border-soft)"></div></div>

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
        <div class="p-3 flex-shrink-0" style="border-top:1px solid var(--border-soft)">
            {{-- Theme toggle --}}
            <button onclick="toggleTheme()"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition nav-inactive text-left mb-1">
                <svg id="icon-moon" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg id="icon-sun" class="w-4 h-4 flex-shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="text-sm font-medium" id="theme-label">Dark mode</span>
            </button>

            <div x-data="{open:false}" class="relative">
                <button @click="open=!open"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition nav-inactive text-left">
                    <div class="w-8 h-8 rounded-full text-white text-xs font-bold flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" style="color:var(--text-strong)">{{ auth()->user()->name }}</p>
                        <p class="text-xs truncate" style="color:var(--text-soft)">{{ auth()->user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 flex-shrink-0 transition" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" @click.outside="open=false"
                     class="absolute bottom-full left-0 right-0 mb-1 rounded-xl overflow-hidden shadow-2xl border"
                     style="background:var(--panel-2);border-color:var(--border)"
                     x-transition:enter="transition duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm nav-inactive transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm nav-inactive transition">
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
             style="background:var(--panel);border-bottom:1px solid var(--border-soft)">
            <button @click="mob=!mob" class="nav-inactive p-2 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <p class="font-semibold text-sm" style="color:var(--text-strong)">{{ config('app.name') }}</p>
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="page-enter">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<script>
    function toggleTheme(){
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        syncThemeUI();
    }
    function syncThemeUI(){
        const dark = document.documentElement.classList.contains('dark');
        const moon = document.getElementById('icon-moon');
        const sun  = document.getElementById('icon-sun');
        const lbl  = document.getElementById('theme-label');
        if (moon) moon.classList.toggle('hidden', !dark);
        if (sun)  sun.classList.toggle('hidden', dark);
        if (lbl)  lbl.textContent = dark ? 'Dark mode' : 'Light mode';
    }
    syncThemeUI();
</script>

@livewireScripts
</body>
</html>