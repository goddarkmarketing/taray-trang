#Requires -Version 5.1
$ErrorActionPreference = 'Stop'
$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$Stamp = Get-Date -Format 'yyyyMMdd-HHmm'
$OutDir = Join-Path $ProjectRoot "dist\deploy-package-$Stamp"

$ExcludeFiles = @(
    'site.json',
    'admin-credentials.json',
    'data-fallback.js',
    'tt-cache-version.js'
)

$ExcludeDirs = @(
    '.git',
    'dist',
    'node_modules',
    'data\backups',
    'assets\uploads'
)

Write-Host ''
Write-Host '========================================' -ForegroundColor Cyan
Write-Host ' Talay Trang - Deploy code only' -ForegroundColor Cyan
Write-Host '========================================' -ForegroundColor Cyan
Write-Host "From: $ProjectRoot"
Write-Host "To:   $OutDir"
Write-Host ''

if (Test-Path $OutDir) {
    Remove-Item -Recurse -Force $OutDir
}
New-Item -ItemType Directory -Path $OutDir -Force | Out-Null

$robocopyArgs = @(
    $ProjectRoot,
    $OutDir,
    '/E',
    '/NFL', '/NDL', '/NJH', '/NJS', '/NC', '/NS'
)
foreach ($f in $ExcludeFiles) {
    $robocopyArgs += '/XF'
    $robocopyArgs += $f
}
foreach ($d in $ExcludeDirs) {
    $robocopyArgs += '/XD'
    $robocopyArgs += (Join-Path $ProjectRoot $d)
}

& robocopy @robocopyArgs | Out-Null
$rc = $LASTEXITCODE
if ($rc -ge 8) {
    Write-Host "robocopy failed (exit $rc)" -ForegroundColor Red
    exit 1
}

$placeholders = @(
    (Join-Path $OutDir 'data\backups\.gitkeep'),
    (Join-Path $OutDir 'assets\uploads\.gitkeep')
)
foreach ($p in $placeholders) {
    $dir = Split-Path $p -Parent
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
    if (-not (Test-Path $p)) {
        Set-Content -Path $p -Value '' -Encoding UTF8
    }
}

$created = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
$readmePath = Join-Path $OutDir 'DEPLOY-README.txt'
$readme = @"
Talay Trang - deploy package (CODE ONLY)
Generated: $created

This package does NOT include live content from hosting.

NOT included (keep existing files on server):
  - data/site.json
  - data/admin-credentials.json
  - data/backups/
  - assets/uploads/
  - assets/js/data-fallback.js
  - assets/js/tt-cache-version.js

Upload steps (FTP / cPanel):
  1. Backup on host first (admin > Backup & Restore)
  2. Upload this folder MERGE into website root (do not delete data/ on server)
  3. Hard refresh website (Ctrl+Shift+R)
  4. Test admin save once

If content disappears after deploy:
  - Restore from admin > Backup & Restore (auto backup)
"@
Set-Content -Path $readmePath -Value $readme -Encoding UTF8

$readmeThPath = Join-Path $OutDir 'DEPLOY-README-TH.txt'
$thTemplate = Join-Path $PSScriptRoot 'DEPLOY-README-TH.txt'
if (Test-Path $thTemplate) {
    $thContent = Get-Content -Path $thTemplate -Raw -Encoding UTF8
    $thContent = $thContent + "`r`nสร้างเมื่อ: $created`r`n"
    [System.IO.File]::WriteAllText($readmeThPath, $thContent, [System.Text.UTF8Encoding]::new($true))
}

if (Test-Path (Join-Path $OutDir 'data\site.json')) {
    Write-Host 'WARNING: site.json was copied - remove it before upload!' -ForegroundColor Red
    Remove-Item (Join-Path $OutDir 'data\site.json') -Force
}

Write-Host 'Done.' -ForegroundColor Green
Write-Host 'Excluded from package:' -ForegroundColor Yellow
Write-Host '  data/site.json, data/backups/, assets/uploads/, data-fallback.js'
Write-Host ''
Write-Host "Open folder: $OutDir" -ForegroundColor Cyan
Start-Process explorer.exe $OutDir
