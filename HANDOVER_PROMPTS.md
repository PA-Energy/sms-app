# SMS Application - Handover Prompts for Google Docs Gemini

## Instructions
Copy each prompt below and paste it into Google Docs, then use Gemini Docs Assistant to generate that section. Work through them in order to build the complete handover document.

---

## PROMPT 1: Project Overview & Introduction

**Copy this prompt to Google Docs Gemini:**

```
Create a professional project handover document section for "SMS Application with GoIP Integration". 

Include:
- Project Name: SMS Application with GoIP Integration
- Repository: git@github.com:PA-Energy/sms-app.git
- Technology Stack: PHP 8.1+, Vue.js 3, TypeScript, Tailwind CSS, MySQL 8.0
- Architecture: Custom PHP MVC API + Vue.js SPA
- Database: MySQL (Docker containerized)
- Authentication: Token-based session management

Write an executive summary explaining this is a cross-platform SMS management system that integrates with GoIP devices for sending and receiving SMS messages. The system provides a web-based interface for managing SMS inbox, sending individual messages, and performing bulk SMS operations.

Format as a professional document with clear headings and bullet points.
```

---

## PROMPT 2: System Architecture

**Copy this prompt to Google Docs Gemini:**

```
Continue the handover document with a detailed System Architecture section.

Backend (PHP MVC API):
- Location: /app/api
- Framework: Custom lightweight PHP MVC (no external framework dependencies)
- Entry Point: index.php
- Core Components: Custom Router, Database Singleton, Authentication System, Base Controller, Custom Autoloader (no Composer required)

Frontend (Vue.js SPA):
- Location: /app/ui
- Framework: Vue.js 3 with TypeScript
- Build Tool: Vite
- Styling: Tailwind CSS
- State Management: Pinia
- Routing: Vue Router

Database:
- Type: MySQL 8.0
- Container: Docker Compose
- Database Name: sms_app

Include a diagram description or clear structure showing how these components interact.
```

---

## PROMPT 3: Database Schema & Tables

**Copy this prompt to Google Docs Gemini:**

```
Add a Database Schema section to the handover document.

List all database tables with their purpose:

1. users - User accounts table
   - Fields: id, username (unique), email, password (hashed), created_at, updated_at

2. user_tokens - Authentication tokens table
   - Fields: id, user_id (foreign key), token (unique), expires_at, revoked, created_at

3. sms_messages - Inbox messages (synced from GoIP)
   - Fields: id, phone_number, message_text, received_at, goip_date, synced_at, is_read, created_at, updated_at

4. sms_outbox - Outgoing messages
   - Fields: id, user_id, phone_number, message_text, goip_line, status (pending/sent/failed), sent_at, error_message, created_at, updated_at

5. sms_batches - SMS blast batches
   - Fields: id, user_id, name, total_recipients, sent_count, failed_count, status (pending/processing/completed/failed), created_at, updated_at

6. sms_batch_recipients - Individual recipients in batches
   - Fields: id, batch_id, phone_number, status (pending/sent/failed), error_message, sent_at, created_at, updated_at

Format as a clear table or structured list with field descriptions.
```

---

## PROMPT 4: Key Features

**Copy this prompt to Google Docs Gemini:**

```
Add a Key Features section describing the main functionality:

1. SMS Inbox Synchronization
   - Manual sync from GoIP device
   - Automatic deduplication
   - Read/unread status tracking

2. SMS Sending
   - Single SMS via GoIP device
   - Status tracking (pending/sent/failed)
   - Error handling and logging

3. SMS Blasting
   - CSV upload support
   - Manual entry (one phone per line)
   - Batch processing with progress tracking
   - Individual recipient status

4. User Authentication
   - Token-based session management
   - 24-hour token expiry (configurable)
   - Protected API routes

Format each feature with a brief description and key capabilities.
```

---

## PROMPT 5: Setup Instructions

**Copy this prompt to Google Docs Gemini:**

```
Add a detailed Setup Instructions section with step-by-step guide:

Prerequisites:
- Docker and Docker Compose
- PHP 8.1+ (for API)
- Node.js 18+ and npm (for frontend)

Step 1: Start MySQL Database
- Command: docker-compose up -d mysql

Step 2: Setup Database
- Windows: setup-db.bat
- Linux/Mac: ./setup-db.sh
- Manual: php app/api/setup-db.php

Step 3: Configure Backend
- Edit app/api/config/database.php or create app/api/.env
- Set database credentials

Step 4: Start Backend API
- Command: cd app/api && php -S localhost:8000 -t .

Step 5: Configure Frontend
- Create app/ui/.env with VITE_API_URL=http://localhost:8000/api

Step 6: Install Frontend Dependencies
- Command: cd app/ui && npm install

Step 7: Start Frontend
- Command: cd app/ui && npm run dev

Include default credentials: Username: admin, Password: admin123
Add warning to change default password immediately.
```

---

## PROMPT 6: Configuration Details

**Copy this prompt to Google Docs Gemini:**

```
Add a Configuration section covering:

Backend Configuration:
- Environment file: app/api/.env (optional, uses config/database.php as fallback)
- Database Config: app/api/config/database.php
- Configuration priority: .env file > config/database.php > default values

Frontend Configuration:
- Environment file: app/ui/.env
- Required variable: VITE_API_URL=http://localhost:8000/api

GoIP Configuration:
- GOIP_ADDR - GoIP device IP address (default: 192.168.1.3)
- GOIP_USER - GoIP username (default: admin)
- GOIP_PASSWORD - GoIP password (default: admin)
- GOIP_LINE - GoIP line number (default: 1)

Token Configuration:
- TOKEN_EXPIRY_HOURS - Token expiration time in hours (default: 24)

Include example configuration snippets and explain where each setting is used.
```

---

## PROMPT 7: API Endpoints Reference

**Copy this prompt to Google Docs Gemini:**

```
Add an API Endpoints Reference section listing all available endpoints:

Authentication:
- POST /api/auth/login - User login
- GET /api/auth/me - Get current user info
- POST /api/auth/logout - Logout

SMS Inbox:
- GET /api/sms/inbox - List inbox messages
- POST /api/sms/inbox/sync - Sync messages from GoIP
- PUT /api/sms/inbox/{id}/read - Mark message as read
- PUT /api/sms/inbox/read-all - Mark all as read
- GET /api/sms/inbox/{id} - Get message details

SMS Outbox:
- GET /api/sms/outbox - List sent messages
- POST /api/sms/send - Send single SMS
- GET /api/sms/outbox/{id} - Get message details

SMS Blast:
- GET /api/sms/blast - List blast batches
- POST /api/sms/blast - Create batch (manual entry)
- POST /api/sms/blast/upload-csv - Create batch (CSV upload)
- GET /api/sms/blast/{id} - Get batch details
- GET /api/sms/blast/{id}/progress - Get batch progress

Utility:
- GET /api/health - Health check
- GET /api/cleanup-tokens - Cleanup expired tokens

Note: All protected endpoints require Bearer token in Authorization header.
```

---

## PROMPT 8: Project Structure

**Copy this prompt to Google Docs Gemini:**

```
Add a Project Structure section showing the directory layout:

sms-app/
├── app/
│   ├── api/                    # Backend PHP API
│   │   ├── index.php           # Entry point
│   │   ├── setup-db.php        # Database setup script
│   │   ├── app/
│   │   │   ├── autoload.php    # Custom autoloader
│   │   │   ├── bootstrap.php   # Bootstrap configuration
│   │   │   ├── Core/           # Core classes (Router, Database, Auth, Controller)
│   │   │   ├── Controllers/    # MVC Controllers
│   │   │   ├── Models/         # Database models
│   │   │   ├── Services/       # Business logic
│   │   │   └── Traits/         # GoIP trait
│   │   └── config/
│   │       └── database.php    # Database configuration
│   └── ui/                     # Frontend Vue.js app
│       ├── src/
│       │   ├── views/          # Vue pages
│       │   ├── components/     # Vue components
│       │   ├── services/       # API service
│       │   ├── stores/         # Pinia stores
│       │   └── router/         # Vue Router
│       └── .env                # Frontend environment
├── docker-compose.yml          # Docker configuration
├── setup-db.bat                # Windows setup script
├── setup-db.sh                 # Linux/Mac setup script
└── README.md                   # Project documentation

Format as a clear tree structure with descriptions for key directories.
```

---

## PROMPT 9: Important Notes & Security

**Copy this prompt to Google Docs Gemini:**

```
Add an Important Notes & Security Considerations section:

Important Notes:
1. No Composer Required - The project uses a custom autoloader, eliminating the need for Composer installation.
2. Database Configuration Priority - .env file (highest) > config/database.php (fallback) > default values
3. Token Management - Expired tokens should be cleaned up periodically using /api/cleanup-tokens endpoint or cron job
4. GoIP Integration - Application communicates with GoIP devices via HTTP API. Ensure GoIP device is accessible on configured network.

Security Considerations:
- Change default admin password immediately after first login
- Use strong passwords for database
- Configure proper CORS settings for production
- Implement rate limiting for production use
- Use HTTPS in production
- Regularly update dependencies
- Monitor token expiration and cleanup

Format as a clear list with warnings and best practices.
```

---

## PROMPT 10: Troubleshooting Guide

**Copy this prompt to Google Docs Gemini:**

```
Add a Troubleshooting section with common issues and solutions:

Database Connection Issues:
- Verify MySQL container is running: docker-compose ps
- Check database credentials in config/database.php or .env
- Ensure database exists: Run php setup-db.php

API Not Responding:
- Check PHP is running: php --version
- Verify port 8000 is available
- Check error logs

Frontend Issues:
- Verify Node.js is installed: node --version
- Install dependencies: npm install
- Check browser console for errors
- Verify API URL in .env file

GoIP Connection Issues:
- Verify GoIP device is powered on and accessible
- Check network connectivity: ping [GoIP_IP]
- Verify GoIP credentials in configuration
- Check GoIP device web interface is accessible

Login Issues:
- Verify admin user exists: Run php setup-db.php
- Default credentials: Username: admin, Password: admin123
- Check database connection
- Verify token generation is working

Format each issue with problem description and step-by-step solutions.
```

---

## PROMPT 11: Deployment & Maintenance

**Copy this prompt to Google Docs Gemini:**

```
Add a Deployment & Maintenance section:

Production Environment:
- Use proper web server (Apache/Nginx) instead of PHP built-in server
- Configure SSL/HTTPS
- Set up proper error logging
- Configure database backups
- Use environment-specific configuration files

Performance Optimization:
- Implement caching for frequently accessed data
- Optimize database queries
- Consider queue system for SMS blasting
- Monitor resource usage

Monitoring:
- Set up application monitoring
- Monitor GoIP device connectivity
- Track SMS delivery rates
- Set up alerts for failures

Maintenance Tasks:
- Regular database backups
- Cleanup expired tokens (automated via cron)
- Monitor disk space
- Update dependencies regularly
- Review error logs

Include recommendations for production deployment and ongoing maintenance.
```

---

## PROMPT 12: Next Steps & Support

**Copy this prompt to Google Docs Gemini:**

```
Add a Next Steps & Support section:

Next Steps for New Team:
1. Review and understand the codebase structure
2. Set up development environment
3. Test all features with GoIP device
4. Configure production environment
5. Set up monitoring and logging
6. Plan for future enhancements

Support Resources:
- Repository: git@github.com:PA-Energy/sms-app.git
- Documentation: See README.md and DOCUMENTATION.md
- Test Scripts: app/api/test-db-config.php for database testing

Contact Information:
- Prepared by: Lucky John Faderon, Software Developer
- Date: 2024

Include a checklist for onboarding and links to additional resources.
```

---

## Usage Instructions

1. Open Google Docs
2. Start with PROMPT 1 and work through them sequentially
3. For each prompt:
   - Copy the prompt text (the content after "Copy this prompt to Google Docs Gemini:")
   - Paste into Google Docs
   - Use Gemini Docs Assistant to generate the section
   - Review and format as needed
4. Combine all sections into one complete handover document
5. Add table of contents and page numbers
6. Finalize formatting and styling

---

**Note:** Each prompt is designed to be under 500 words/characters to work within Gemini's limits. Adjust as needed based on your specific Gemini assistant's requirements.
