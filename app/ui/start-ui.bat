@echo off
echo Starting UI Development Server...
cd /d %~dp0
if not exist .env (
    echo Creating .env file...
    echo VITE_API_URL=http://localhost:8000/api > .env
)
npm run dev
