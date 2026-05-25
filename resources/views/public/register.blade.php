<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $event->title }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{font-family:'Outfit',sans-serif}
        @keyframes fadeUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .fade-up{animation:fadeUp .5s ease both}
        .fade-up-2{animation:fadeUp .5s ease .12s both}
        .fade-up-3{animation:fadeUp .5s ease .24s both}

        /* Row layout for registration: card left, form right */
        .reg-row{
            display:flex;
            flex-direction:column;
            gap:20px;
        }
        @media(min-width:720px){
            .reg-row{
                flex-direction:row;
                align-items:flex-start;
            }
            .reg-card-col{
                flex:0 0 360px;
                position:sticky;
                top:24px;
            }
            .reg-form-col{
                flex:1;
            }
        }
    </style>
</head>
<body style="background:#080b14;min-height:100vh;padding:24px 16px 48px">

    {{-- Background --}}
    <div style="position:fixed;inset:0;pointer-events:none;z-index:0">
        <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);width:700px;height:400px;background:radial-gradient(ellipse at 50% 0%,rgba(79,70,229,.18),transparent 70%)"></div>
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.018) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.018) 1px,transparent 1px);background-size:48px 48px"></div>
    </div>

    @php
        $inviteCard  = $event->inviteCard;
        $style       = $inviteCard?->template_style ?? 'classic';
        $showVenue   = $inviteCard?->show_venue   ?? false;
        $showAgenda  = $inviteCard?->show_agenda  ?? false;
        $showQr      = $inviteCard?->show_qr      ?? true;   // was missing
        $customMsg   = $inviteCard?->custom_message ?? '';
        $themes = [
            'classic' => ['card'=>'#2d2c8a','accent'=>'#f59e0b','text'=>'#fbbf24','sub'=>'rgba(199,210,254,.8)','border'=>'rgba(245,158,11,.25)'],
            'elegant' => ['card'=>'#4c0519','accent'=>'#f43f5e','text'=>'#fb7185','sub'=>'rgba(254,205,211,.8)','border'=>'rgba(244,63,94,.25)'],
            'minimal' => ['card'=>'#0d0f14','accent'=>'#94a3b8','text'=>'#e2e8f0','sub'=>'rgba(148,163,184,.75)','border'=>'rgba(255,255,255,.1)'],
        ];
        $t = $themes[$style] ?? $themes['classic'];
    @endphp

    <div style="position:relative;z-index:10;max-width:880px;margin:0 auto">

    @if((isset($registered) && $registered) || (isset($alreadyJoined) && $alreadyJoined))
    {{-- ===== SUCCESS / ALREADY REGISTERED — shared card ===== --}}
    @php
        // Use today's schedule if passed, otherwise fall back to all event schedules
        $displaySchedule = $todaySchedule ?? $event->schedules->sortBy('start_time');
    @endphp
    <div style="max-width:440px;margin:0 auto">

        {{-- Thank you / Welcome back banner — shown ABOVE the card --}}
        @if(isset($alreadyJoined) && $alreadyJoined)
        <div class="fade-up" style="text-align:center;margin-bottom:24px">
            <div style="font-size:40px;margin-bottom:10px">👋</div>
            <h2 style="color:white;font-size:22px;font-weight:800;margin:0 0 6px">Welcome back, {{ $guestName }}!</h2>
            <p style="color:#9ca3af;font-size:14px;margin:0">You're already on the guest list — here's your invite card.</p>
        </div>
        @else
        <div class="fade-up" style="text-align:center;margin-bottom:24px">
            <div style="font-size:40px;margin-bottom:10px">🎉</div>
            <h2 style="color:white;font-size:22px;font-weight:800;margin:0 0 6px">Thank you, {{ $guestName }}!</h2>
            <p style="color:#9ca3af;font-size:14px;margin:0">You're registered! Here's your personal invite card.</p>
        </div>
        @endif

        {{-- Themed invite card with name + QR --}}
        <div class="fade-up" style="border-radius:20px;overflow:hidden;margin-bottom:20px;box-shadow:0 16px 48px rgba(0,0,0,.5);border:1px solid {{ $t['border'] }}">
            <div style="height:4px;background:{{ $t['accent'] }}"></div>
            <div style="background:{{ $t['card'] }};padding:24px;position:relative;overflow:hidden">

                {{-- Decorative background elements --}}
                <div style="position:absolute;top:0;left:0;right:0;height:100px;background:linear-gradient(180deg,{{ $t['accent'] }}22,transparent);pointer-events:none"></div>
                <div style="position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:{{ $t['accent'] }};opacity:.07;pointer-events:none"></div>
                <div style="position:absolute;bottom:-30px;left:-30px;width:100px;height:100px;border-radius:50%;background:{{ $t['accent'] }};opacity:.05;pointer-events:none"></div>

                {{-- Category pill --}}
                <div style="display:inline-flex;align-items:center;gap:6px;background:{{ $t['accent'] }}22;border:1px solid {{ $t['accent'] }}44;border-radius:20px;padding:3px 10px 3px 8px;margin-bottom:14px">
                    <span style="width:6px;height:6px;border-radius:50%;background:{{ $t['accent'] }};display:inline-block;flex-shrink:0"></span>
                    <span style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['text'] }}">{{ $event->category?->name ?? 'Event' }}</span>
                </div>

                <h1 style="color:white;font-size:22px;font-weight:800;margin:0 0 4px;line-height:1.2;position:relative">{{ $event->title }}</h1>

                @if($customMsg)
                <p style="color:{{ $t['sub'] }};font-size:13px;font-style:italic;margin:8px 0 0">"{{ $customMsg }}"</p>
                @endif

                <div style="height:1px;background:{{ $t['border'] }};margin:16px 0"></div>

                {{-- Date --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <svg width="14" height="14" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span style="color:white;font-size:13px;font-weight:500">
                        {{ \Carbon\Carbon::parse($event->start_date)->format('D, M d Y') }}
                        @if($event->start_date != $event->end_date) – {{ \Carbon\Carbon::parse($event->end_date)->format('M d Y') }} @endif
                    </span>
                </div>

                @if($event->start_time)
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <svg width="14" height="14" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="color:white;font-size:13px">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                    @if($event->end_time) – {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }} @endif</span>
                </div>
                @endif

                {{-- Venue — only if toggle ON and venue is set --}}
                @if($showVenue && $event->venue)
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                    <svg width="14" height="14" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span style="color:white;font-size:13px">{{ $event->venue }}</span>
                </div>
                @endif

                {{-- Schedule/Agenda — only if toggle ON --}}
                @if($showAgenda && $displaySchedule->count() > 0)
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid {{ $t['border'] }}">
                    <p style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['text'] }};margin:0 0 8px">Schedule</p>
                    @foreach($displaySchedule->take(3) as $session)
                    <div style="display:flex;gap:10px;margin-bottom:6px">
                        <span style="font-size:11px;font-family:monospace;color:{{ $t['sub'] }};width:50px;flex-shrink:0">
                            {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                        </span>
                        <span style="font-size:12px;color:white">{{ $session->session_name }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                <div style="height:1px;background:{{ $t['border'] }};margin:16px 0"></div>

                {{-- Guest name + QR (QR shown only if toggle ON) --}}
                <div style="display:flex;align-items:flex-end;justify-content:space-between">
                    <div>
                        <p style="font-size:10px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['text'] }};margin:0 0 4px">Invited Guest</p>
                        <p style="color:white;font-size:20px;font-weight:800;margin:0">{{ $guestName }}</p>
                    </div>
                    @if($showQr)
                    <div style="background:white;padding:8px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.3)">
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate($guestCode) !!}
                    </div>
                    @endif
                </div>

                <div style="margin-top:12px;text-align:center">
                    <span style="display:inline-block;background:{{ $t['accent'] }}18;border:1px solid {{ $t['accent'] }}33;border-radius:20px;padding:5px 16px;font-size:12px;font-family:monospace;color:{{ $t['sub'] }};letter-spacing:1.5px">
                        {{ $guestCode }}
                    </span>
                </div>
            </div>
            <div style="height:3px;background:{{ $t['accent'] }};opacity:.4"></div>
        </div>

        {{-- Screenshot hint --}}
        <p class="fade-up-2" style="text-align:center;color:#4b5563;font-size:12px;margin:14px 0 0">
            📸 Screenshot this card — show it at the door for check-in
        </p>

    </div>

    @elseif(isset($isFull) && $isFull)
    {{-- ===== FULL ===== --}}
    <div class="fade-up" style="max-width:440px;margin:0 auto;background:#0d1117;border:1px solid rgba(239,68,68,.2);border-radius:20px;overflow:hidden">
        <div style="padding:28px 24px;text-align:center;background:linear-gradient(135deg,#4f46e5,#7c3aed)">
            <h1 style="color:white;font-size:20px;font-weight:700;margin:0">{{ $event->title }}</h1>
        </div>
        <div style="padding:28px 24px;text-align:center">
            <div style="width:52px;height:52px;border-radius:50%;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg width="26" height="26" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p style="color:white;font-weight:600;font-size:16px;margin:0 0 6px">Registration Full</p>
            <p style="color:#6b7280;font-size:13px;margin:0">This event has reached its maximum capacity.</p>
        </div>
    </div>

    @else
    {{-- ===== REGISTRATION FORM — ROW LAYOUT ===== --}}
    <div class="reg-row fade-up">

        {{-- LEFT: Invite card preview --}}
        <div class="reg-card-col">
            <div style="border-radius:20px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.4);border:1px solid {{ $t['border'] }}">
                <div style="height:3px;background:{{ $t['accent'] }}"></div>
                <div style="background:{{ $t['card'] }};padding:24px;position:relative;overflow:hidden">

                    {{-- Decorative background elements --}}
                    <div style="position:absolute;top:0;left:0;right:0;height:90px;background:linear-gradient(180deg,{{ $t['accent'] }}22,transparent);pointer-events:none"></div>
                    <div style="position:absolute;top:-35px;right:-35px;width:120px;height:120px;border-radius:50%;background:{{ $t['accent'] }};opacity:.07;pointer-events:none"></div>
                    <div style="position:absolute;bottom:-25px;left:-25px;width:90px;height:90px;border-radius:50%;background:{{ $t['accent'] }};opacity:.05;pointer-events:none"></div>

                    {{-- Category pill --}}
                    @if($event->category)
                    <div style="display:inline-flex;align-items:center;gap:6px;background:{{ $t['accent'] }}22;border:1px solid {{ $t['accent'] }}44;border-radius:20px;padding:3px 10px 3px 8px;margin-bottom:12px">
                        <span style="width:6px;height:6px;border-radius:50%;background:{{ $t['accent'] }};display:inline-block;flex-shrink:0"></span>
                        <span style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['text'] }}">{{ $event->category->name }}</span>
                    </div>
                    @endif

                    <h1 style="color:white;font-size:20px;font-weight:800;margin:0 0 4px;line-height:1.2;position:relative">{{ $event->title }}</h1>

                    @if($customMsg)
                    <p style="color:{{ $t['sub'] }};font-size:12px;font-style:italic;margin:8px 0 0">"{{ $customMsg }}"</p>
                    @endif

                    <div style="height:1px;background:{{ $t['border'] }};margin:16px 0"></div>

                    {{-- Date --}}
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                        <svg width="13" height="13" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span style="color:white;font-size:12px;font-weight:500">{{ \Carbon\Carbon::parse($event->start_date)->format('D, M d Y') }}</span>
                    </div>

                    @if($event->start_time)
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                        <svg width="13" height="13" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span style="color:white;font-size:12px">{{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                        @if($event->end_time) – {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }} @endif</span>
                    </div>
                    @endif

                    {{-- Venue — only if toggle ON and venue is set --}}
                    @if($showVenue && $event->venue)
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
                        <svg width="13" height="13" fill="none" stroke="{{ $t['text'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span style="color:white;font-size:12px">{{ $event->venue }}</span>
                    </div>
                    @endif

                    {{-- Schedule/Agenda — only if toggle ON --}}
                    @if($showAgenda)
                    @php $allSchedule = $event->schedules->sortBy('start_time')->take(4); @endphp
                    @if($allSchedule->count() > 0)
                    <div style="margin-top:12px;padding-top:12px;border-top:1px solid {{ $t['border'] }}">
                        <p style="font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:{{ $t['text'] }};margin:0 0 8px">Agenda</p>
                        @foreach($allSchedule as $session)
                        <div style="display:flex;gap:10px;margin-bottom:6px">
                            <span style="font-size:11px;font-family:monospace;color:{{ $t['sub'] }};width:50px;flex-shrink:0">
                                {{ \Carbon\Carbon::parse($session->start_time)->format('g:i A') }}
                            </span>
                            <span style="font-size:12px;color:white">{{ $session->session_name }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @endif

                    <div style="height:1px;background:{{ $t['border'] }};margin:16px 0 12px"></div>

                    {{-- QR placeholder (shown only if toggle ON) --}}
                    @if($showQr)
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="background:rgba(255,255,255,.08);border:1px dashed rgba(255,255,255,.2);border-radius:10px;width:52px;height:52px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg width="22" height="22" fill="none" stroke="{{ $t['sub'] }}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12v.01M12 4H4v8h8V4zM4 16h4v4H4v-4z"/></svg>
                        </div>
                        <p style="color:{{ $t['sub'] }};font-size:11px;line-height:1.5;margin:0">
                            Your personal QR code will appear here after registration
                        </p>
                    </div>
                    @else
                    <p style="color:{{ $t['sub'] }};font-size:10px;text-transform:uppercase;letter-spacing:1.5px;margin:0">
                        Register below to receive your personal invite card
                    </p>
                    @endif
                </div>
                <div style="height:2px;background:{{ $t['accent'] }};opacity:.4"></div>
            </div>
        </div>

        {{-- RIGHT: Registration form --}}
        <div class="reg-form-col fade-up-2">
            <div style="background:#0d1117;border:1px solid rgba(255,255,255,.07);border-radius:20px;padding:28px">
                <h2 style="color:white;font-size:18px;font-weight:700;margin:0 0 6px">Guest Registration</h2>
                <p style="color:#6b7280;font-size:13px;margin:0 0 24px">Fill in your details to receive a personalised invite card.</p>

                @if($errors->any())
                <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:12px;padding:12px 16px;margin-bottom:16px">
                    @foreach($errors->all() as $error)
                    <p style="color:#f87171;font-size:13px;margin:0">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('public.register.store', $event->invite_token) }}"
                      style="display:flex;flex-direction:column;gap:16px">
                    @csrf

                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;font-weight:500;margin-bottom:6px">
                            Full Name <span style="color:#f87171">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Sokha Chan"
                               style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 14px;color:white;font-size:14px;outline:none;box-sizing:border-box;font-family:Outfit,sans-serif"
                               onfocus="this.style.borderColor='rgba(99,102,241,.5)'"
                               onblur="this.style.borderColor='rgba(255,255,255,.1)'">
                    </div>

                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;font-weight:500;margin-bottom:6px">
                            Email <span style="color:#6b7280;font-weight:400">(optional)</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com"
                               style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 14px;color:white;font-size:14px;outline:none;box-sizing:border-box;font-family:Outfit,sans-serif"
                               onfocus="this.style.borderColor='rgba(99,102,241,.5)'"
                               onblur="this.style.borderColor='rgba(255,255,255,.1)'">
                    </div>

                    <div>
                        <label style="display:block;color:#9ca3af;font-size:13px;font-weight:500;margin-bottom:6px">
                            Phone <span style="color:#6b7280;font-weight:400">(optional)</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 012 345 678"
                               style="width:100%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px 14px;color:white;font-size:14px;outline:none;box-sizing:border-box;font-family:Outfit,sans-serif"
                               onfocus="this.style.borderColor='rgba(99,102,241,.5)'"
                               onblur="this.style.borderColor='rgba(255,255,255,.1)'">
                    </div>

                    {{-- Event info recap --}}
                    <div style="background:rgba(79,70,229,.06);border:1px solid rgba(99,102,241,.15);border-radius:12px;padding:14px 16px">
                        <p style="color:#818cf8;font-size:12px;font-weight:600;margin:0 0 6px;text-transform:uppercase;letter-spacing:1px">You're registering for</p>
                        <p style="color:white;font-size:14px;font-weight:700;margin:0 0 4px">{{ $event->title }}</p>
                        <p style="color:#6b7280;font-size:12px;margin:0">{{ \Carbon\Carbon::parse($event->start_date)->format('D, M d Y') }}</p>
                    </div>

                    <button type="submit"
                            style="width:100%;padding:14px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;font-size:15px;font-weight:600;border:none;border-radius:14px;cursor:pointer;font-family:Outfit,sans-serif;box-shadow:0 4px 14px rgba(79,70,229,.4);margin-top:4px;transition:all .2s"
                            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 20px rgba(79,70,229,.5)'"
                            onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(79,70,229,.4)'">
                        Register for Event
                    </button>
                </form>
            </div>
        </div>

    </div>
    @endif

    <p style="text-align:center;color:#374151;font-size:12px;margin-top:24px">
        Powered by {{ config('app.name') }}
    </p>

    </div>
</body>
</html>
