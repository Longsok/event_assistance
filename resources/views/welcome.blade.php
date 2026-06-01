<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} — Smart Event Management</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{font-family:'Outfit',sans-serif}

        /* ---- Theme variables (dark default, light overrides) ---- */
        :root{
            --bg:#080b14; --bg-soft:rgba(255,255,255,.04); --bg-nav:rgba(8,11,20,.85);
            --text:#e2e8f0; --text-strong:#ffffff; --text-soft:#94a3b8;
            --border:rgba(255,255,255,.08); --grid:rgba(255,255,255,.025);
        }
        html:not(.dark){
            --bg:#e7e9f2; --bg-soft:rgba(15,23,42,.05); --bg-nav:rgba(231,233,242,.85);
            --text:#1e293b; --text-strong:#0f172a; --text-soft:#5b657a;
            --border:rgba(15,23,42,.12); --grid:rgba(15,23,42,.05);
        }
        body{background:var(--bg);color:var(--text);transition:background .3s ease,color .3s ease}

        .btn-glow{background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 4px 16px rgba(79,70,229,.4);transition:all .2s ease}
        .btn-glow:hover{transform:translateY(-1px);box-shadow:0 8px 24px rgba(79,70,229,.5)}
        .card-hover{transition:transform .2s ease,box-shadow .2s ease}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.25)}
        .gradient-text{
            background:linear-gradient(135deg,#a5b4fc,#c4b5fd);
            -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent
        }
        html:not(.dark) .gradient-text{
            background:linear-gradient(135deg,#4f46e5,#7c3aed);
            -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent
        }

        @keyframes pulse{0%,100%{opacity:.5}50%{opacity:1}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
        .fade-up{animation:fadeUp .7s ease both}
        .fade-up-2{animation:fadeUp .7s ease .15s both}
        .fade-up-3{animation:fadeUp .7s ease .3s both}
        .floaty{animation:float 6s ease-in-out infinite}

        /* ---- Scroll reveal ---- */
        .reveal{opacity:0;transform:translateY(28px);transition:opacity .7s ease,transform .7s ease}
        .reveal.in{opacity:1;transform:translateY(0)}

        /* ---- Slideshow ---- */
        .slides{position:relative;border-radius:1rem;overflow:hidden;border:1px solid var(--border);min-height:300px}
        .slide{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 24px;text-align:center;opacity:0;transition:opacity .8s ease;pointer-events:none}
        .slide.active{opacity:1;pointer-events:auto}
        .slide-dots{display:flex;gap:8px;justify-content:center;margin-top:16px}
        .dot{width:9px;height:9px;border-radius:50%;background:var(--text-soft);opacity:.4;cursor:pointer;transition:all .25s ease}
        .dot.active{opacity:1;width:26px;border-radius:6px;background:#818cf8}
    </style>
</head>
<body class="antialiased overflow-x-hidden">

<div class="fixed inset-0 pointer-events-none" style="z-index:0">
    <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:900px;height:500px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.18),transparent 70%)"></div>
    <div style="position:absolute;inset:0;background-image:linear-gradient(var(--grid) 1px,transparent 1px),linear-gradient(90deg,var(--grid) 1px,transparent 1px);background-size:64px 64px"></div>
</div>

<nav class="relative z-10 border-b sticky top-0" style="border-color:var(--border);background:var(--bg-nav);backdrop-filter:blur(12px)">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <span class="font-bold" style="color:var(--text-strong)">{{ config('app.name') }}</span>
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" aria-label="Toggle theme"
                        class="w-9 h-9 rounded-xl flex items-center justify-center border transition"
                        style="border-color:var(--border);background:var(--bg-soft);color:var(--text)">
                    <svg id="icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg id="icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
                @auth
                    <a href="{{ auth()->user()->hasRole('admin') ? route('admin.dashboard') : route('dashboard') }}"
                       class="text-sm transition px-4 py-2" style="color:var(--text-soft)">Dashboard</a>
                @else
                    @if(Route::has('login'))
                    <a href="{{ route('login') }}" class="text-sm transition px-4 py-2" style="color:var(--text-soft)">Sign in</a>
                    @endif
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-glow text-sm text-white font-medium px-5 py-2 rounded-xl">Get started free</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-4 text-center">
    <div class="fade-up badge-pill inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-medium mb-8 border"
         style="background:rgba(79,70,229,.12);border-color:rgba(99,102,241,.3)">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400" style="animation:pulse 2s infinite"></span>
        Smart Event Assistance System
    </div>
    <h1 class="fade-up-2 text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-6" style="color:var(--text-strong)">
        Plan events with<br><span class="gradient-text">zero stress</span>
    </h1>
    <p class="fade-up-3 text-lg leading-relaxed mb-10 max-w-lg mx-auto" style="color:var(--text-soft)">
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
           style="background:var(--bg-soft);border-color:var(--border);color:var(--text)">Sign in</a>
        @endauth
    </div>
</section>

{{-- STATS BAR --}}
<section class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20">
    <div class="reveal grid grid-cols-3 gap-4">
        @php $stats=[['500+','Guests managed'],['200+','Events run'],['90%','Check-in success']]; @endphp
        @foreach($stats as $s)
        <div class="rounded-2xl p-5 text-center border" style="background:var(--bg-soft);border-color:var(--border)">
            <p class="text-2xl sm:text-3xl font-extrabold gradient-text">{{ $s[0] }}</p>
            <p class="text-xs mt-1" style="color:var(--text-soft)">{{ $s[1] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- SLIDESHOW --}}
<section class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <h2 class="reveal text-2xl sm:text-3xl font-bold text-center mb-8" style="color:var(--text-strong)">See it in action</h2>
    <div class="reveal slides" id="slideshow" style="background:var(--bg-soft)">
        @php
        $slideData=[
            ['Create your event','Set up venues, dates, capacity and categories in minutes.','#6366f1'],
            ['Invite & track guests','Send QR invitations and watch RSVPs roll in live.','#10b981'],
            ['Check in at the door','Scan QR codes for instant, contactless attendance.','#f59e0b'],
            ['Review the results','Get attendance rates and budget breakdowns after.','#8b5cf6'],
        ];
        @endphp
        @foreach($slideData as $i => $sl)
        <div class="slide {{ $i===0 ? 'active' : '' }}">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 floaty" style="background:{{ $sl[2] }}22;border:1px solid {{ $sl[2] }}55">
                <span class="text-2xl font-extrabold" style="color:{{ $sl[2] }}">{{ $i+1 }}</span>
            </div>
            <h3 class="text-xl font-bold mb-2" style="color:var(--text-strong)">{{ $sl[0] }}</h3>
            <p class="text-sm max-w-sm" style="color:var(--text-soft)">{{ $sl[1] }}</p>
        </div>
        @endforeach
    </div>
    <div class="slide-dots" id="slide-dots">
        @foreach($slideData as $i => $sl)
        <span class="dot {{ $i===0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})"></span>
        @endforeach
    </div>
</section>

{{-- FEATURES --}}
<section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <h2 class="reveal text-2xl sm:text-3xl font-bold text-center mb-10" style="color:var(--text-strong)">Everything you need</h2>
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
        <div class="reveal card-hover rounded-2xl p-6 border" style="background:var(--bg-soft);border-color:var(--border)">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" style="background:{{ $f['glow'] }};border:1px solid {{ $f['accent'] }}30">
                <svg class="w-5 h-5" style="color:{{ $f['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $f['icon'] }}"/>
                </svg>
            </div>
            <h3 class="font-semibold text-base mb-2" style="color:var(--text-strong)">{{ $f['title'] }}</h3>
            <p class="text-sm leading-relaxed" style="color:var(--text-soft)">{{ $f['desc'] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <h2 class="reveal text-2xl sm:text-3xl font-bold text-center mb-10" style="color:var(--text-strong)">How it works</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @php $steps=[['1','Create','Set up your event and details'],['2','Invite','Share QR invitations with guests'],['3','Run','Check in guests and track everything live']]; @endphp
        @foreach($steps as $st)
        <div class="reveal text-center">
            <div class="w-12 h-12 rounded-full mx-auto mb-4 flex items-center justify-center text-lg font-extrabold text-white" style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">{{ $st[0] }}</div>
            <h3 class="font-semibold mb-1" style="color:var(--text-strong)">{{ $st[1] }}</h3>
            <p class="text-sm" style="color:var(--text-soft)">{{ $st[2] }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="reveal rounded-2xl px-8 py-14 text-center border" style="background:linear-gradient(135deg,rgba(79,70,229,.15),rgba(124,58,237,.08));border-color:rgba(99,102,241,.25)">
        <h2 class="text-2xl sm:text-3xl font-bold mb-3" style="color:var(--text-strong)">Ready to plan your next event?</h2>
        <p class="text-sm mb-8 max-w-md mx-auto" style="color:var(--text-soft)">
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

<footer class="relative z-10 border-t px-4 py-6 text-center text-xs" style="border-color:var(--border);color:var(--text-soft)">
    &copy; {{ date('Y') }} {{ config('app.name') }}. Built with Laravel &amp; Tailwind CSS.
</footer>

<script>
    // ---- Theme toggle ----
    (function(){
        const saved = localStorage.getItem('theme');
        if (saved === 'light') document.documentElement.classList.remove('dark');
        else document.documentElement.classList.add('dark');
        syncIcons();
    })();
    function toggleTheme(){
        const html = document.documentElement;
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        syncIcons();
    }
    function syncIcons(){
        const dark = document.documentElement.classList.contains('dark');
        document.getElementById('icon-moon').classList.toggle('hidden', !dark);
        document.getElementById('icon-sun').classList.toggle('hidden', dark);
    }

    // ---- Slideshow ----
    let current = 0;
    const slides = document.querySelectorAll('#slideshow .slide');
    const dots = document.querySelectorAll('#slide-dots .dot');
    function showSlide(n){
        current = (n + slides.length) % slides.length;
        slides.forEach((s,i)=>s.classList.toggle('active', i===current));
        dots.forEach((d,i)=>d.classList.toggle('active', i===current));
    }
    function goToSlide(n){ showSlide(n); resetTimer(); }
    let timer = setInterval(()=>showSlide(current+1), 4000);
    function resetTimer(){ clearInterval(timer); timer = setInterval(()=>showSlide(current+1), 4000); }

    // ---- Scroll reveal ----
    const io = new IntersectionObserver((entries)=>{
        entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el=>{
        // If already visible on load, reveal immediately; otherwise wait for scroll
        const r = el.getBoundingClientRect();
        if (r.top < window.innerHeight && r.bottom > 0) {
            el.classList.add('in');
        } else {
            io.observe(el);
        }
    });
</script>

</body>
</html>