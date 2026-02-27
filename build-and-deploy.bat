@echo off
echo ========================================
echo SMS App - Build and Deploy Script
echo ========================================
echo.

set XAMPP_PATH=C:\xampp\htdocs\sms-app
set UI_BUILD_PATH=app\ui\dist
set DEPLOY_PATH=%XAMPP_PATH%\ui

echo Step 1: Building frontend...
cd app\ui
call npm run build
if errorlevel 1 (
    echo ERROR: Build failed!
    pause
    exit /b 1
)
echo Build successful!
echo.

echo Step 2: Checking XAMPP htdocs...
if not exist "%XAMPP_PATH%" (
    echo.
    echo WARNING: XAMPP path not found: %XAMPP_PATH%
    echo.
    echo Please either:
    echo 1. Copy sms-app folder to C:\xampp\htdocs\
    echo 2. Or update XAMPP_PATH in this script
    echo.
    pause
    exit /b 1
)
echo XAMPP path found!
echo.

echo Step 3: Deploying built files...
if exist "%DEPLOY_PATH%" (
    echo Removing old deployment...
    rmdir /s /q "%DEPLOY_PATH%"
)
echo Creating deployment directory...
mkdir "%DEPLOY_PATH%"
echo Copying built files...
xcopy /E /I /Y "%UI_BUILD_PATH%\*" "%DEPLOY_PATH%"
if errorlevel 1 (
    echo ERROR: Deployment failed!
    pause
    exit /b 1
)

echo Copying .htaccess for Vue Router...
if exist "app\ui\.htaccess" (
    copy /Y "app\ui\.htaccess" "%DEPLOY_PATH%\.htaccess"
    echo .htaccess copied!
)
echo.
echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo Frontend deployed to: %DEPLOY_PATH%
echo.
echo Access your app at:
echo   http://localhost/sms-app/ui/
echo.
echo Make sure:
echo 1. API is running at: http://localhost/sms-app/app/api
echo 2. Update app/ui/.env.production with correct API URL
echo 3. Rebuild if API URL changes
echo.
pause
