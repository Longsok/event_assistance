<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Apply saved theme before paint --}}
    <script>
        (function(){
            if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
            else document.documentElement.classList.add('dark');
        })();
    </script>
    {{-- Apply saved theme before paint --}}
    <script>
        (function(){
            if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
            else document.documentElement.classList.add('dark');
        })();
    </script>
    @stack('styles')
    <style>
        *{font-family:'Outfit',sans-serif}

        :root{
            --bg:#030712; --panel:#111827; --panel-input:#1f2937;
            --text:#f1f5f9; --text-strong:#ffffff; --text-soft:#9ca3af;
            --border:#1f2937; --input-bg:#1f2937; --input-border:#374151;
            --hover:rgba(255,255,255,.04);
        }
        html:not(.dark){
            --bg:#e7e9f2; --panel:#ffffff; --panel-input:#f1f3f9;
            --text:#1e293b; --text-strong:#0f172a; --text-soft:#64748b;
            --border:rgba(15,23,42,.10); --input-bg:#f1f3f9; --input-border:rgba(15,23,42,.18);
            --hover:rgba(15,23,42,.04);
        }
        body{background:var(--bg);color:var(--text);transition:background .3s ease,color .3s ease}
        .admin-nav-inactive{color:var(--text-soft)}
        .admin-nav-inactive:hover{background:var(--hover);color:var(--text-strong)}
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
         x-transition:leave-end="opacity-0"
         style="display:none"></div>


    <aside class="fixed inset-y-0 left-0 w-64 z-30 flex-shrink-0 flex flex-col overflow-hidden border-r
                  transition-transform duration-300 lg:relative lg:translate-x-0"
           style="background:var(--panel);border-color:var(--border)"
           :class="mob?'translate-x-0 shadow-2xl':'-translate-x-full lg:translate-x-0'">
        <div class="h-16 flex items-center px-5 flex-shrink-0" style="border-bottom:1px solid var(--border)">
            <div>
                <p class="text-sm font-bold leading-tight" style="color:var(--text-strong)">Admin Panel</p>
                <p class="text-xs leading-tight" style="color:var(--text-soft)">{{ config('app.name') }}</p>
            </div>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
            $fn=fn($route,$label,$icon)=>['route'=>$route,'label'=>$label,'icon'=>$icon,'active'=>request()->routeIs($route.'*')];
            $links=[
                $fn('admin.dashboard','Dashboard','M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'),
                $fn('admin.users.index','Users','M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'),
                $fn('admin.events.index','Events','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'),
                $fn('admin.categories.index','Categories','M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'),
                $fn('admin.category-templates.index','Category Templates','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'),
                $fn('admin.budget-templates.index','Budget Templates','M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'),
                $fn('admin.schedule-templates.index','Schedule Templates','M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'),
                $fn('admin.task-groups.index','Task Groups','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'),
            ];
            @endphp
            @foreach($links as $link)
            <a href="{{ route($link['route']) }}" @click="mob=false" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition
               {{ $link['active'] ? 'bg-indigo-600/20 text-indigo-400 border border-indigo-600/30' : 'admin-nav-inactive' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $link['icon'] }}"/>
                </svg>
                {{ $link['label'] }}
            </a>
            @endforeach
        </nav>
        <div class="p-4 flex-shrink-0 space-y-3" style="border-top:1px solid var(--border)">
            {{-- Theme toggle --}}
            <button onclick="toggleTheme()"
                    class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition admin-nav-inactive">
                <svg id="icon-moon" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                <svg id="icon-sun" class="w-4 h-4 flex-shrink-0 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>

                <span id="theme-label">Dark mode</span>
            </button>

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr(Auth::guard('admin')->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate" style="color:var(--text-strong)">{{ Auth::guard('admin')->user()->name }}</p>
                    <p class="text-xs truncate" style="color:var(--text-soft)">{{ Auth::guard('admin')->user()->email }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition admin-nav-inactive"
                        onmouseover="this.style.color='#f87171'" onmouseout="this.style.color=''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <header class="h-16 flex items-center gap-3 px-4 sm:px-6 flex-shrink-0" style="background:var(--panel);border-bottom:1px solid var(--border)">
            {{-- Hamburger (mobile only) --}}
            <button @click="mob=!mob" class="lg:hidden p-2 rounded-lg transition admin-nav-inactive">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-base font-semibold" style="color:var(--text-strong)">{{ $title ?? 'Dashboard' }}</h1>
        </header>
        <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-6">
            @if(session('success'))
            <div class="mb-5 flex items-center gap-2 text-sm px-4 py-3 rounded-lg" style="background:rgba(16,185,129,.12);border:1px solid rgba(52,211,153,.3);color:#34d399">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-5 flex items-center gap-2 text-sm px-4 py-3 rounded-lg" style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.3);color:#f87171">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif
            {{ $slot }}
        </div>
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
@stack('scripts')
</body>
</html>
