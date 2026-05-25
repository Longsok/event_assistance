@php
    $style      = $inviteCard?->template_style ?? 'classic';
    $showAgenda = $inviteCard?->show_agenda    ?? false;
    $showVenue  = $inviteCard?->show_venue     ?? false;
    $showQr     = $inviteCard?->show_qr        ?? false;
    $customMsg  = $inviteCard?->custom_message  ?? '';

    $themes = [
        'classic' => [
            'card'      => 'linear-gradient(160deg,#1e1b6e 0%,#2d2980 60%,#1a1760 100%)',
            'accent'    => 'linear-gradient(90deg,#f59e0b,#fbbf24,#f59e0b)',
            'accentSolid'=> '#f59e0b',
            'badge'     => '#fbbf24',
            'sub'       => 'rgba(199,210,254,.75)',
            'divider'   => 'rgba(245,158,11,.2)',
            'iconBg'    => 'rgba(245,158,11,.15)',
            'codeBg'    => 'rgba(255,255,255,.06)',
            'codeText'  => 'rgba(199,210,254,.45)',
            'corner'    => 'rgba(245,158,11,.06)',
        ],
        'elegant' => [
            'card'      => 'linear-gradient(160deg,#3b0a1a 0%,#4c0519 60%,#2d0712 100%)',
            'accent'    => 'linear-gradient(90deg,#f43f5e,#fb7185,#f43f5e)',
            'accentSolid'=> '#f43f5e',
            'badge'     => '#fb7185',
            'sub'       => 'rgba(254,205,211,.75)',
            'divider'   => 'rgba(244,63,94,.2)',
            'iconBg'    => 'rgba(244,63,94,.15)',
            'codeBg'    => 'rgba(255,255,255,.06)',
            'codeText'  => 'rgba(254,205,211,.4)',
            'corner'    => 'rgba(244,63,94,.06)',
        ],
        'minimal' => [
            'card'      => 'linear-gradient(160deg,#0f1117 0%,#161b27 60%,#0a0d13 100%)',
            'accent'    => 'linear-gradient(90deg,#e2e8f0,#94a3b8,#e2e8f0)',
            'accentSolid'=> '#94a3b8',
            'badge'     => '#94a3b8',
            'sub'       => 'rgba(148,163,184,.75)',
            'divider'   => 'rgba(255,255,255,.08)',
            'iconBg'    => 'rgba(255,255,255,.07)',
            'codeBg'    => 'rgba(255,255,255,.04)',
            'codeText'  => 'rgba(100,116,139,.6)',
            'corner'    => 'rgba(255,255,255,.03)',
        ],
    ];
    $t = $themes[$style] ?? $themes['classic'];
@endphp

<div style="background:{{ $t['card'] }};position:relative;overflow:hidden;font-family:'Outfit',system-ui,sans-serif">

    {{-- Top accent bar --}}
    <div style="height:3px;background:{{ $t['accent'] }}"></div>

    {{-- Decorative corner circle --}}
    <div style="position:absolute;top:0;right:0;width:100px;height:100px;border-radius:0 0 0 100px;background:{{ $t['corner'] }};pointer-events:none"></div>
    <div style="position:absolute;bottom:0;left:0;width:60px;height:60px;border-radius:0 60px 0 0;background:{{ $t['corner'] }};pointer-events:none"></div>

    <div style="padding:22px 24px">

        {{-- Category badge --}}
        <div style="display:inline-flex;align-items:center;gap:5px;background:{{ $t['iconBg'] }};border:1px solid {{ $t['divider'] }};border-radius:20px;padding:3px 10px 3px 8px;margin-bottom:12px">
            <div style="width:5px;height:5px;border-radius:50%;background:{{ $t['badge'] }}"></div>
            <span style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:{{ $t['badge'] }}">
                {{ $event->category?->name ?? 'Event' }}
            </span>
        </div>

        {{-- Event title --}}
        <h2 style="color:white;font-size:22px;font-weight:800;margin:0 0 4px;line-height:1.15;letter-spacing:-.3px">
            {{ $event->title }}
        </h2>

        {{-- Custom message --}}
        @if($customMsg)
        <p style="font-size:12px;color:{{ $t['sub'] }};font-style:italic;margin:6px 0 0;line-height:1.5">
            "{{ $customMsg }}"
        </p>
        @endif

        {{-- Divider --}}
        <div style="height:1px;background:{{ $t['divider'] }};margin:14px 0"></div>

        {{-- Date row --}}
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <div style="width:22px;height:22px;border-radius:6px;background:{{ $t['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="12" height="12" fill="none" stroke="{{ $t['badge'] }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span style="color:white;font-size:13px;font-weight:500">
                {{ \Carbon\Carbon::parse($event->start_date)->format('D, M d Y') }}
                @if($event->start_date != $event->end_date)
                — {{ \Carbon\Carbon::parse($event->end_date)->format('M d Y') }}
                @endif
            </span>
        </div>

        {{-- Time row --}}
        @if($event->start_time)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <div style="width:22px;height:22px;border-radius:6px;background:{{ $t['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="12" height="12" fill="none" stroke="{{ $t['badge'] }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span style="color:{{ $t['sub'] }};font-size:13px">
                {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                @if($event->end_time) — {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }} @endif
            </span>
        </div>
        @endif

        {{-- Venue row --}}
        @if($showVenue)
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
            <div style="width:22px;height:22px;border-radius:6px;background:{{ $t['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="12" height="12" fill="none" stroke="{{ $t['badge'] }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                </svg>
            </div>
            <div>
                <span style="color:white;font-size:13px;font-weight:500">{{ $event->venue ?? 'Venue TBA' }}</span>
                @if($event->address)
                <span style="color:{{ $t['sub'] }};font-size:11px;display:block">{{ $event->address }}</span>
                @endif
            </div>
        </div>
        @endif

        {{-- Schedule --}}
        @if($showAgenda)
        @php $sessions = $event->schedules?->sortBy('start_time')->take(3) ?? collect(); @endphp
        @if($sessions->isNotEmpty())
        <div style="background:{{ $t['iconBg'] }};border:1px solid {{ $t['divider'] }};border-radius:10px;padding:10px 12px;margin:10px 0">
            <p style="font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['badge'] }};margin:0 0 8px">Schedule</p>
            @foreach($sessions as $session)
            <div style="display:flex;gap:10px;margin-bottom:5px;align-items:center">
                <span style="font-size:10px;font-family:monospace;color:{{ $t['sub'] }};width:48px;flex-shrink:0">
                    {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                </span>
                <span style="font-size:11px;color:white;font-weight:500">{{ $session->session_name }}</span>
            </div>
            @endforeach
        </div>
        @endif
        @endif

        {{-- Divider --}}
        <div style="height:1px;background:{{ $t['divider'] }};margin:14px 0 12px"></div>

        {{-- Guest name + QR --}}
        <div style="display:flex;align-items:flex-end;justify-content:space-between">
            <div>
                <p style="font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['badge'] }};margin:0 0 4px">
                    Invited Guest
                </p>
                <p style="color:white;font-size:20px;font-weight:800;margin:0;letter-spacing:-.2px">
                    {{ $guestName ?? 'Guest Name' }}
                </p>
            </div>
            @if($showQr)
            <div style="background:white;padding:7px;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,.3)">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(64)->generate(
                    isset($guestCode) ? $guestCode : ($event->invite_token ?? 'preview')
                ) !!}
            </div>
            @endif
        </div>

        {{-- Guest code --}}
        @if(isset($guestCode))
        <div style="background:{{ $t['codeBg'] }};border:1px solid {{ $t['divider'] }};border-radius:8px;padding:5px 10px;margin-top:10px;text-align:center">
            <span style="font-size:10px;font-family:monospace;letter-spacing:1.5px;color:{{ $t['codeText'] }}">
                {{ $guestCode }}
            </span>
        </div>
        @endif

    </div>

    {{-- Bottom accent bar --}}
    <div style="height:2px;background:{{ $t['accent'] }};opacity:.4"></div>
</div>
