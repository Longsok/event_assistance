<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Event Assistance') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .nav-active { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: #fff; box-shadow: 0 4px 12px rgba(79,70,229,.3); }
        .nav-inactive { color: #64748b; }
        .nav-inactive:hover { background: #f1f5f9; color: #1e293b; }
        .page-in { animation: pi .25s ease; }
        @keyframes pi { from { opacity:0; transform:translateY(8px); } to { opacity:1; transform:translateY(0); } }
        ::-webkit-scrollbar { width:4px; }
        ::-webkit-scrollbar-thumb { background:#e2e8f0; border-radius:4px; }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900">

<div class="flex min-h-screen" x-data="{ mob: false, prof: false }">

    {{-- Mobile overlay --}}
    <div x-show="mob" @click="mob=false"
         class="fixed inset-0 bg-black/40 backdrop-blur-sm z-20 lg:hidden"
         x-transition:enter="transition duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-30 flex flex-col
                  transition-transform duration-300 lg:static lg:translate-x-0"
           :class="mob ? 'translate-x-0 shadow-2xl' : '-translate-x-full'">

        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-100">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-slate-900 text-sm leading-tight truncate">Event Assistance</p>
                <p class="text-xs text-slate-400 leading-tight">Organizer Portal</p>
            </div>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            @php
            $links = [
                ['route'=>'dashboard',    'label'=>'Dashboard',   'icon'=>'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                ['route'=>'events.index','label'=>'My Events',    'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['route'=>'guests.index','label'=>'Guest Book',   'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ];
            @endphp

            @foreach($links as $l)
            @php $a = request()->routeIs($l['route'].'*'); @endphp
            <a href="{{ route($l['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $a ? 'nav-active' : 'nav-inactive' }}">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $l['icon'] }}"/>
                </svg>
                {{ $l['label'] }}
            </a>
            @endforeach

            <div class="pt-4 pb-1"><div class="h-px bg-slate-100"></div></div>

            <a href="{{ route('events.create') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                      text-indigo-600 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Event
            </a>
        </nav>

        <div class="border-t border-slate-100 p-3">
            <div class="relative">
                <button @click="prof=!prof"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition text-left">
                    <div class="w-8 h-8 rounded-xl text-xs font-bold text-white flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0"
                         :class="prof?'rotate-180':''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="prof" @click.outside="prof=false"
                     x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                     class="absolute bottom-full left-0 right-0 mb-1 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden z-50">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile Settings
                    </a>
                    <div class="h-px bg-slate-100"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-10 flex items-center gap-4 px-4 lg:px-6">
            <button @click="mob=true" class="lg:hidden p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex-1"></div>
            <a href="{{ route('events.create') }}"
               class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white"
               style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 2px 8px rgba(79,70,229,.3)">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                New Event
            </a>
            <div class="w-8 h-8 rounded-xl text-xs font-bold text-white flex items-center justify-center"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                {{ strtoupper(substr(Auth::user()->name,0,1)) }}
            </div>
        </header>

        @if(session('success'))
        <div class="mx-6 mt-5 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mx-6 mt-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        <main class="flex-1 page-in">{{ $slot }}</main>

        <footer class="px-6 py-3 text-xs text-slate-400 border-t border-slate-100 text-center">
            {{ config('app.name') }} &copy; {{ date('Y') }}
        </footer>
    </div>
</div>

@livewireScripts
</body>
</html>
