#Requires -Version 5.1
<#
.SYNOPSIS
  ดึงเนื้อหาจากไฟล์แบ็คอัพ (โฮส) มาใช้บน localhost — ให้ dev ตรงกับโฮส

.USAGE
  powershell -File Sync-LocalFromBackup.ps1 -BackupFile "C:\Downloads\20260602_manual.json"
#>
param(
    [Parameter(Mandatory = $true)]
    [string]$BackupFile
)

$ErrorActionPreference = 'Stop'
$ProjectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
$Target = Join-Path $ProjectRoot 'data\site.json'
$Fallback = Join-Path $ProjectRoot 'assets\js\data-fallback.js'

if (-not (Test-Path $BackupFile)) {
    Write-Host "ไม่พบไฟล์: $BackupFile" -ForegroundColor Red
    exit 1
}

Copy-Item -Path $Target -Destination ($Target + '.before-sync') -Force -ErrorAction SilentlyContinue
Copy-Item -Path $BackupFile -Destination $Target -Force

Write-Host 'คัดลอก site.json แล้ว — กำลังสร้าง data-fallback.js...' -ForegroundColor Cyan
$phpCode = @"
require '$($ProjectRoot -replace '\\','/')/includes/functions.php';
`$d = tt_read_data();
if (!tt_write_data(`$d)) { fwrite(STDERR, 'fail'); exit(1); }
echo 'ok';
"@
$phpCode | & c:\xampp\php\php.exe 2>&1 | Out-Null
if ($LASTEXITCODE -ne 0) {
    Write-Host 'สร้าง fallback ไม่สำเร็จ — เปิด admin แล้วกดบันทึกครั้งหนึ่ง' -ForegroundColor Yellow
}

Write-Host ''
Write-Host 'sync เรียบร้อย — localhost ใช้เนื้อหาเดียวกับแบ็คอัพแล้ว' -ForegroundColor Green
Write-Host "สำรองของเดิมไว้ที่: $($Target).before-sync"
