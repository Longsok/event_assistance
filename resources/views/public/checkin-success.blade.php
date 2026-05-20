<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-600 px-6 py-8 text-white text-center">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <span class="text-4xl">✅</span>
                </div>
                <h1 class="text-2xl font-bold">Welcome, {{ $guestName }}!</h1>
                <p class="text-green-200 mt-1">Checked in at {{ $checkedInAt->format('H:i') }}</p>
            </div>
            <div class="p-6">
                <h2 class="font-semibold text-gray-800 mb-4">{{ $event->title }}</h2>
                @if($todaySchedule->count())
                <h3 class="text-sm font-medium text-gray-600 mb-3">Today's Program</h3>
                <div class="space-y-2">
                    @foreach($todaySchedule as $session)
                    <div class="flex gap-3 py-2 border-b border-gray-100 last:border-0">
                        <span class="text-xs text-gray-400 font-mono w-20 flex-shrink-0">{{ $session->start_time }}</span>
                        <span class="text-sm text-gray-700 {{ $session->is_break ? 'italic text-gray-400' : '' }}">
                            {{ $session->session_name }}
                        </span>
                    </div>
                    @endforeach
                </div>
                @endif
                <p class="text-center text-gray-400 text-sm mt-6">Enjoy the event! 🎉</p>
            </div>
        </div>
    </div>
</body>
</html>
