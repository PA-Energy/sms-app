@echo off
echo Fixing admin user role...
cd /d %~dp0
php app\api\fix-admin-role.php
pause
