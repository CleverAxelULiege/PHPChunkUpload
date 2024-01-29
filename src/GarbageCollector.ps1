 param (
    [switch]$force = $false
 )


$pathToTemp = "D:\php_projects\upload\upload\temp"
$formatDate = "+%Y-%m-%d %H:%M:%S"
$maxTimeLifeSpan = 60 * 60 * 8

if (-NOT (Test-Path -Path $pathToTemp)) {
    Write-Error "Temporary folder doesn't exist. Exiting garbage collector"
    exit
}

Set-Location -Path $pathToTemp
# Write-Host (Get-Location)

$startUnixTimeStamp = (Get-Date -Date "1970-01-01" -UFormat $formatDate);
$currentTimeStamp = (Get-Date -Date (Get-Date).ToUniversalTime() -UFormat $formatDate);

$currentUnixTimeStamp = ([int64](New-TimeSpan -Start $startUnixTimeStamp -End $currentTimeStamp).TotalSeconds)

$directories = Get-ChildItem -Directory -Name

if($directories.Count -eq 0){
    Write-Warning "No temp directory found"
    exit;
}

foreach ($directory in $directories){
    $relaTivedirectory = ".\" + $directory
    $files = Get-ChildItem -Path $relaTivedirectory -File -Name -Include *.json

    foreach ($file in $files){
        $lastWriteTime = (Get-item ($relaTivedirectory + "\" + $file)).LastWriteTime
        $lastWriteTime = (Get-Date ($lastWriteTime).ToUniversalTime() -UFormat $formatDate)
        $lastWriteTime = ([int64](New-TimeSpan -Start $startUnixTimeStamp -End $lastWriteTime).TotalSeconds)

        if((($currentUnixTimeStamp - $lastWriteTime) -gt $maxTimeLifeSpan) -or $force){
            if(Test-Path -Path ($pathToTemp + "\" +$directory)){
                Remove-Item -Path ($pathToTemp + "\" +$directory) -Force -Recurse
                # Write-Host "Deleted temporary directory : "($pathToTemp + $directory)
            } else {
                Write-Warning "Temporary directory doesn't exist"
            }
        } else {
            # Write-Host "Upload still active for :" ($pathToTemp + $directory)
        }
    }
}

# powershell -executionpolicy bypass -file .\GarbageCollector.ps1