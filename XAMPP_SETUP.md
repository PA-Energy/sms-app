# XAMPP Setup Instructions

This guide will help you set up the SMS Application to run on Apache XAMPP without using PHP command line.

## Prerequisites

- XAMPP installed (with Apache and MySQL)
- PHP 8.1 or higher
- MySQL running in XAMPP (or use Docker MySQL)

## Step 1: Copy Project to XAMPP

1. Copy the entire `sms-app` folder to your XAMPP `htdocs` directory:
   ```
   C:\xampp\htdocs\sms-app\
   ```

   Or if you prefer a different location, you can use a virtual host (see Step 3).

## Step 2: Configure Database

### Option A: Use XAMPP MySQL

1. Start MySQL from XAMPP Control Panel
2. Open `http://localhost/phpmyadmin`
3. Create a new database named `sms_app`
4. Update `app/api/config/database.php`:
   ```php
   'mysql' => [
       'host' => env('DB_HOST', '127.0.0.1'),
       'port' => env('DB_PORT', '3306'),
       'database' => env('DB_DATABASE', 'sms_app'),
       'username' => env('DB_USERNAME', 'root'),
       'password' => env('DB_PASSWORD', ''), // XAMPP default is empty
   ],
   ```

5. Run the setup script:
   - Open browser: `http://localhost/sms-app/app/api/setup-db.php`
   - Or use XAMPP shell: `php C:\xampp\htdocs\sms-app\app\api\setup-db.php`

### Option B: Use Docker MySQL (Recommended)

If you're using Docker MySQL, keep the database configuration as is and ensure Docker MySQL is running.

## Step 3: Configure Apache Virtual Host (Recommended)

### Method 1: Using XAMPP Virtual Hosts

1. Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

2. Add the following configuration:
   ```apache
   <VirtualHost *:80>
       ServerName sms-app.local
       DocumentRoot "C:/xampp/htdocs/sms-app/app/api"
       
       <Directory "C:/xampp/htdocs/sms-app/app/api">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog "C:/xampp/apache/logs/sms-app-error.log"
       CustomLog "C:/xampp/apache/logs/sms-app-access.log" common
   </VirtualHost>
   ```

3. Open `C:\Windows\System32\drivers\etc\hosts` (as Administrator) and add:
   ```
   127.0.0.1    sms-app.local
   ```

4. Restart Apache from XAMPP Control Panel

5. Access API at: `http://sms-app.local/api/health`

### Method 2: Direct Access (Simpler)

1. Access API directly at: `http://localhost/sms-app/app/api/api/health`

2. Update frontend `.env`:
   ```env
   VITE_API_URL=http://localhost/sms-app/app/api/api
   ```

## Step 4: Enable Required Apache Modules

1. Open `C:\xampp\apache\conf\httpd.conf`

2. Ensure these modules are enabled (uncomment if needed):
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   LoadModule headers_module modules/mod_headers.so
   ```

3. Restart Apache

## Step 5: Verify .htaccess File

The `.htaccess` file in `app/api/` should contain:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## Step 6: Test the Setup

1. Start Apache and MySQL from XAMPP Control Panel

2. Test API health endpoint:
   - With virtual host: `http://sms-app.local/api/health`
   - Without virtual host: `http://localhost/sms-app/app/api/api/health`

3. You should see: `{"status":"ok"}`

## Step 7: Frontend Configuration

Update `app/ui/.env`:
```env
# If using virtual host
VITE_API_URL=http://sms-app.local/api

# If using direct access
VITE_API_URL=http://localhost/sms-app/app/api/api
```

## Troubleshooting

### 403 Forbidden Error
- Check `httpd.conf` - ensure `Require all granted` is set
- Check directory permissions
- Verify `.htaccess` file exists

### 404 Not Found
- Check that `mod_rewrite` is enabled
- Verify `.htaccess` file is in `app/api/` directory
- Check Apache error logs: `C:\xampp\apache\logs\error.log`

### Database Connection Failed
- Verify MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Ensure database `sms_app` exists

### CORS Errors
- The API already sets CORS headers in `bootstrap.php`
- If issues persist, check Apache `mod_headers` is enabled

## Quick Start Commands

After setup, you can:
- Access API: `http://localhost/sms-app/app/api/api/health`
- Access Frontend: Run `npm run dev` in `app/ui` directory
- Login: Username `admin`, Password `admin123`
