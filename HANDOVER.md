# SMS Application - Handover Document Prompt

## Instructions for Gemini Docs Assistant

**Copy the content below and paste it into Google Docs, then use Gemini to generate a professional handover document:**

---

# Project Handover: SMS Application with GoIP Integration

## Project Overview

This document provides a comprehensive handover for the SMS Application project, a cross-platform SMS management system that integrates with GoIP devices for sending and receiving SMS messages.

## Project Details

**Project Name:** SMS Application with GoIP Integration  
**Repository:** git@github.com:PA-Energy/sms-app.git  
**Technology Stack:** PHP 8.1+, Vue.js 3, TypeScript, Tailwind CSS, MySQL 8.0  
**Architecture:** Custom PHP MVC API + Vue.js SPA  
**Database:** MySQL (Docker containerized)  
**Authentication:** Token-based session management  

## System Architecture

### Backend (PHP MVC API)
- **Location:** `/app/api`
- **Framework:** Custom lightweight PHP MVC (no external framework dependencies)
- **Entry Point:** `index.php`
- **Core Components:**
  - Custom Router (`app/Core/Router.php`)
  - Database Singleton (`app/Core/Database.php`)
  - Authentication System (`app/Core/Auth.php`)
  - Base Controller (`app/Core/Controller.php`)
  - Custom Autoloader (`app/autoload.php` - no Composer required)

### Frontend (Vue.js SPA)
- **Location:** `/app/ui`
- **Framework:** Vue.js 3 with TypeScript
- **Build Tool:** Vite
- **Styling:** Tailwind CSS
- **State Management:** Pinia
- **Routing:** Vue Router

### Database
- **Type:** MySQL 8.0
- **Container:** Docker Compose
- **Database Name:** `sms_app`
- **Tables:**
  - `users` - User accounts
  - `user_tokens` - Authentication tokens
  - `sms_messages` - Inbox messages (synced from GoIP)
  - `sms_outbox` - Outgoing messages
  - `sms_batches` - SMS blast batches
  - `sms_batch_recipients` - Individual recipients in batches

## Key Features

1. **SMS Inbox Synchronization**
   - Manual sync from GoIP device
   - Automatic deduplication
   - Read/unread status tracking

2. **SMS Sending**
   - Single SMS via GoIP device
   - Status tracking (pending/sent/failed)
   - Error handling and logging

3. **SMS Blasting**
   - CSV upload support
   - Manual entry (one phone per line)
   - Batch processing with progress tracking
   - Individual recipient status

4. **User Authentication**
   - Token-based session management
   - 24-hour token expiry (configurable)
   - Protected API routes

## Setup Instructions

### Prerequisites
- Docker and Docker Compose
- PHP 8.1+ (for API)
- Node.js 18+ and npm (for frontend)

### Database Setup
```bash
# Start MySQL container
docker-compose up -d mysql

# Setup database and create admin user
# Windows:
setup-db.bat

# Linux/Mac/Git Bash:
./setup-db.sh

# Or manually:
php app/api/setup-db.php
```

### Backend Setup
```bash
cd app/api
php -S localhost:8000 -t .
```

### Frontend Setup
```bash
cd app/ui
npm install
npm run dev
```

## Default Credentials

- **Username:** `admin`
- **Password:** `admin123`

**⚠️ IMPORTANT:** Change default password immediately after first login!

## Configuration Files

### Backend Configuration
- **Environment:** `app/api/.env` (optional, uses config/database.php as fallback)
- **Database Config:** `app/api/config/database.php`
- **Bootstrap:** `app/api/app/bootstrap.php`

### Frontend Configuration
- **Environment:** `app/ui/.env`
  - `VITE_API_URL=http://localhost:8000/api`

### GoIP Configuration
Configure in `app/api/config/database.php` or `.env`:
- `GOIP_ADDR` - GoIP device IP address (default: 192.168.1.3)
- `GOIP_USER` - GoIP username (default: admin)
- `GOIP_PASSWORD` - GoIP password (default: admin)
- `GOIP_LINE` - GoIP line number (default: 1)

## API Endpoints

### Authentication
- `POST /api/auth/login` - User login
- `GET /api/auth/me` - Get current user info
- `POST /api/auth/logout` - Logout

### SMS Inbox
- `GET /api/sms/inbox` - List inbox messages
- `POST /api/sms/inbox/sync` - Sync messages from GoIP
- `PUT /api/sms/inbox/{id}/read` - Mark message as read
- `PUT /api/sms/inbox/read-all` - Mark all as read
- `GET /api/sms/inbox/{id}` - Get message details

### SMS Outbox
- `GET /api/sms/outbox` - List sent messages
- `POST /api/sms/send` - Send single SMS
- `GET /api/sms/outbox/{id}` - Get message details

### SMS Blast
- `GET /api/sms/blast` - List blast batches
- `POST /api/sms/blast` - Create batch (manual entry)
- `POST /api/sms/blast/upload-csv` - Create batch (CSV upload)
- `GET /api/sms/blast/{id}` - Get batch details
- `GET /api/sms/blast/{id}/progress` - Get batch progress

### Utility
- `GET /api/health` - Health check
- `GET /api/cleanup-tokens` - Cleanup expired tokens

## Database Schema

### users
- `id` (INT, PRIMARY KEY)
- `username` (VARCHAR(255), UNIQUE)
- `email` (VARCHAR(255))
- `password` (VARCHAR(255), hashed)
- `created_at`, `updated_at` (TIMESTAMP)

### user_tokens
- `id` (INT, PRIMARY KEY)
- `user_id` (INT, FOREIGN KEY)
- `token` (VARCHAR(64), UNIQUE)
- `expires_at` (TIMESTAMP)
- `revoked` (TINYINT(1))
- `created_at` (TIMESTAMP)

### sms_messages
- `id` (INT, PRIMARY KEY)
- `phone_number` (VARCHAR(20))
- `message_text` (TEXT)
- `received_at` (TIMESTAMP)
- `goip_date` (VARCHAR(50))
- `synced_at` (TIMESTAMP)
- `is_read` (BOOLEAN)
- `created_at`, `updated_at` (TIMESTAMP)

### sms_outbox
- `id` (INT, PRIMARY KEY)
- `user_id` (INT, FOREIGN KEY)
- `phone_number` (VARCHAR(20))
- `message_text` (TEXT)
- `goip_line` (INT)
- `status` (ENUM: pending, sent, failed)
- `sent_at` (TIMESTAMP)
- `error_message` (TEXT)
- `created_at`, `updated_at` (TIMESTAMP)

### sms_batches
- `id` (INT, PRIMARY KEY)
- `user_id` (INT, FOREIGN KEY)
- `name` (VARCHAR(255))
- `total_recipients` (INT)
- `sent_count` (INT)
- `failed_count` (INT)
- `status` (ENUM: pending, processing, completed, failed)
- `created_at`, `updated_at` (TIMESTAMP)

### sms_batch_recipients
- `id` (INT, PRIMARY KEY)
- `batch_id` (INT, FOREIGN KEY)
- `phone_number` (VARCHAR(20))
- `status` (ENUM: pending, sent, failed)
- `error_message` (TEXT)
- `sent_at` (TIMESTAMP)
- `created_at`, `updated_at` (TIMESTAMP)

## Project Structure

```
sms-app/
├── app/
│   ├── api/                    # Backend PHP API
│   │   ├── index.php           # Entry point
│   │   ├── setup-db.php        # Database setup script
│   │   ├── app/
│   │   │   ├── autoload.php    # Custom autoloader
│   │   │   ├── bootstrap.php   # Bootstrap configuration
│   │   │   ├── Core/           # Core classes
│   │   │   │   ├── Router.php
│   │   │   │   ├── Database.php
│   │   │   │   ├── Auth.php
│   │   │   │   └── Controller.php
│   │   │   ├── Controllers/    # MVC Controllers
│   │   │   ├── Models/         # Database models
│   │   │   ├── Services/       # Business logic
│   │   │   └── Traits/         # GoIP trait
│   │   └── config/
│   │       └── database.php    # Database configuration
│   └── ui/                     # Frontend Vue.js app
│       ├── src/
│       │   ├── views/          # Vue pages
│       │   ├── components/      # Vue components
│       │   ├── services/        # API service
│       │   ├── stores/          # Pinia stores
│       │   └── router/         # Vue Router
│       └── .env                # Frontend environment
├── docker-compose.yml          # Docker configuration
├── setup-db.bat                # Windows setup script
├── setup-db.sh                 # Linux/Mac setup script
└── README.md                   # Project documentation
```

## Important Notes

1. **No Composer Required:** The project uses a custom autoloader, eliminating the need for Composer installation.

2. **Database Configuration Priority:**
   - `.env` file (highest priority)
   - `config/database.php` (fallback)
   - Default values (lowest priority)

3. **Token Management:** Expired tokens should be cleaned up periodically. Use `/api/cleanup-tokens` endpoint or set up a cron job.

4. **GoIP Integration:** The application communicates with GoIP devices via HTTP API. Ensure the GoIP device is accessible on the configured network.

5. **Security Considerations:**
   - Change default admin password immediately
   - Use strong passwords for database
   - Configure proper CORS settings for production
   - Implement rate limiting for production use
   - Use HTTPS in production

## Troubleshooting

### Database Connection Issues
- Verify MySQL container is running: `docker-compose ps`
- Check database credentials in `config/database.php` or `.env`
- Ensure database exists: Run `php setup-db.php`

### API Not Responding
- Check PHP is running: `php --version`
- Verify port 8000 is available
- Check error logs

### Frontend Issues
- Verify Node.js is installed: `node --version`
- Install dependencies: `npm install`
- Check browser console for errors
- Verify API URL in `.env` file

## Deployment Considerations

1. **Production Environment:**
   - Use proper web server (Apache/Nginx) instead of PHP built-in server
   - Configure SSL/HTTPS
   - Set up proper error logging
   - Configure database backups

2. **Performance:**
   - Implement caching for frequently accessed data
   - Optimize database queries
   - Consider queue system for SMS blasting

3. **Monitoring:**
   - Set up application monitoring
   - Monitor GoIP device connectivity
   - Track SMS delivery rates

## Support and Maintenance

- **Repository:** git@github.com:PA-Energy/sms-app.git
- **Documentation:** See `README.md` and `DOCUMENTATION.md`
- **Test Scripts:** `app/api/test-db-config.php` for database testing

## Next Steps

1. Review and understand the codebase structure
2. Set up development environment
3. Test all features with GoIP device
4. Configure production environment
5. Set up monitoring and logging
6. Plan for future enhancements

---

## Document Information

**Prepared by:**  
Lucky John Faderon  
Software Developer

**Date:** 2024

---

**End of Handover Prompt**
