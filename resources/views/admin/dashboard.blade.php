<x-admin-layout title="Dashboard">

    {{-- ── Stats Grid ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        @php
            $stats = [
                ['label' => 'Total Users',     'value' => $stat['total_users'],    'color' => 'indigo',  'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ['label' => 'Total Events',    'value' => $stat['total_events'],   'color' => 'violet',  'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ['label' => 'Total Guests',    'value' => $stat['total_guests'],   'color' => 'sky',     'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['label' => 'Ongoing Events',  'value' => $stat['ongoing_events'], 'color' => 'emerald', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
            ];
            $colorMap = [
                'indigo'  => ['bg' => 'bg-indigo-600/20',  'icon' => 'text-indigo-400',  'border' => 'border-indigo-600/30'],
                'violet'  => ['bg' => 'bg-violet-600/20',  'icon' => 'text-violet-400',  'border' => 'border-violet-600/30'],
                'sky'     => ['bg' => 'bg-sky-600/20',     'icon' => 'text-sky-400',     'border' => 'border-sky-600/30'],
                'emerald' => ['bg' => 'bg-emerald-600/20', 'icon' => 'text-emerald-400', 'border' => 'border-emerald-600/30'],
            ];
        @endphp

        @foreach ($stats as $s)
            @php $c = $colorMap[$s['color']]; @endphp
            <div class="bg-gray-900 border border-gray-800 rounded-xl p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 {{ $c['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $s['icon'] }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ number_format($s['value']) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Two-column grid ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">

        {{-- Recent Events --}}
        <div class="xl:col-span-2 bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="text-sm font-semibold text-white">Recent Events</h2>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-800">
                            <th class="px-6 py-3 text-left">Event</th>
                            <th class="px-6 py-3 text-left">Organizer</th>
                            <th class="px-6 py-3 text-left">Category</th>
                            <th class="px-6 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse ($recentEvents as $event)
                            <tr class="hover:bg-gray-800/40 transition">
                                <td class="px-6 py-3 text-white font-medium max-w-[200px] truncate">
                                    {{ $event->title }}
                                </td>
                                <td class="px-6 py-3 text-gray-400">
                                    {{ $event->user?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3 text-gray-400">
                                    {{ $event->category?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-3">
                                    @php
                                        $statusColor = match($event->status ?? '') {
                                            'ongoing'   => 'bg-emerald-900/40 text-emerald-400 border-emerald-700/50',
                                            'completed' => 'bg-gray-700/40 text-gray-400 border-gray-600/50',
                                            'cancelled' => 'bg-red-900/40 text-red-400 border-red-700/50',
                                            default     => 'bg-indigo-900/40 text-indigo-400 border-indigo-700/50',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs border {{ $statusColor }}">
                                        {{ ucfirst($event->status ?? 'draft') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-600 text-sm">No events yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Users --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
                <h2 class="text-sm font-semibold text-white">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition">View all</a>
            </div>
            <ul class="divide-y divide-gray-800">
                @forelse ($recentUsers as $user)
                    <li class="flex items-center gap-3 px-6 py-3 hover:bg-gray-800/40 transition">
                        <div class="w-8 h-8 rounded-full bg-indigo-600/30 border border-indigo-600/40 flex items-center justify-center text-xs font-bold text-indigo-300 shrink-0">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        @if ($user->hasRole('admin'))
                            <span class="ml-auto shrink-0 text-xs bg-indigo-900/40 text-indigo-400 border border-indigo-700/40 px-2 py-0.5 rounded-full">Admin</span>
                        @endif
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-gray-600 text-sm">No users yet</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ── Events by Category + Activity ──────────────────────────────── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

        {{-- Events by Category --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-sm font-semibold text-white">Events by Category</h2>
            </div>
            <ul class="divide-y divide-gray-800 px-6 py-2">
                @forelse ($eventByCategory as $cat)
                    <li class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $cat->color ?? '#6366f1' }}"></span>
                            <span class="text-sm text-gray-300">{{ $cat->name }}</span>
                        </div>
                        <span class="text-sm font-semibold text-white">{{ $cat->events_count }}</span>
                    </li>
                @empty
                    <li class="py-8 text-center text-gray-600 text-sm">No categories with events yet</li>
                @endforelse
            </ul>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-sm font-semibold text-white">Recent Activity</h2>
            </div>
            <ul class="divide-y divide-gray-800">
                @forelse ($recentActivity as $activity)
                    <li class="flex items-start gap-3 px-6 py-3">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-indigo-400">{{ $activity['type'] }}</p>
                            <p class="text-sm text-gray-300 truncate">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-600 mt-0.5">
                                {{ \Carbon\Carbon::parse($activity['time'])->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-gray-600 text-sm">No recent activity</li>
                @endforelse
            </ul>
        </div>
    </div>

</x-admin-layout>