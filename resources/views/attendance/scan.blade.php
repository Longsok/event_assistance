<x-app-layout>
<div class="py-6 px-4 sm:px-6 max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('events.attendance.index', $event) }}" class="text-sm hover:underline" style="color:#818cf8">← Back to Attendance</a>
        <h2 class="text-2xl font-bold text-white mt-1">Scan Guest QR</h2>
        <p class="text-sm mt-0.5" style="color:#6b7280">{{ $event->title }} — Point camera at guest's invite QR code</p>
    </div>

    {{-- Stats --}}
    <div class="rounded-2xl border p-4 mb-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)" id="stats-bar">
        <div class="flex items-center justify-between">
            <div class="text-center">
                <p class="text-2xl font-bold text-indigo-400" id="checked-count">—</p>
                <p class="text-xs mt-0.5" style="color:#6b7280">Checked In</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-white" id="total-count">—</p>
                <p class="text-xs mt-0.5" style="color:#6b7280">Total Guests</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-bold text-amber-400" id="remaining-count">—</p>
                <p class="text-xs mt-0.5" style="color:#6b7280">Remaining</p>
            </div>
        </div>
    </div>

    {{-- Camera scanner --}}
    <div class="rounded-2xl border overflow-hidden mb-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="p-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,.07)">
            <p class="font-semibold text-white">Camera Scanner</p>
            <button id="start-btn" onclick="startScanner()"
                    class="px-4 py-2 text-white text-sm font-medium rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                Start Camera
            </button>
        </div>
        <div id="reader" class="w-full"></div>
        <div id="camera-placeholder" class="flex items-center justify-center" style="min-height:280px">
            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl mx-auto mb-3 flex items-center justify-center" style="background:rgba(99,102,241,.1)">
                    <svg class="w-8 h-8" style="color:#818cf8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <p class="text-sm" style="color:#6b7280">Click "Start Camera" to begin scanning</p>
            </div>
        </div>
    </div>

    {{-- Manual entry --}}
    <div class="rounded-2xl border p-5 mb-5" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <h3 class="font-semibold text-white mb-4">Manual Entry</h3>
        <div class="flex gap-3">
            <input type="text" id="manual-code" placeholder="Enter guest code..."
                   class="flex-1 rounded-xl px-3 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 uppercase"
                   style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)"
                   onkeydown="if(event.key==='Enter') processManual()">
            <button onclick="processManual()"
                    class="px-5 py-2.5 text-white text-sm font-medium rounded-xl"
                    style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">Check In</button>
        </div>
    </div>

    {{-- Result --}}
    <div id="result-box" class="hidden rounded-2xl p-5 mb-5 border">
        <div class="flex items-center gap-4">
            <div id="result-icon" class="w-14 h-14 rounded-full flex items-center justify-center text-2xl flex-shrink-0"></div>
            <div>
                <p id="result-name" class="text-xl font-bold text-white"></p>
                <p id="result-message" class="text-sm mt-0.5"></p>
            </div>
        </div>
    </div>

    {{-- Recent check-ins --}}
    <div class="rounded-2xl border overflow-hidden" style="background:#0d1117;border-color:rgba(255,255,255,.07)">
        <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.07)">
            <p class="font-semibold text-white">Recent Check-ins</p>
        </div>
        <div id="recent-checkins">
            <div class="px-5 py-8 text-center text-sm" style="color:#6b7280">No check-ins yet</div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;

function startScanner() {
    document.getElementById('camera-placeholder').style.display = 'none';
    document.getElementById('start-btn').disabled = true;
    document.getElementById('start-btn').textContent = 'Scanning...';
    html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start({ facingMode:"environment" }, { fps:10, qrbox:{width:250,height:250} }, onScanSuccess, ()=>{}).catch(err => {
        document.getElementById('camera-placeholder').style.display = 'flex';
        document.getElementById('start-btn').disabled = false;
        document.getElementById('start-btn').textContent = 'Start Camera';
        showResult('error','✗','Camera Error', err.message || 'Could not access camera');
    });
}
function onScanSuccess(code) { processCode(code); }
function processManual() {
    const code = document.getElementById('manual-code').value.trim();
    if (!code) return;
    processCode(code);
    document.getElementById('manual-code').value = '';
}
function processCode(code) {
    fetch('{{ route('events.attendance.scan.process', $event) }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({guest_code: code})
    }).then(r=>r.json()).then(data => {
        if (data.status === 'success') {
            showResult('success','✓', data.guest_name, `Checked in at ${data.checked_in_at}`);
            addRecent(data.guest_name, data.checked_in_at);
        } else if (data.status === 'already_checked_in') {
            showResult('warning','⚠', data.guest_name, `Already checked in at ${data.checked_in_at}`);
        } else {
            showResult('error','✗','Not Found', data.message || 'Guest code not found');
        }
        loadStats();
    }).catch(()=>showResult('error','✗','Error','Connection error'));
}
function showResult(type, icon, name, msg) {
    const box = document.getElementById('result-box');
    const colors = { success:'rgba(16,185,129,.1);border-color:rgba(52,211,153,.3)', warning:'rgba(251,191,36,.08);border-color:rgba(251,191,36,.3)', error:'rgba(239,68,68,.08);border-color:rgba(239,68,68,.3)' };
    const iconBg = { success:'background:#059669', warning:'background:#d97706', error:'background:#dc2626' };
    const msgColor = { success:'color:#34d399', warning:'color:#fbbf24', error:'color:#f87171' };
    box.style.cssText = `background:${colors[type]}`;
    box.classList.remove('hidden');
    document.getElementById('result-icon').style.cssText = iconBg[type];
    document.getElementById('result-icon').textContent = icon;
    document.getElementById('result-name').textContent = name;
    document.getElementById('result-message').style.cssText = msgColor[type];
    document.getElementById('result-message').textContent = msg;
}
function addRecent(name, time) {
    const c = document.getElementById('recent-checkins');
    if (c.querySelector('.text-center')) c.innerHTML = '';
    const d = document.createElement('div');
    d.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:12px 20px;border-bottom:1px solid rgba(255,255,255,.05)';
    d.innerHTML = `<div style="display:flex;align-items:center;gap:10px"><div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#059669,#047857);display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700">${name.charAt(0).toUpperCase()}</div><p style="color:white;font-size:14px">${name}</p></div><span style="font-size:12px;color:#6b7280">${time}</span>`;
    c.insertBefore(d, c.firstChild);
}
function loadStats() {
    fetch('{{ route('events.attendance.scan.stats', $event) }}').then(r=>r.json()).then(data => {
        document.getElementById('checked-count').textContent = data.checked_in;
        document.getElementById('total-count').textContent = data.total;
        document.getElementById('remaining-count').textContent = data.remaining;
    });
}
loadStats();
</script>
</x-app-layout>
