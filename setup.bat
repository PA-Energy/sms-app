@echo off
echo ========================================
echo SMS App Setup Script
echo ========================================
echo.

echo [1/6] Starting MySQL container...
cd /d %~dp0
docker-compose up -d mysql
timeout /t 5 /nobreak >nul
echo MySQL container started!
echo.

echo [2/6] Installing PHP dependencies...
cd app\api
if exist composer.phar (
    php composer.phar install
) else (
    composer install
)
if errorlevel 1 (
    echo ERROR: Composer install failed. Please ensure Composer is installed and in your PATH.
    pause
    exit /b 1
)
echo Dependencies installed!
echo.

echo [3/6] Generating application key...
php artisan key:generate
if errorlevel 1 (
    echo ERROR: Failed to generate application key.
    pause
    exit /b 1
)
echo Application key generated!
echo.

echo [4/6] Generating JWT secret...
php artisan jwt:secret
if errorlevel 1 (
    echo ERROR: Failed to generate JWT secret.
    pause
    exit /b 1
)
echo JWT secret generated!
echo.

echo [5/6] Running database migrations...
php artisan migrate
if errorlevel 1 (
    echo ERROR: Database migration failed. Please check MySQL is running.
    pause
    exit /b 1
)
echo Migrations completed!
echo.

echo [6/6] Seeding database with admin user...
php artisan db:seed
if errorlevel 1 (
    echo ERROR: Database seeding failed.
    pause
    exit /b 1
)
echo Database seeded!
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Default Admin Credentials:
echo   Username: admin
echo   Password: admin123
echo.
echo To start the backend server, run:
echo   cd app\api
echo   php artisan serve
echo.
echo To start the frontend, run (in another terminal):
echo   cd app\ui
echo   npm run dev
echo.
pause
