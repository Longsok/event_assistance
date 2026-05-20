<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $stats['checked_in'] ?? 0 }}</p>
        <p class="text-sm text-gray-500 mt-1">Checked In</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] ?? 0 }}</p>
        <p class="text-sm text-gray-500 mt-1">Expected</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        @php $rate = ($stats['expected'] ?? 0) > 0 ? round((($stats['checked_in'] ?? 0) / $stats['expected']) * 100) : 0; @endphp
        <p class="text-3xl font-bold text-indigo-600">{{ $rate }}%</p>
        <p class="text-sm text-gray-500 mt-1">Attendance Rate</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 text-center">
        <p class="text-3xl font-bold text-orange-500">{{ ($stats['expected'] ?? 0) - ($stats['checked_in'] ?? 0) }}</p>
        <p class="text-sm text-gray-500 mt-1">Not Arrived</p>
    </div>
    <div class="col-span-2 lg:col-span-4 flex items-center justify-center gap-2 text-sm text-green-600">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        Live — updates every 3 seconds
    </div>
</div>
