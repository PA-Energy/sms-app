@echo off
echo Updating Composer dependencies for PHP 8.5 compatibility...
cd /d %~dp0
composer update --with-all-dependencies
echo.
echo Update complete! You can now run: composer install
pause
