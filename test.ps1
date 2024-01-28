Write-Host "hello";
# Read-Host -Prompt "Press any key to continue"
# $files = Get-ChildItem -Directory -Name
$files = Get-ChildItem -File -Name -Exclude *.json
Write-Host (Get-Date -Date "1970/01/01" -UFormat '+%Y-%m-%d %H:%M:%S')
exit;

# $date1 = Get-Date -Date "01/01/1970"
# $date2 = Get-Date
# Write-Host ([int64](New-TimeSpan -Start $date1 -End $date2).TotalSeconds)

foreach ($f in $files){
    # Write-host (Get-item $f).LastWriteTime
    $stuff = (Get-item $f).LastWriteTime
    Write-Host (Get-Date ($stuff).ToUniversalTime() -UFormat '+%Y-%m-%dT%H:%M:%S.000Z')

    # $totalSeconds =  (Get-item $f).LastWriteTime.TotalSeconds
    # Write-Host $totalSeconds

    # Get-Content $f.FullName | Where-Object { ($_ -match 'step4' -or $_ -match 'step9') } | Set-Content $outfile
}


# powershell -noprofile -executionpolicy bypass -file .\test.ps1
# Get-ExecutionPolicy