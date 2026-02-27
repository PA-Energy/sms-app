# XAMPP Production Deployment Guide

This guide explains how to build and deploy the Vue.js frontend to XAMPP for production use.

## Overview

For production deployment:
1. **Backend API**: Runs from `C:\xampp\htdocs\sms-app\app\api\`
2. **Frontend UI**: Built static files deployed to `C:\xampp\htdocs\sms-app\ui\`

## Step 1: Configure Production API URL

1. Edit `app/ui/.env.production`:
   ```env
   # If API is at: http://localhost/sms-app/app/api
   VITE_API_URL=http://localhost/sms-app/app/api/api
   
   # Or if using virtual host: http://sms-app.local/api
   # VITE_API_URL=http://sms-app.local/api
   ```

2. **Important**: The API URL must match your XAMPP setup!

## Step 2: Build the Frontend

### Option A: Using the Build Script (Recommended)

```bash
build-and-deploy.bat
```

This script will:
- Build the frontend (`npm run build`)
- Copy built files to XAMPP htdocs
- Deploy to `C:\xampp\htdocs\sms-app\ui\`

### Option B: Manual Build

```bash
cd app/ui

# Install dependencies (if not done)
npm install

# Build for production
npm run build

# The built files will be in: app/ui/dist/
```

## Step 3: Deploy to XAMPP

### Option A: Using Build Script

The `build-and-deploy.bat` script automatically deploys to XAMPP.

### Option B: Manual Deployment

1. Copy contents of `app/ui/dist/` to:
   ```
   C:\xampp\htdocs\sms-app\ui\
   ```

2. Your XAMPP structure should be:
   ```
   C:\xampp\htdocs\sms-app\
   ├── app\
   │   └── api\          ← Backend API
   │       ├── index.php
   │       └── ...
   └── ui\               ← Frontend (built static files)
       ├── index.html
       ├── assets\
       └── ...
   ```

## Step 4: Access Your Application

After deployment, access your app at:
- **Frontend**: `http://localhost/sms-app/ui/`
- **API**: `http://localhost/sms-app/app/api/api/health`

## Step 5: Configure Apache (if needed)

If you want cleaner URLs, you can set up Apache virtual hosts or aliases:

### Option 1: Virtual Host

Add to `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
```apache
<VirtualHost *:80>
    ServerName sms-app.local
    DocumentRoot "C:/xampp/htdocs/sms-app"
    
    # API
    Alias /api "C:/xampp/htdocs/sms-app/app/api"
    <Directory "C:/xampp/htdocs/sms-app/app/api">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Frontend
    <Directory "C:/xampp/htdocs/sms-app/ui">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "C:/xampp/apache/logs/sms-app-error.log"
    CustomLog "C:/xampp/apache/logs/sms-app-access.log" common
</VirtualHost>
```

Then access:
- Frontend: `http://sms-app.local/ui/`
- API: `http://sms-app.local/api/api/health`

### Option 2: Direct Access (Simpler)

Just use:
- Frontend: `http://localhost/sms-app/ui/`
- API: `http://localhost/sms-app/app/api/api/health`

## Important Notes

### Environment Variables

- **Development**: Uses `app/ui/.env` (or `.env.local`)
- **Production Build**: Uses `app/ui/.env.production`

The API URL in `.env.production` is baked into the build at build time.

### Rebuilding After Changes

If you change:
- API URL → Rebuild and redeploy
- Frontend code → Rebuild and redeploy
- Backend code → Just restart Apache (no rebuild needed)

### Quick Rebuild Command

```bash
cd app/ui
npm run build
# Then copy dist/* to C:\xampp\htdocs\sms-app\ui\
```

## Troubleshooting

### 404 Errors on Routes

If you get 404 errors when navigating, you need to configure Apache to handle Vue Router's history mode.

Create `C:\xampp\htdocs\sms-app\ui\.htaccess`:
```apache
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /sms-app/ui/
  RewriteRule ^index\.html$ - [L]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule . /sms-app/ui/index.html [L]
</IfModule>
```

### CORS Errors

The API already sets CORS headers. If you still get CORS errors:
- Check that API URL in `.env.production` matches your setup
- Verify Apache `mod_headers` is enabled

### API Not Found

- Check API URL in `.env.production` matches your XAMPP setup
- Verify API is accessible: `http://localhost/sms-app/app/api/api/health`
- Rebuild frontend if you changed the API URL
