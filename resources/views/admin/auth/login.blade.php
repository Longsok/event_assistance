<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login — {{ config('app.name') }}</title>
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Apply saved theme before paint --}}
    <script>
        (function(){
            if (localStorage.getItem('theme') === 'light') document.documentElement.classList.remove('dark');
            else document.documentElement.classList.add('dark');
        })();
    </script>
    <style>
        *{font-family:'Outfit',sans-serif}

        :root{
            --bg:#030712; --panel:#111827; --panel-input:#1f2937;
            --text:#e5e7eb; --text-strong:#ffffff; --text-soft:#9ca3af;
            --border:#1f2937; --border-input:#374151; --grid:rgba(255,255,255,.025);
        }
        html:not(.dark){
            --bg:#e7e9f2; --panel:#ffffff; --panel-input:#f1f3f9;
            --text:#1e293b; --text-strong:#0f172a; --text-soft:#64748b;
            --border:rgba(15,23,42,.10); --border-input:rgba(15,23,42,.18); --grid:rgba(15,23,42,.05);
        }
        body{background:var(--bg);color:var(--text);transition:background .3s ease,color .3s ease}

        .admin-input{
            width:100%;background:var(--panel-input);border:1px solid var(--border-input);
            border-radius:8px;padding:10px 12px;color:var(--text);font-size:14px;outline:none;transition:border-color .15s;
        }
        .admin-input:focus{ border-color:#6366f1; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">

    {{-- Background effects --}}
    <div class="fixed inset-0 pointer-events-none" style="z-index:0">
        <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:800px;height:500px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.18),transparent 70%)"></div>
        <div style="position:absolute;inset:0;background-image:linear-gradient(var(--grid) 1px,transparent 1px),linear-gradient(90deg,var(--grid) 1px,transparent 1px);background-size:56px 56px"></div>
    </div>

    {{-- Theme toggle (top-right) --}}
    <button onclick="toggleTheme()" aria-label="Toggle theme"
            class="fixed top-5 right-5 z-10 w-9 h-9 rounded-xl flex items-center justify-center border transition"
            style="border-color:var(--border);background:var(--panel);color:var(--text)">
        <svg id="icon-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
        <svg id="icon-sun" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
    </button>

    <div class="w-full max-w-sm relative z-10">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl mx-auto mb-4 flex items-center justify-center" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);box-shadow:0 8px 24px rgba(79,70,229,.4)">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold" style="color:var(--text-strong)">Admin Login</h1>
            <p class="text-sm mt-1" style="color:var(--text-soft)">{{ config('app.name') }} Administration</p>
        </div>
        <div class="rounded-2xl border px-7 py-8 shadow-xl" style="background:var(--panel);border-color:var(--border)">
            @if(session('status'))
            <div class="mb-5 text-sm px-4 py-3 rounded-lg" style="background:rgba(16,185,129,.12);border:1px solid rgba(52,211,153,.3);color:#34d399">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="admin-input">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-1.5" style="color:var(--text-soft)">Password</label>
                    <input type="password" name="password" required class="admin-input">
                </div>
                <button type="submit" class="w-full py-2.5 text-white rounded-lg text-sm font-semibold transition"
                        style="background:linear-gradient(135deg,#4f46e5,#7c3aed)"
                        onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                    Sign in
                </button>
            </form>
        </div>
        <p class="text-center text-xs mt-5" style="color:var(--text-soft)">Authorized administrators only.</p>
    </div>

    <script>
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
        syncIcons();
    </script>
</body>
</html>