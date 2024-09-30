# Zip the attendancebot folder into a attendancebot_{timestamp}.zip file

# Get the current timestamp
timestamp=$(date +%s)

# Zip the attendancebot folder
zip -r attendancebot_${timestamp}.zip attendancebot

# Move the zip file to the current directory
mv attendancebot_${timestamp}.zip ./

# Print the path to the zip file
echo "attendancebot_${timestamp}.zip"

# End of file
exit 0