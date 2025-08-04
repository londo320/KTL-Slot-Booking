@echo off 
echo Saving your work... 
git add . 
set /p message="Describe your changes: " 
git commit -m "%message%" 
git push origin main 
echo Your changes are saved and synced! 
