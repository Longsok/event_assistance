<<<<<<< HEAD
{{--
  navigation.blade.php
  Included inside app.blade.php only if needed as standalone top nav.
  In the new sidebar layout this file is unused — all nav is in app.blade.php.
  Kept here as fallback / reference.
--}}
<nav class="bg-white border-b border-slate-200 sticky top-0 z-40"
     style="font-family:'Outfit',sans-serif"
     x-data="{ mob: false, prof: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14">

            {{-- Logo --}}
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

            {{-- Desktop links --}}
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

            {{-- Right --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('events.create') }}"
                   class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white"
                   style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Event
                </a>

                <div class="relative">
                    <button @click="prof=!prof"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 transition">
                        <div class="w-7 h-7 rounded-lg text-xs font-bold text-white flex items-center justify-center"
                             style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="prof" @click.outside="prof=false"
                         x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-1 w-44 bg-white border border-slate-200 rounded-xl shadow-xl overflow-hidden z-50">
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center gap-2.5 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 transition">
                            Profile
                        </a>
                        <div class="h-px bg-slate-100"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile menu --}}
                <button @click="mob=!mob" class="sm:hidden p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
=======
<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
                    </svg>
                </button>
            </div>
        </div>
    </div>

<<<<<<< HEAD
    {{-- Mobile menu --}}
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
        <a href="{{ route('events.create') }}"
           class="block px-3 py-2.5 rounded-xl text-sm font-medium text-white text-center"
           style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            Create New Event
        </a>
=======
    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
>>>>>>> 7f1e22f2e341e4a9e9bb2a7e5438216ed5625882
    </div>
</nav>
