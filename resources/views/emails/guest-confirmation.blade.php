<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You're registered for {{ $event->title }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 24px; }
        .wrapper { max-width: 560px; margin: 0 auto; }
        .card { background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header { background: #4f46e5; padding: 32px 32px 24px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 22px; margin: 0 0 6px; }
        .header p { color: #a5b4fc; font-size: 14px; margin: 0; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; color: #111827; margin-bottom: 16px; }
        .code-box { background: #f5f3ff; border: 2px dashed #a5b4fc; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0; }
        .code-label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
        .code { font-size: 28px; font-weight: 800; color: #4f46e5; font-family: monospace; letter-spacing: 3px; }
        .code-note { font-size: 12px; color: #9ca3af; margin-top: 8px; }
        .details { background: #f9fafb; border-radius: 10px; padding: 16px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; }
        .detail-value { color: #111827; font-weight: 500; }
        .schedule-title { font-size: 14px; font-weight: 600; color: #374151; margin: 20px 0 10px; }
        .session { display: flex; gap: 12px; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
        .session:last-child { border-bottom: none; }
        .session-time { color: #6b7280; font-family: monospace; min-width: 80px; }
        .session-name { color: #374151; }
        .footer { padding: 20px 32px; background: #f9fafb; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        .btn { display: inline-block; background: #4f46e5; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; margin: 16px 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">

            {{-- Header --}}
            <div class="header">
                <h1>You're registered! 🎉</h1>
                <p>{{ $event->title }}</p>
            </div>

            {{-- Body --}}
            <div class="body">
                <p class="greeting">Hi <strong>{{ $guest->name }}</strong>,</p>
                <p style="color: #4b5563; font-size: 14px; line-height: 1.6;">
                    You have successfully registered for <strong>{{ $event->title }}</strong>.
                    Keep your guest code safe — you'll need it to check in on the event day.
                </p>

                {{-- Guest Code --}}
                <div class="code-box">
                    <p class="code-label">Your Guest Code</p>
                    <p class="code">{{ $guestCode }}</p>
                    <p class="code-note">Show this code at the entrance to check in</p>
                </div>

                {{-- Event Details --}}
                <div class="details">
                    <div class="detail-row">
                        <span class="detail-label">📅 Date</span>
                        <span class="detail-value">
                            {{ $event->start_date->format('M d, Y') }}
                            @if($event->start_date->ne($event->end_date))
                                – {{ $event->end_date->format('M d, Y') }}
                            @endif
                        </span>
                    </div>
                    @if($event->start_time)
                    <div class="detail-row">
                        <span class="detail-label">⏰ Time</span>
                        <span class="detail-value">{{ $event->start_time }}</span>
                    </div>
                    @endif
                    @if($event->venue)
                    <div class="detail-row">
                        <span class="detail-label">📍 Venue</span>
                        <span class="detail-value">{{ $event->venue }}</span>
                    </div>
                    @endif
                    @if($event->address)
                    <div class="detail-row">
                        <span class="detail-label">🗺️ Address</span>
                        <span class="detail-value">{{ $event->address }}</span>
                    </div>
                    @endif
                    @if($event->meal_provided)
                    <div class="detail-row">
                        <span class="detail-label">🍽️ Meal</span>
                        <span class="detail-value">Provided</span>
                    </div>
                    @endif
                </div>

                {{-- Schedule --}}
                @if($schedule->count())
                <p class="schedule-title">📋 Event Schedule</p>
                @foreach($schedule->take(8) as $session)
                <div class="session">
                    <span class="session-time">{{ $session->start_time }}</span>
                    <span class="session-name {{ $session->is_break ? 'italic' : '' }}" style="{{ $session->is_break ? 'color:#9ca3af' : '' }}">
                        {{ $session->session_name }}
                        @if($session->is_break) (Break) @endif
                    </span>
                </div>
                @endforeach
                @if($schedule->count() > 8)
                <p style="font-size:12px;color:#9ca3af;margin-top:8px;">+ {{ $schedule->count() - 8 }} more sessions</p>
                @endif
                @endif

                <p style="font-size:13px;color:#6b7280;margin-top:24px;line-height:1.6;">
                    If you have any questions, please contact the event organizer directly.
                </p>
            </div>

            {{-- Footer --}}
            <div class="footer">
                <p>This email was sent by <strong>{{ config('app.name') }}</strong></p>
                <p style="margin-top:4px;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>

        </div>
    </div>
</body>
</html>
