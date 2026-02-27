# XAMPP Public Directory Setup (Optional)

## Current Structure

Currently, the `index.php` is directly in `app/api/` directory:
```
app/api/
├── index.php          ← Entry point (public)
├── .htaccess         ← URL rewriting
├── app/              ← Application code (protected)
├── config/           ← Configuration (protected)
└── ...
```

## For XAMPP Setup

**Current setup works fine!** The `app/api/index.php` is your public entry point.

### Option 1: Use Current Structure (Recommended for XAMPP)

Point Apache DocumentRoot to `app/api/`:
```apache
DocumentRoot "C:/xampp/htdocs/sms-app/app/api"
```

Access: `http://localhost/api/health`

### Option 2: Create Public Directory (Better Security)

If you want a more secure structure with a `public` directory:

1. Create `app/api/public/` directory
2. Move `index.php` and `.htaccess` to `public/`
3. Update paths in `index.php`:
   ```php
   require_once __DIR__ . '/../app/autoload.php';
   require_once __DIR__ . '/../app/bootstrap.php';
   ```
4. Point Apache DocumentRoot to `app/api/public/`

**Note:** The current structure is simpler and works well for XAMPP. The `public` directory structure is more common in frameworks like Laravel, but not necessary for this simple setup.
