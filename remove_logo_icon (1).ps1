Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass -Force

$ViewsPath = Join-Path (Get-Location) "resources\views"
$Changed   = 0

if (-not (Test-Path $ViewsPath)) {
    Write-Host "ERROR: views folder not found. Run from project root." -ForegroundColor Red
    exit 1
}

$Patterns = @(
    '(?s)<div class="w-\d+ h-\d+ rounded-xl flex items-center justify-center btn-primary">.*?</div>',
    '(?s)<div class="w-\d+ h-\d+ rounded-xl[^"]*" style="background:linear-gradient[^"]*">.*?</div>',
    '(?s)<div class="w-\d+ h-\d+ bg-indigo-\d+ rounded-\w+ flex items-center justify-center shrink-0">.*?</div>',
    '(?s)<div class="w-\d+ h-\d+ rounded-\w+ flex items-center justify-center"[\s\r\n]*style="background:linear-gradient[^"]*">.*?</div>'
)

$Files = Get-ChildItem -Path $ViewsPath -Recurse -Filter "*.blade.php"

Write-Host ""
Write-Host "  Event Assistance - Remove Logo Icon" -ForegroundColor Cyan
Write-Host "  =====================================" -ForegroundColor Cyan
Write-Host ""

foreach ($File in $Files) {
    $Original = Get-Content $File.FullName -Raw -Encoding UTF8
    $Modified = $Original

    foreach ($Pattern in $Patterns) {
        $Modified = [System.Text.RegularExpressions.Regex]::Replace(
            $Modified,
            $Pattern,
            '',
            [System.Text.RegularExpressions.RegexOptions]::Singleline
        )
    }

    if ($Modified -ne $Original) {
        Set-Content -Path $File.FullName -Value $Modified -Encoding UTF8 -NoNewline
        $Changed++
        $RelPath = $File.FullName.Replace((Get-Location).ToString(), '').TrimStart('\')
        Write-Host "  [FIXED]  $RelPath" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "  =====================================" -ForegroundColor Cyan
Write-Host "  Files updated: $Changed" -ForegroundColor Green
Write-Host ""
Write-Host "  Now run: php artisan view:clear" -ForegroundColor Yellow
Write-Host ""
