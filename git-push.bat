@echo off
SETLOCAL ENABLEDELAYEDEXPANSION

:: Set your GitHub repo URL and branch here
SET GITHUB_URL=https://github.com/londo320/WM_Slot_Bookings.git.git
SET BRANCH=main

:: Check if .git folder exists
IF NOT EXIST ".git" (
    echo Initializing Git...
    git init
    git remote add origin %GITHUB_URL%
    git branch -M %BRANCH%
)

echo Adding all files...
git add .

:: Create a timestamp for the commit
FOR /F "tokens=1-5 delims=/: " %%d IN ("%date% %time%") DO (
    SET TIMESTAMP=%%d-%%e-%%f_%%g-%%h
)

echo Committing changes...
git commit -m "Backup on %TIMESTAMP%"

echo Pushing to GitHub...
git push -u origin %BRANCH%

echo.
echo ✅ Push complete.
pause
