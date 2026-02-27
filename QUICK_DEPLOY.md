# Quick Production Deployment Guide

## Quick Steps for XAMPP Production Deployment

### 1. Configure Production API URL

Create `app/ui/.env.production`:
```env
VITE_API_URL=http://localhost/sms-app/app/api/api
```

**Important**: Update this URL to match your XAMPP setup!

### 2. Build Frontend

```bash
cd app/ui
npm run build
```

This creates production files in `app/ui/dist/`

### 3. Deploy to XAMPP

**Option A: Automated Script**
```bash
build-and-deploy.bat
```

**Option B: Manual**
```bash
# Copy dist/* to:
C:\xampp\htdocs\sms-app\ui\
```

### 4. Copy .htaccess

Copy `app/ui/.htaccess` to `C:\xampp\htdocs\sms-app\ui\.htaccess`

### 5. Access Your App

- **Frontend**: `http://localhost/sms-app/ui/`
- **API**: `http://localhost/sms-app/app/api/api/health`

## File Structure After Deployment

```
C:\xampp\htdocs\sms-app\
├── app\
│   └── api\              ← Backend API (PHP)
│       ├── index.php
│       ├── .htaccess
│       └── ...
└── ui\                   ← Frontend (Built static files)
    ├── index.html
    ├── assets\
    ├── .htaccess
    └── ...
```

## Important Notes

1. **API URL**: Must be set in `.env.production` before building
2. **Rebuild**: Required after changing API URL or frontend code
3. **.htaccess**: Needed for Vue Router to work correctly
4. **No npm needed**: After deployment, frontend runs as static files

## Troubleshooting

- **404 on routes**: Check `.htaccess` is in `ui/` directory
- **API errors**: Verify API URL in `.env.production` matches your setup
- **CORS errors**: API already handles CORS, check API URL is correct
