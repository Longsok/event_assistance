Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force

$root = Get-Location

function FixFile($path, $changes) {
    if (-not (Test-Path $path)) { Write-Host "NOT FOUND: $path" -ForegroundColor Red; return }
    $c = [System.IO.File]::ReadAllText($path)
    $orig = $c
    foreach ($ch in $changes) {
        $c = [System.Text.RegularExpressions.Regex]::Replace($c, $ch[0], $ch[1], [System.Text.RegularExpressions.RegexOptions]::Singleline)
    }
    if ($c -ne $orig) {
        [System.IO.File]::WriteAllText($path, $c, [System.Text.Encoding]::UTF8)
        Write-Host "FIXED: $path" -ForegroundColor Green
    } else {
        Write-Host "NO CHANGE: $path" -ForegroundColor Gray
    }
}

# ── 1. app.blade.php ─────────────────────────────────────────
# Remove: sidebar logo icon div (calendar icon box)
# Remove: "Create New Event" sidebar button (duplicate of header button)
FixFile "resources\views\layouts\app.blade.php" @(

    # Remove logo icon div in sidebar brand area
    @('(?s)<div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"\s*style="background:linear-gradient[^"]*">.*?</div>', ''),

    # Remove "Create New Event" sidebar nav link (the indigo-50 bordered one)
    @('(?s)<div class="pt-4 pb-1"><div class="h-px bg-slate-100"></div></div>\s*<a href="{{ route\(''events\.create''\) }}"[^>]*class="flex items-center gap-3[^"]*text-indigo-600[^"]*">.*?</a>', ''),

    # Also remove header "+ New Event" button duplicate avatar icon (keep button, remove standalone avatar)
    @('(?s)<div class="w-8 h-8 rounded-xl text-xs font-bold text-white flex items-center justify-center"\s*style="background:linear-gradient[^"]*">\s*{{ strtoupper\(substr\(Auth::user\(\)->name,0,1\)\) }}\s*</div>\s*</header>', '</header>')
)

# ── 2. admin.blade.php ───────────────────────────────────────
# Remove sidebar logo icon div
FixFile "resources\views\layouts\admin.blade.php" @(
    @('(?s)<div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">.*?</div>', '')
)

# ── 3. guest.blade.php ───────────────────────────────────────
# Remove left panel logo icon and mobile logo icon
FixFile "resources\views\layouts\guest.blade.php" @(
    @('(?s)<div class="w-10 h-10 rounded-2xl flex items-center justify-center border border-white/20"[^>]*>.*?</div>', ''),
    @('(?s)<div class="w-8 h-8 rounded-xl flex items-center justify-center"\s*style="background:linear-gradient[^"]*">.*?</div>', '')
)

# ── 4. navigation.blade.php ──────────────────────────────────
FixFile "resources\views\layouts\navigation.blade.php" @(
    @('(?s)<div class="w-7 h-7 rounded-lg flex items-center justify-center"\s*style="background:linear-gradient[^"]*">.*?</div>', '')
)

# ── 5. welcome.blade.php ─────────────────────────────────────
FixFile "resources\views\welcome.blade.php" @(
    @('(?s)<div class="w-8 h-8 rounded-xl flex items-center justify-center btn-primary">.*?</div>', '')
)

Write-Host ""
Write-Host "Done. Now run: php artisan view:clear" -ForegroundColor Cyan
