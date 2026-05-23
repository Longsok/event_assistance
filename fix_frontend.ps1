# ==============================================================
#  fix_frontend.ps1
#  Event Assistance - Frontend Theme Fixer
#  Run from project root: .\fix_frontend.ps1
#  - Removes all emojis from every blade file
#  - Standardises buttons, badges, alerts to indigo/slate theme
#  - Reports every file changed and what was fixed
# ==============================================================

$ProjectRoot = Get-Location
$ViewsPath   = Join-Path $ProjectRoot "resources\views"

# ── Theme colour palette ──────────────────────────────────────
# Primary   : indigo-600  (#4f46e5)
# Primary Hover : indigo-500
# Sidebar bg (organizer) : white  border-slate-200
# Sidebar bg (admin)     : gray-900 border-gray-800
# Page bg (organizer)    : slate-50
# Page bg (admin)        : gray-950
# Text primary : slate-900
# Text muted   : slate-500
# Success  : emerald-600
# Warning  : amber-500
# Danger   : red-600
# Info     : sky-600
# -------------------------------------------------------------

$Stats = @{ FilesScanned = 0; FilesChanged = 0; EmojiRemoved = 0; PatternsFixed = 0 }
$ChangeLog = @()

# ── Emoji Unicode regex ───────────────────────────────────────
# Covers: emoticons, misc symbols, supplemental symbols,
#         transport/map, dingbats, enclosed alphanumerics,
#         flags, skin-tone modifiers, ZWJ sequences
$EmojiPattern = '[' +
    '\u00A9\u00AE' +
    '\u203C\u2049' +
    '\u20E3' +
    '\u2122\u2139' +
    '\u2194-\u2199' +
    '\u21A9-\u21AA' +
    '\u231A-\u231B' +
    '\u2328' +
    '\u23CF' +
    '\u23E9-\u23F3' +
    '\u23F8-\u23FA' +
    '\u24C2' +
    '\u25AA-\u25AB' +
    '\u25B6' +
    '\u25C0' +
    '\u25FB-\u25FE' +
    '\u2600-\u2604' +
    '\u260E' +
    '\u2611' +
    '\u2614-\u2615' +
    '\u2618' +
    '\u261D' +
    '\u2620' +
    '\u2622-\u2623' +
    '\u2626' +
    '\u262A' +
    '\u262E-\u262F' +
    '\u2638-\u263A' +
    '\u2640' +
    '\u2642' +
    '\u2648-\u2653' +
    '\u265F-\u2660' +
    '\u2663' +
    '\u2665-\u2666' +
    '\u2668' +
    '\u267B' +
    '\u267E-\u267F' +
    '\u2692-\u2697' +
    '\u2699' +
    '\u269B-\u269C' +
    '\u26A0-\u26A1' +
    '\u26AA-\u26AB' +
    '\u26B0-\u26B1' +
    '\u26BD-\u26BE' +
    '\u26C4-\u26C5' +
    '\u26CE-\u26CF' +
    '\u26D1' +
    '\u26D3-\u26D4' +
    '\u26E9-\u26EA' +
    '\u26F0-\u26F5' +
    '\u26F7-\u26FA' +
    '\u26FD' +
    '\u2702' +
    '\u2705' +
    '\u2708-\u270D' +
    '\u270F' +
    '\u2712' +
    '\u2714' +
    '\u2716' +
    '\u271D' +
    '\u2721' +
    '\u2728' +
    '\u2733-\u2734' +
    '\u2744' +
    '\u2747' +
    '\u274C' +
    '\u274E' +
    '\u2753-\u2755' +
    '\u2757' +
    '\u2763-\u2764' +
    '\u2795-\u2797' +
    '\u27A1' +
    '\u27B0' +
    '\u27BF' +
    '\u2934-\u2935' +
    '\u2B05-\u2B07' +
    '\u2B1B-\u2B1C' +
    '\u2B50' +
    '\u2B55' +
    '\u3030' +
    '\u303D' +
    '\u3297' +
    '\u3299' +
    '\uD83C-\uD83E' +
    ']'

# Surrogate pairs for emoji (covers most multi-byte emoji like faces, objects)
$SurrogatePairPattern = '[\uD800-\uDBFF][\uDC00-\uDFFF]'

# ── Replacement rules: [pattern, replacement, description] ──
$Replacements = @(

    # ---- BUTTONS ------------------------------------------------
    # Old: btn-primary (Bootstrap style)
    @(
        'class="[^"]*btn-primary[^"]*"',
        'class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"',
        'btn-primary -> indigo button'
    ),
    # Old: btn-secondary
    @(
        'class="[^"]*btn-secondary[^"]*"',
        'class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition focus:outline-none focus:ring-2 focus:ring-indigo-500"',
        'btn-secondary -> slate outline button'
    ),
    # Old: btn-danger
    @(
        'class="[^"]*btn-danger[^"]*"',
        'class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-500 transition focus:outline-none focus:ring-2 focus:ring-red-500"',
        'btn-danger -> red button'
    ),
    # Old green submit buttons
    @(
        'class="[^"]*bg-green-600[^"]*hover:bg-green-[^"]*"',
        'class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-500 transition"',
        'green submit -> indigo button'
    ),

    # ---- BADGES / STATUS PILLS ----------------------------------
    @('class="[^"]*badge badge-success[^"]*"', 'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"', 'badge-success -> emerald pill'),
    @('class="[^"]*badge badge-warning[^"]*"', 'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800"',   'badge-warning -> amber pill'),
    @('class="[^"]*badge badge-danger[^"]*"',  'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"',       'badge-danger -> red pill'),
    @('class="[^"]*badge badge-info[^"]*"',    'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800"',        'badge-info -> sky pill'),
    @('class="[^"]*badge badge-primary[^"]*"', 'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"', 'badge-primary -> indigo pill'),

    # ---- ALERTS -------------------------------------------------
    @('class="[^"]*alert alert-success[^"]*"', 'class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-xl"', 'alert-success -> emerald alert'),
    @('class="[^"]*alert alert-danger[^"]*"',  'class="flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl"',           'alert-danger -> red alert'),
    @('class="[^"]*alert alert-warning[^"]*"', 'class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm px-4 py-3 rounded-xl"',     'alert-warning -> amber alert'),
    @('class="[^"]*alert alert-info[^"]*"',    'class="flex items-center gap-2 bg-sky-50 border border-sky-200 text-sky-700 text-sm px-4 py-3 rounded-xl"',           'alert-info -> sky alert'),

    # ---- CARDS --------------------------------------------------
    @('class="[^"]*card[^"]*"',    'class="bg-white rounded-2xl border border-slate-200 shadow-sm"', 'card -> rounded white card'),
    @('class="[^"]*card-body[^"]*"', 'class="p-6"', 'card-body -> p-6'),
    @('class="[^"]*card-header[^"]*"', 'class="px-6 py-4 border-b border-slate-100"', 'card-header -> border-b header'),

    # ---- FORM INPUTS --------------------------------------------
    @(
        'class="[^"]*form-control[^"]*"',
        'class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"',
        'form-control -> themed input'
    ),
    @(
        'class="[^"]*form-select[^"]*"',
        'class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"',
        'form-select -> themed select'
    ),
    @(
        'class="[^"]*form-label[^"]*"',
        'class="block text-sm font-medium text-slate-700 mb-1.5"',
        'form-label -> themed label'
    ),

    # ---- PAGE HEADINGS ------------------------------------------
    @(
        'class="[^"]*page-title[^"]*"',
        'class="text-xl font-semibold text-slate-900"',
        'page-title -> text-xl slate heading'
    ),
    @(
        'class="[^"]*section-title[^"]*"',
        'class="text-base font-semibold text-slate-800 mb-4"',
        'section-title -> text-base heading'
    ),

    # ---- TABLES -------------------------------------------------
    @('class="[^"]*table[^"]*"',    'class="w-full text-sm text-left"', 'table -> base table'),
    @('class="[^"]*thead[^"]*"',    'class="bg-slate-50 text-xs text-slate-500 uppercase tracking-wide"', 'thead -> slate header'),
    @('class="[^"]*table-row[^"]*"', 'class="border-b border-slate-100 hover:bg-slate-50 transition"', 'table-row -> hover row'),

    # ---- MISC ---------------------------------------------------
    # Remove inline style="color:..." that conflict with Tailwind
    @('style="color:\s*#?[a-fA-F0-9]+;?\s*"', '', 'remove inline color styles'),
    # Remove Bootstrap container
    @('class="[^"]*container-fluid[^"]*"', 'class="w-full px-4 lg:px-6"', 'container-fluid -> full width'),
    @('class="[^"]*container[^"]*"',       'class="max-w-7xl mx-auto px-4 lg:px-6"', 'container -> max-w-7xl')
)

# ── Helper: strip emojis from a string ───────────────────────
function Remove-Emojis {
    param([string]$Text)
    # Remove surrogate pairs (multi-byte emoji)
    $clean = [System.Text.RegularExpressions.Regex]::Replace($Text, $SurrogatePairPattern, '')
    # Remove single-codepoint emoji
    $clean = [System.Text.RegularExpressions.Regex]::Replace($clean, $EmojiPattern, '')
    return $clean
}

# ── Main loop ─────────────────────────────────────────────────
Write-Host ""
Write-Host "  Event Assistance — Frontend Theme Fixer" -ForegroundColor Cyan
Write-Host "  =========================================" -ForegroundColor Cyan
Write-Host "  Scanning: $ViewsPath" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path $ViewsPath)) {
    Write-Host "  ERROR: views folder not found at $ViewsPath" -ForegroundColor Red
    Write-Host "  Run this script from the project root (C:\xampp\htdocs\event_assistance-main)" -ForegroundColor Yellow
    exit 1
}

$BladeFiles = Get-ChildItem -Path $ViewsPath -Recurse -Filter "*.blade.php"

foreach ($File in $BladeFiles) {
    $Stats.FilesScanned++
    $Original = Get-Content $File.FullName -Raw -Encoding UTF8
    $Modified = $Original
    $FileChanges = @()

    # -- Strip emojis --
    $NoEmoji = Remove-Emojis -Text $Modified
    if ($NoEmoji -ne $Modified) {
        $Count = ([System.Text.RegularExpressions.Regex]::Matches($Modified, $SurrogatePairPattern)).Count +
                 ([System.Text.RegularExpressions.Regex]::Matches($Modified, $EmojiPattern)).Count
        $Stats.EmojiRemoved += $Count
        $FileChanges += "Removed $Count emoji character(s)"
        $Modified = $NoEmoji
    }

    # -- Apply pattern replacements --
    foreach ($Rule in $Replacements) {
        $Pattern     = $Rule[0]
        $Replacement = $Rule[1]
        $Description = $Rule[2]

        $Matches = [System.Text.RegularExpressions.Regex]::Matches($Modified, $Pattern, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
        if ($Matches.Count -gt 0) {
            $Modified = [System.Text.RegularExpressions.Regex]::Replace($Modified, $Pattern, $Replacement, [System.Text.RegularExpressions.RegexOptions]::IgnoreCase)
            $Stats.PatternsFixed += $Matches.Count
            $FileChanges += "$($Matches.Count)x $Description"
        }
    }

    # -- Save if changed --
    if ($Modified -ne $Original) {
        Set-Content -Path $File.FullName -Value $Modified -Encoding UTF8 -NoNewline
        $Stats.FilesChanged++
        $RelPath = $File.FullName.Replace($ProjectRoot.ToString(), '').TrimStart('\')
        $ChangeLog += [PSCustomObject]@{ File = $RelPath; Changes = ($FileChanges -join ' | ') }

        Write-Host "  [FIXED]  $($File.Name)" -ForegroundColor Green
        foreach ($Change in $FileChanges) {
            Write-Host "           - $Change" -ForegroundColor Gray
        }
    }
}

# ── Summary ───────────────────────────────────────────────────
Write-Host ""
Write-Host "  =========================================" -ForegroundColor Cyan
Write-Host "  Done." -ForegroundColor Cyan
Write-Host "  Files scanned : $($Stats.FilesScanned)" -ForegroundColor White
Write-Host "  Files changed : $($Stats.FilesChanged)" -ForegroundColor $(if ($Stats.FilesChanged -gt 0) { 'Green' } else { 'Gray' })
Write-Host "  Emojis removed: $($Stats.EmojiRemoved)" -ForegroundColor $(if ($Stats.EmojiRemoved -gt 0) { 'Yellow' } else { 'Gray' })
Write-Host "  Patterns fixed: $($Stats.PatternsFixed)" -ForegroundColor $(if ($Stats.PatternsFixed -gt 0) { 'Green' } else { 'Gray' })
Write-Host ""

# ── Write change log ─────────────────────────────────────────
if ($ChangeLog.Count -gt 0) {
    $LogPath = Join-Path $ProjectRoot "frontend_fix_log.txt"
    $LogLines = @("Event Assistance - Frontend Fix Log", "Run: $(Get-Date)", "=" * 60)
    foreach ($Entry in $ChangeLog) {
        $LogLines += ""
        $LogLines += "FILE: $($Entry.File)"
        $LogLines += "  $($Entry.Changes)"
    }
    $LogLines | Set-Content -Path $LogPath -Encoding UTF8
    Write-Host "  Change log saved to: frontend_fix_log.txt" -ForegroundColor Gray
}

Write-Host "  Run [php artisan view:clear] to flush compiled views." -ForegroundColor Yellow
Write-Host ""
