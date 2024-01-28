Write-Host "hello";
# Read-Host -Prompt "Press any key to continue"
# $files = Get-ChildItem -Directory -Name
$files = Get-ChildItem -File -Name -Exclude *.json

foreach ($f in $files){
    Write-host $f
    # Get-Content $f.FullName | Where-Object { ($_ -match 'step4' -or $_ -match 'step9') } | Set-Content $outfile
}


# powershell -noprofile -executionpolicy bypass -file .\test.ps1
# Get-ExecutionPolicy