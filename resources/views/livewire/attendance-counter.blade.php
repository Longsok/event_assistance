<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="rounded-2xl border p-5 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <p class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] ?? 0 }}</p>
        <p class="text-sm mt-1" style="color:#6b7280">Checked In</p>
    </div>
    <div class="rounded-2xl border p-5 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
        <p class="text-sm mt-1" style="color:#6b7280">Expected</p>
    </div>
    <div class="rounded-2xl border p-5 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        @php $rate = ($stats['total'] ?? 0) > 0 ? round((($stats['checked_in'] ?? 0) / $stats['total']) * 100) : 0; @endphp
        <p class="text-3xl font-bold text-indigo-600">{{ $rate }}%</p>
        <p class="text-sm mt-1" style="color:#6b7280">Attendance Rate</p>
    </div>
    <div class="rounded-2xl border p-5 text-center" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <p class="text-3xl font-bold text-orange-500">{{ ($stats['total'] ?? 0) - ($stats['checked_in'] ?? 0) }}</p>
        <p class="text-sm mt-1" style="color:#6b7280">Not Arrived</p>
    </div>
    <div class="col-span-2 lg:col-span-4 flex items-center justify-center gap-2 text-sm text-green-600">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        Live — updates every 3 seconds
    </div>
</div>
