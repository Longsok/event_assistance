<x-admin-layout title="Dashboard">
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        @php
        $cards=[
            ['label'=>'Total Users','value'=>$stat['total_users'],'color'=>'text-indigo-400','border'=>'border-indigo-600/30','bg'=>'bg-indigo-600/10','icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
            ['label'=>'Total Events','value'=>$stat['total_events'],'color'=>'text-violet-400','border'=>'border-violet-600/30','bg'=>'bg-violet-600/10','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['label'=>'Total Guests','value'=>$stat['total_guests'],'color'=>'text-sky-400','border'=>'border-sky-600/30','bg'=>'bg-sky-600/10','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label'=>'Ongoing Events','value'=>$stat['ongoing_events'],'color'=>'text-emerald-400','border'=>'border-emerald-600/30','bg'=>'bg-emerald-600/10','icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="rounded-xl p-5 flex items-center gap-4 border" style="background:var(--panel);border-color:var(--border)">
            <div class="w-11 h-11 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 {{ $c['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $c['icon'] }}"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold" style="color:var(--text-strong)">{{ number_format($c['value']) }}</p>
                <p class="text-xs mt-0.5" style="color:var(--text-soft)">{{ $c['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
        <div class="xl:col-span-2 rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
            <div class="flex items-center justify-between px-6 py-4" style="border-bottom:1px solid var(--border)">
                <h3 class="font-semibold" style="color:var(--text-strong)">Recent Events</h3>
                <a href="{{ route('admin.events.index') }}" class="text-xs text-indigo-400 hover:text-indigo-300">View all</a>
            </div>
            @forelse($recentEvents as $event)
            <a href="{{ route('admin.events.show', $event) }}" class="flex items-center gap-4 px-6 py-4 transition"
               style="border-bottom:1px solid var(--border)"
               onmouseover="this.style.background='var(--hover)'" onmouseout="this.style.background='transparent'">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate" style="color:var(--text-strong)">{{ $event->title }}</p>
                    <p class="text-xs mt-0.5" style="color:var(--text-soft)">{{ $event->user->name ?? '-' }} &middot; {{ $event->category->name ?? '-' }}</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full {{ $event->status==='ongoing' ? 'bg-emerald-900/40 text-emerald-400' : 'bg-gray-800 text-gray-400' }}">{{ $event->status }}</span>
            </a>
            @empty
            <p class="px-6 py-8 text-center text-sm" style="color:var(--text-soft)">No events yet.</p>
            @endforelse
        </div>

        <div class="rounded-xl border overflow-hidden" style="background:var(--panel);border-color:var(--border)">
            <div class="px-6 py-4" style="border-bottom:1px solid var(--border)">
                <h3 class="font-semibold" style="color:var(--text-strong)">Recent Users</h3>
            </div>
            @forelse($recentUsers as $user)
            <div class="flex items-center gap-3 px-5 py-3.5" style="border-bottom:1px solid var(--border)">
                <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-bold text-white flex-shrink-0">
                    {{ strtoupper(substr($user->name,0,1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm truncate" style="color:var(--text-strong)">{{ $user->name }}</p>
                    <p class="text-xs truncate" style="color:var(--text-soft)">{{ $user->email }}</p>
                </div>
            </div>
            @empty
            <p class="px-5 py-8 text-center text-sm" style="color:var(--text-soft)">No users yet.</p>
            @endforelse
        </div>
    </div>
</x-admin-layout>