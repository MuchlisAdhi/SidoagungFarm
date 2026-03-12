param(
    [switch]$Clean
)

$ErrorActionPreference = 'Stop'

$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$workDir = Join-Path $scriptDir 'work'
$downloadDir = Join-Path $workDir 'downloads'
$extractDir = Join-Path $workDir 'extract'
$binDir = Join-Path $scriptDir 'bin'
$packageDir = Join-Path $scriptDir 'packages'
$archivePath = Join-Path $packageDir 'image-optimizer-linux-amd64.tar.gz'
$msysTar = 'C:\msys64\usr\bin\tar.exe'
$extractTarExe = if (Test-Path $msysTar) { $msysTar } else { 'tar' }
$packageTarExe = 'tar'
$useMsysExtract = $extractTarExe -eq $msysTar

function Ensure-Dir {
    param([string]$Path)
    if (-not (Test-Path $Path)) {
        New-Item -ItemType Directory -Path $Path -Force | Out-Null
    }
}

function Convert-ToMsysPath {
    param([string]$Path)
    $full = [System.IO.Path]::GetFullPath($Path)
    $drive = $full.Substring(0, 1).ToLower()
    $rest = $full.Substring(2).Replace('\', '/')
    return "/$drive$rest"
}

if ($Clean) {
    Remove-Item $workDir -Recurse -Force -ErrorAction SilentlyContinue
    Remove-Item $binDir -Recurse -Force -ErrorAction SilentlyContinue
    Remove-Item $packageDir -Recurse -Force -ErrorAction SilentlyContinue
    Write-Host "Clean finished."
    exit 0
}

$packages = @(
    @{
        Name = 'jpegoptim'
        Binary = 'jpegoptim'
        Url = 'https://deb.debian.org/debian/pool/main/j/jpegoptim/jpegoptim_1.4.7-1_amd64.deb'
    },
    @{
        Name = 'optipng'
        Binary = 'optipng'
        Url = 'https://deb.debian.org/debian/pool/main/o/optipng/optipng_0.7.7-2+b1_amd64.deb'
    },
    @{
        Name = 'pngquant'
        Binary = 'pngquant'
        Url = 'https://deb.debian.org/debian/pool/main/p/pngquant/pngquant_2.17.0-1_amd64.deb'
    },
    @{
        Name = 'gifsicle'
        Binary = 'gifsicle'
        Url = 'https://deb.debian.org/debian/pool/main/g/gifsicle/gifsicle_1.93-2_amd64.deb'
    }
)

Ensure-Dir $workDir
Ensure-Dir $downloadDir
Ensure-Dir $extractDir
Ensure-Dir $binDir
Ensure-Dir $packageDir

Remove-Item "$extractDir\*" -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item "$binDir\*" -Recurse -Force -ErrorAction SilentlyContinue

foreach ($pkg in $packages) {
    $debPath = Join-Path $downloadDir ($pkg.Name + '.deb')
    Write-Host "Downloading $($pkg.Name) ..."
    Invoke-WebRequest -Uri $pkg.Url -OutFile $debPath

    $pkgExtractDir = Join-Path $extractDir $pkg.Name
    Ensure-Dir $pkgExtractDir
    Remove-Item "$pkgExtractDir\*" -Recurse -Force -ErrorAction SilentlyContinue

    Push-Location $pkgExtractDir
    try {
        & ar x $debPath
    }
    finally {
        Pop-Location
    }

    $dataArchive = Get-ChildItem -Path $pkgExtractDir -Filter 'data.tar.*' | Select-Object -First 1
    if (-not $dataArchive) {
        throw "Failed to find data archive in package $($pkg.Name)"
    }

    $rootDir = Join-Path $pkgExtractDir 'root'
    Ensure-Dir $rootDir
    $dataArg = if ($useMsysExtract) { Convert-ToMsysPath $dataArchive.FullName } else { $dataArchive.FullName }
    $rootArg = if ($useMsysExtract) { Convert-ToMsysPath $rootDir } else { $rootDir }
    & $extractTarExe -xf $dataArg -C $rootArg
    if ($LASTEXITCODE -ne 0) {
        throw "Failed extracting package archive for $($pkg.Name)"
    }

    $sourceBinary = Join-Path $rootDir ("usr/bin/" + $pkg.Binary)
    if (-not (Test-Path $sourceBinary)) {
        throw "Binary not found for $($pkg.Name): $sourceBinary"
    }

    Copy-Item -Path $sourceBinary -Destination (Join-Path $binDir $pkg.Binary) -Force
}

if (Test-Path $archivePath) {
    Remove-Item $archivePath -Force
}

& $packageTarExe -czf $archivePath -C $binDir .
if ($LASTEXITCODE -ne 0) {
    throw "Failed creating package archive"
}

Write-Host ""
Write-Host "Package created:"
Write-Host $archivePath
Write-Host ""
Get-ChildItem $binDir | Select-Object Name, Length
