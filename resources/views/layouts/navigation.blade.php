{{-- Standalone top nav — fallback only. Main nav is inside app.blade.php sidebar. --}}
<nav class="bg-white border-b border-slate-200 sticky top-0 z-40"
     style="font-family:'Outfit',sans-serif"
     x-data="{ mob: false, prof: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">

            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center"
                     style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="font-semibold text-slate-900 text-sm">Event Assistance</span>
            </a>

            <div class="hidden sm:flex items-center gap-1">
                @foreach([
                    ['route'=>'dashboard',    'label'=>'Dashboard'],
                    ['route'=>'events.index', 'label'=>'Events'],
                    ['route'=>'guests.index', 'label'=>'Guests'],
                ] as $l)
                <a href="{{ route($l['route']) }}"
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition
                          {{ request()->routeIs($l['route'].'*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    {{ $l['label'] }}
                </a>
                @endforeach
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('events.create') }}"
                   class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white"
                   style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    + New Event
                </a>
                <button @click="mob=!mob" class="sm:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mob" x-transition class="sm:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
        @foreach([
            ['route'=>'dashboard',    'label'=>'Dashboard'],
            ['route'=>'events.index', 'label'=>'Events'],
            ['route'=>'guests.index', 'label'=>'Guests'],
        ] as $l)
        <a href="{{ route($l['route']) }}"
           class="block px-3 py-2.5 rounded-xl text-sm font-medium transition
                  {{ request()->routeIs($l['route'].'*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">
            {{ $l['label'] }}
        </a>
        @endforeach
    </div>
</nav>
