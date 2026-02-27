# SMS Application with GoIP Integration

A simple cross-platform SMS application with custom PHP MVC backend and Vue.js frontend that synchronizes SMS inbox from GoIP device to MySQL, supports user authentication with JWT, and enables SMS sending/blasting functionality.

## Architecture

- **Backend**: Simple PHP MVC API (no framework, no JWT) in `/app/api`
- **Frontend**: Vue.js + TypeScript + Tailwind CSS in `/app/ui`
- **Database**: MySQL running in Docker container
- **Integration**: GoIP device communication via HTTP API

## Prerequisites

- Docker and Docker Compose
- PHP 8.1+ (for API)
- Composer (PHP package manager)
- Node.js 18+ and npm (for Vue.js frontend)

## Quick Setup

### 1. Start MySQL Database

```bash
docker-compose up -d mysql
```

### 2. Backend Setup

```bash
cd app/api

# Install dependencies (only JWT library)
composer install

# Setup database and create admin user
php setup-db.php

# Start API server
php -S localhost:8000 -t .
```

Or use the batch file:
```bash
start-api.bat
```

### 3. Frontend Setup

```bash
cd app/ui

# Install dependencies
npm install

# Create .env file (if not exists)
echo VITE_API_URL=http://localhost:8000/api > .env

# Start development server
npm run dev
```

Or use the batch file:
```bash
start-ui.bat
```

## Default Admin Credentials

- **Username**: `admin`
- **Password**: `admin123`

**Important**: Change the default password after first login for security purposes.

## Configuration

### Backend Environment Variables

Edit `app/api/.env`:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sms_app
DB_USERNAME=root
DB_PASSWORD=sms_app_root_password

TOKEN_EXPIRY_HOURS=24

GOIP_ADDR=192.168.1.3
GOIP_USER=admin
GOIP_PASSWORD=admin
GOIP_LINE=1
```

### Frontend Environment Variables

Edit `app/ui/.env`:

```env
VITE_API_URL=http://localhost:8000/api
```

## Features

1. **SMS Inbox Synchronization**
   - Manual sync button triggers immediate sync
   - Deduplication based on phone + date + text

2. **SMS Sending**
   - Single SMS via GoIP device
   - Track status (pending/sent/failed)
   - Error handling

3. **SMS Blasting**
   - CSV upload with phone numbers
   - Manual entry (textarea with one per line)
   - Progress tracking
   - Individual recipient status

4. **Authentication**
   - Simple token-based session auth
   - Protected routes
   - Token expiry (24 hours default)

## API Endpoints

### Authentication
- `POST /api/auth/login` - Login
- `GET /api/auth/me` - Get current user
- `POST /api/auth/logout` - Logout

### SMS Inbox
- `GET /api/sms/inbox` - List messages
- `POST /api/sms/inbox/sync` - Sync from GoIP
- `PUT /api/sms/inbox/{id}/read` - Mark as read
- `PUT /api/sms/inbox/read-all` - Mark all as read

### SMS Outbox
- `GET /api/sms/outbox` - List sent messages
- `POST /api/sms/send` - Send single SMS

### SMS Blast
- `GET /api/sms/blast` - List batches
- `POST /api/sms/blast` - Create batch (manual)
- `POST /api/sms/blast/upload-csv` - Create batch (CSV)
- `GET /api/sms/blast/{id}` - Get batch details
- `GET /api/sms/blast/{id}/progress` - Get batch progress

## Project Structure

```
/app/api/
  ├── index.php              # Entry point
  ├── setup-db.php           # Database setup script
  ├── app/
  │   ├── Core/              # Core classes (Router, Database, Auth, Controller)
  │   ├── Controllers/       # MVC Controllers
  │   ├── Models/            # Database models
  │   ├── Services/          # Business logic services
  │   └── Traits/            # GoIP trait
  └── .env                   # Environment configuration

/app/ui/
  ├── src/
  │   ├── views/             # Vue pages
  │   ├── components/        # Vue components
  │   ├── services/          # API service
  │   ├── stores/            # Pinia stores
  │   └── router/            # Vue Router
  └── .env                   # Frontend environment
```

## Troubleshooting

### UI not running
1. Make sure Node.js is installed: `node --version`
2. Install dependencies: `cd app/ui && npm install`
3. Check if port 5173 is available
4. Check browser console for errors

### API not running
1. Make sure PHP is installed: `php --version`
2. Make sure MySQL container is running: `docker-compose ps`
3. Check if port 8000 is available
4. Run database setup: `php setup-db.php`

### Database connection errors
1. Verify MySQL container is running: `docker-compose ps`
2. Check database credentials in `.env`
3. Make sure database exists: `php setup-db.php`

## License

MIT
