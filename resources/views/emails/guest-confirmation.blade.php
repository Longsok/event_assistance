<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body{font-family:system-ui,sans-serif;background:#f8fafc;margin:0;padding:20px}
        .card{background:#fff;border-radius:16px;max-width:500px;margin:0 auto;overflow:hidden;border:1px solid #e2e8f0}
        .header{background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:32px;text-align:center;color:#fff}
        .body{padding:32px}
        .code{background:#eef2ff;border-radius:12px;padding:16px;text-align:center;font-family:monospace;font-size:24px;font-weight:700;color:#4338ca;letter-spacing:2px;margin:20px 0}
        .footer{padding:20px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;color:#94a3b8;font-size:12px}
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1 style="margin:0;font-size:24px">You are invited!</h1>
            <p style="margin:8px 0 0;opacity:.85">{{ $event->title }}</p>
        </div>
        <div class="body">
            <p style="color:#334155">Hi {{ $guest->name }},</p>
            <p style="color:#64748b">You have been invited to <strong>{{ $event->title }}</strong>.</p>
            @if($event->event_date)
            <p style="color:#64748b">Date: <strong>{{ $event->event_date->format('l, F j, Y') }}</strong></p>
            @endif
            @if($event->venue)
            <p style="color:#64748b">Venue: <strong>{{ $event->venue }}</strong></p>
            @endif
            <p style="color:#64748b">Your guest code for check-in:</p>
            <div class="code">{{ $guest->qr_token }}</div>
            <p style="color:#94a3b8;font-size:13px">Please keep this code — you will need it to check in on the day of the event.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
