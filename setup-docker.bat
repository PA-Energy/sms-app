@echo off
echo ========================================
echo SMS App Setup Script (Docker)
echo ========================================
echo.

echo [1/7] Starting MySQL container...
docker-compose up -d mysql
timeout /t 5 /nobreak >nul
echo MySQL container started!
echo.

echo [2/7] Installing PHP dependencies...
docker-compose run --rm composer install
if errorlevel 1 (
    echo ERROR: Composer install failed.
    pause
    exit /b 1
)
echo Dependencies installed!
echo.

echo [3/7] Generating application key...
docker-compose run --rm artisan key:generate
if errorlevel 1 (
    echo ERROR: Failed to generate application key.
    pause
    exit /b 1
)
echo Application key generated!
echo.

echo [4/7] Generating JWT secret...
docker-compose run --rm artisan jwt:secret
if errorlevel 1 (
    echo ERROR: Failed to generate JWT secret.
    pause
    exit /b 1
)
echo JWT secret generated!
echo.

echo [5/7] Running database migrations...
docker-compose run --rm artisan migrate
if errorlevel 1 (
    echo ERROR: Database migration failed. Please check MySQL is running.
    pause
    exit /b 1
)
echo Migrations completed!
echo.

echo [6/7] Seeding database with admin user...
docker-compose run --rm artisan db:seed
if errorlevel 1 (
    echo ERROR: Database seeding failed.
    pause
    exit /b 1
)
echo Database seeded!
echo.

echo [7/7] Setup Complete!
echo ========================================
echo.
echo Default Admin Credentials:
echo   Username: admin
echo   Password: admin123
echo.
echo To start the backend server, run:
echo   docker-compose run -d -p 8000:8000 artisan serve --host=0.0.0.0
echo.
echo Or install PHP locally and use:
echo   cd app\api
echo   php artisan serve
echo.
pause
