$timestamp = Get-Date -Format "yyyyMMddHHmmss"

Compress-Archive -Path attendancebot -DestinationPath "attendancebot_$timestamp.zip"

Write-Output "attendancebot_$timestamp.zip"

exit