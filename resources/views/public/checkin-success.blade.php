<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>*{font-family:'Outfit',sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-8 text-white text-center" style="background:linear-gradient(135deg,#059669,#047857)">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h1 class="text-2xl font-bold">Welcome, {{ $guestName }}!</h1>
                <p class="text-emerald-200 mt-1">Checked in at {{ $checkedInAt->format('H:i') }}</p>
            </div>
            <div class="p-6">
                <h2 class="font-semibold text-slate-800 mb-4">{{ $event->title }}</h2>
                @if($todaySchedule->count())
                <h3 class="text-sm font-medium text-slate-600 mb-3">Today's Program</h3>
                <div class="space-y-2">
                    @foreach($todaySchedule as $session)
                    <div class="flex gap-3 py-2 border-b border-slate-100 last:border-0">
                        <span class="text-xs text-slate-400 font-mono w-20 flex-shrink-0">{{ $session->start_time }}</span>
                        <span class="text-sm text-slate-700">{{ $session->session_name }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
                <p class="text-center text-slate-400 text-sm mt-6">Enjoy the event!</p>
            </div>
        </div>
    </div>
</body>
</html>
