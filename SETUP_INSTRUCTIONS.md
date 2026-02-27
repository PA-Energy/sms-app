# PHP Setup Instructions for Windows

Since PHP is not installed on your system, you have several options:

## Option 1: Install PHP on Windows (Recommended for Development)

### Using XAMPP (Easiest)
1. Download XAMPP from: https://www.apachefriends.org/
2. Install XAMPP (includes PHP, MySQL, Apache)
3. Add PHP to PATH:
   - Open System Properties → Environment Variables
   - Add `C:\xampp\php` to your PATH
   - Restart your terminal

### Using PHP Standalone
1. Download PHP from: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add `C:\php` to your PATH
4. Install Composer from: https://getcomposer.org/download/

## Option 2: Use Docker for PHP Commands

I've updated `docker-compose.yml` to include PHP containers. You can use these commands:

### Install Dependencies
```bash
docker-compose run --rm composer install
```

### Run Artisan Commands
```bash
# Generate key
docker-compose run --rm artisan key:generate

# Generate JWT secret
docker-compose run --rm artisan jwt:secret

# Run migrations
docker-compose run --rm artisan migrate

# Seed database
docker-compose run --rm artisan db:seed

# Start server (this one needs to stay running)
docker-compose run --rm -p 8000:8000 artisan serve --host=0.0.0.0
```

## Option 3: Use Laravel Sail (Full Docker Setup)

If you want everything in Docker, we can set up Laravel Sail, but it requires restructuring the project.

## Quick Start with Docker Commands

After installing dependencies, you can use these Docker commands:

```bash
# 1. Install dependencies
docker-compose run --rm composer install

# 2. Generate keys
docker-compose run --rm artisan key:generate
docker-compose run --rm artisan jwt:secret

# 3. Run migrations
docker-compose run --rm artisan migrate

# 4. Seed database
docker-compose run --rm artisan db:seed

# 5. Start server (in background)
docker-compose run -d -p 8000:8000 artisan serve --host=0.0.0.0
```

## Recommended: Install PHP Locally

For the best development experience, I recommend installing PHP locally using XAMPP or the standalone PHP installer. This will make all `php artisan` commands work directly.
