@echo off
echo Starting API Server...
cd /d %~dp0
echo API will be available at: http://localhost:8000
echo.
php -S localhost:8000 -t .
pause
