@echo off
echo ========================================
echo SMS App - XAMPP Setup Script
echo ========================================
echo.

echo This script will help you set up the SMS App for XAMPP.
echo.
echo Prerequisites:
echo - XAMPP installed
echo - Apache and MySQL running in XAMPP
echo.
pause

echo.
echo Step 1: Checking XAMPP installation...
if not exist "C:\xampp\apache\conf\httpd.conf" (
    echo ERROR: XAMPP not found at C:\xampp
    echo Please install XAMPP or update the path in this script.
    pause
    exit /b 1
)
echo XAMPP found!

echo.
echo Step 2: Checking if project is in htdocs...
set PROJECT_PATH=C:\xampp\htdocs\sms-app
if not exist "%PROJECT_PATH%" (
    echo.
    echo Project not found at %PROJECT_PATH%
    echo.
    echo Please copy the sms-app folder to: C:\xampp\htdocs\
    echo Or update PROJECT_PATH in this script to match your location.
    pause
    exit /b 1
)
echo Project found!

echo.
echo Step 3: Setting up database...
cd /d "%PROJECT_PATH%\app\api"
if exist "setup-db.php" (
    echo Running database setup...
    php setup-db.php
    if errorlevel 1 (
        echo ERROR: Database setup failed!
        echo Please check your MySQL configuration.
        pause
        exit /b 1
    )
    echo Database setup complete!
) else (
    echo WARNING: setup-db.php not found!
)

echo.
echo Step 4: Checking Apache configuration...
echo.
echo IMPORTANT: You need to manually:
echo 1. Enable mod_rewrite in C:\xampp\apache\conf\httpd.conf
echo    (Uncomment: LoadModule rewrite_module modules/mod_rewrite.so)
echo.
echo 2. Enable mod_headers in C:\xampp\apache\conf\httpd.conf
echo    (Uncomment: LoadModule headers_module modules/mod_headers.so)
echo.
echo 3. Optional: Add virtual host configuration
echo    - Copy xampp-vhost.conf content to C:\xampp\apache\conf\extra\httpd-vhosts.conf
echo    - Add 127.0.0.1 sms-app.local to C:\Windows\System32\drivers\etc\hosts
echo.

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Start Apache and MySQL from XAMPP Control Panel
echo 2. Test API: http://localhost/sms-app/app/api/api/health
echo 3. Update app/ui/.env with: VITE_API_URL=http://localhost/sms-app/app/api/api
echo 4. Run frontend: cd app/ui ^&^& npm run dev
echo.
pause
