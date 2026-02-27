@echo off
echo ========================================
echo Deploy UI to XAMPP
echo ========================================
echo.

set XAMPP_UI_PATH=C:\xampp\htdocs\sms-app\ui
set BUILD_PATH=app\ui\dist

echo Checking build directory...
if not exist "%BUILD_PATH%" (
    echo ERROR: Build directory not found!
    echo Please run: cd app\ui ^&^& npm run build
    pause
    exit /b 1
)

echo Build directory found!
echo.

echo Deploying to XAMPP...
if exist "%XAMPP_UI_PATH%" (
    echo Removing old deployment...
    rmdir /s /q "%XAMPP_UI_PATH%"
)

echo Creating deployment directory...
mkdir "%XAMPP_UI_PATH%"

echo Copying files...
xcopy /E /I /Y "%BUILD_PATH%\*" "%XAMPP_UI_PATH%"

echo Copying .htaccess for Vue Router...
if exist "app\ui\.htaccess" (
    copy /Y "app\ui\.htaccess" "%XAMPP_UI_PATH%\.htaccess"
    echo .htaccess copied!
) else (
    echo WARNING: .htaccess not found! Vue Router may not work correctly.
)

echo.
echo ========================================
echo Deployment Complete!
echo ========================================
echo.
echo Frontend deployed to: %XAMPP_UI_PATH%
echo.
echo Access at: http://localhost/sms-app/ui/
echo.
pause
