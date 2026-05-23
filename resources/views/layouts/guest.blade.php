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
    <style>*{font-family:'Outfit',sans-serif}</style>
</head>
<body class="antialiased min-h-screen flex" style="background:#0f0e1a">

    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col relative overflow-hidden"
         style="background:linear-gradient(160deg,#1e1b4b 0%,#312e81 40%,#4c1d95 100%)">
        <div class="absolute inset-0 pointer-events-none" style="background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:48px 48px"></div>
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(139,92,246,.35),transparent 70%)"></div>
        <div class="absolute -bottom-40 -left-20 w-96 h-96 rounded-full pointer-events-none" style="background:radial-gradient(circle,rgba(79,70,229,.25),transparent 70%)"></div>
        <div class="relative z-10 flex flex-col flex-1 p-12">
            <span class="font-semibold text-white text-lg">{{ config('app.name') }}</span>
            <div class="flex-1 flex flex-col justify-center">
                <p class="text-violet-300 text-sm font-medium mb-3 tracking-wide uppercase">Organizer Platform</p>
                <h1 class="text-4xl xl:text-5xl font-extrabold text-white leading-tight mb-6">
                    Run events<br>without the chaos
                </h1>
                <p class="text-indigo-200 text-base leading-relaxed mb-10 max-w-xs">
                    From invites to check-ins, tasks to budgets — manage everything in one focused workspace.
                </p>
                <div class="space-y-4">
                    @foreach([
                        ['icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','text'=>'Guest management with QR check-in'],
                        ['icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4','text'=>'Auto-generated task timelines'],
                        ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','text'=>'Real-time budget tracking'],
                    ] as $f)
                    <div class="flex items-center gap-4">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(99,102,241,.25)">
                            <svg class="w-4 h-4" style="color:#a5b4fc" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-indigo-100 text-sm">{{ $f['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="rounded-2xl p-4 border border-white/10" style="background:rgba(255,255,255,.06)">
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

    <div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 relative">
        <div class="absolute inset-0 pointer-events-none" style="background-image:linear-gradient(rgba(99,102,241,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.025) 1px,transparent 1px);background-size:48px 48px"></div>
        <div class="lg:hidden flex items-center gap-2.5 mb-10">
            <span class="font-semibold text-white">{{ config('app.name') }}</span>
        </div>
        <div class="relative w-full max-w-sm">
            <div class="rounded-2xl px-8 py-9 border border-slate-800" style="background:rgba(15,23,42,.8)">
                {{ $slot }}
            </div>
            <p class="text-center text-xs text-slate-600 mt-6">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
