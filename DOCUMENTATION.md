# SMS Application - Complete Documentation

## Table of Contents

1. [Introduction](#introduction)
2. [Architecture](#architecture)
3. [Installation & Setup](#installation--setup)
4. [Configuration](#configuration)
5. [API Reference](#api-reference)
6. [Database Schema](#database-schema)
7. [Frontend Guide](#frontend-guide)
8. [GoIP Integration](#goip-integration)
9. [Authentication](#authentication)
10. [Troubleshooting](#troubleshooting)
11. [Development Guide](#development-guide)

---

## Introduction

The SMS Application is a cross-platform SMS management system designed to work with GoIP devices. It provides a web-based interface for sending and receiving SMS messages, managing inbox, and performing bulk SMS operations.

### Key Features

- **SMS Inbox Management**: Sync and manage SMS messages from GoIP devices
- **SMS Sending**: Send individual SMS messages via GoIP
- **SMS Blasting**: Bulk SMS sending with CSV upload or manual entry
- **User Authentication**: Secure token-based authentication
- **Mobile-Responsive UI**: Modern, mobile-first design with Tailwind CSS

### Technology Stack

- **Backend**: PHP 8.1+ (Custom MVC, no framework dependencies)
- **Frontend**: Vue.js 3 + TypeScript + Tailwind CSS
- **Database**: MySQL 8.0 (Docker containerized)
- **Build Tool**: Vite
- **State Management**: Pinia

---

## Architecture

### Backend Architecture

The backend follows a custom MVC pattern without external framework dependencies:

```
app/api/
├── index.php              # Entry point, route definitions
├── app/
│   ├── autoload.php      # Custom PSR-4 autoloader
│   ├── bootstrap.php     # Configuration and initialization
│   ├── Core/             # Core framework classes
│   │   ├── Router.php    # Custom routing system
│   │   ├── Database.php  # PDO database singleton
│   │   ├── Auth.php      # Authentication system
│   │   └── Controller.php # Base controller
│   ├── Controllers/      # Request handlers
│   ├── Models/           # Data models
│   ├── Services/         # Business logic
│   └── Traits/           # Reusable traits (GoIP)
└── config/
    └── database.php      # Database configuration
```

### Frontend Architecture

The frontend is a Vue.js Single Page Application (SPA):

```
app/ui/
├── src/
│   ├── views/            # Page components
│   │   ├── Login.vue
│   │   ├── Dashboard.vue
│   │   ├── Inbox.vue
│   │   ├── Compose.vue
│   │   ├── Blast.vue
│   │   └── BlastHistory.vue
│   ├── components/       # Reusable components
│   │   └── Layout.vue    # Main layout with navigation
│   ├── services/         # API communication
│   │   └── api.ts        # Axios-based API service
│   ├── stores/           # State management
│   │   └── auth.ts       # Authentication store
│   └── router/           # Route definitions
│       └── index.ts
└── .env                  # Environment variables
```

### Request Flow

1. **Frontend Request** → Vue component calls API service
2. **API Service** → Axios sends HTTP request to backend
3. **Backend Router** → Routes request to appropriate controller
4. **Controller** → Processes request, uses models/services
5. **Response** → JSON response sent back to frontend
6. **Frontend Update** → Vue component updates UI

---

## Installation & Setup

### Prerequisites

- **Docker & Docker Compose** (for MySQL database)
- **PHP 8.1+** (for backend API)
- **Node.js 18+** and **npm** (for frontend)
- **GoIP Device** (for SMS functionality)

### Step 1: Clone Repository

```bash
git clone git@github.com:PA-Energy/sms-app.git
cd sms-app
```

### Step 2: Start MySQL Database

```bash
docker-compose up -d mysql
```

This starts a MySQL 8.0 container with:
- Database: `sms_app`
- Root password: `sms_app_root_password` (configurable)
- Port: `3306`

### Step 3: Setup Database

**Windows:**
```bash
setup-db.bat
```

**Linux/Mac/Git Bash:**
```bash
chmod +x setup-db.sh
./setup-db.sh
```

**Manual:**
```bash
php app/api/setup-db.php
```

This script:
- Creates the database (if not exists)
- Creates all required tables
- Creates default admin user (username: `admin`, password: `admin123`)

### Step 4: Configure Backend

Edit `app/api/config/database.php` or create `app/api/.env`:

```php
// In config/database.php
'mysql' => [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'sms_app'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
],
```

Or create `app/api/.env`:
```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sms_app
DB_USERNAME=root
DB_PASSWORD=your_password

TOKEN_EXPIRY_HOURS=24

GOIP_ADDR=192.168.1.3
GOIP_USER=admin
GOIP_PASSWORD=admin
GOIP_LINE=1
```

### Step 5: Start Backend API

```bash
cd app/api
php -S localhost:8000 -t .
```

Or use the batch file:
```bash
cd app/api
start-api.bat
```

The API will be available at `http://localhost:8000`

### Step 6: Configure Frontend

Create `app/ui/.env`:
```env
VITE_API_URL=http://localhost:8000/api
```

### Step 7: Install Frontend Dependencies

```bash
cd app/ui
npm install
```

### Step 8: Start Frontend Development Server

```bash
cd app/ui
npm run dev
```

Or use the batch file:
```bash
cd app/ui
start-ui.bat
```

The frontend will be available at `http://localhost:5173` (or another port if 5173 is busy)

---

## Configuration

### Backend Configuration

#### Database Configuration

**Priority Order:**
1. `.env` file (`app/api/.env`)
2. `config/database.php`
3. Default values

**Configuration File:** `app/api/config/database.php`

```php
'mysql' => [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'sms_app'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
],
```

#### GoIP Configuration

Configure in `config/database.php` or `.env`:

- `GOIP_ADDR`: GoIP device IP address (default: 192.168.1.3)
- `GOIP_USER`: GoIP username (default: admin)
- `GOIP_PASSWORD`: GoIP password (default: admin)
- `GOIP_LINE`: GoIP line number (default: 1)

#### Token Configuration

- `TOKEN_EXPIRY_HOURS`: Token expiration time in hours (default: 24)

### Frontend Configuration

**File:** `app/ui/.env`

```env
VITE_API_URL=http://localhost:8000/api
```

For production, update to your production API URL.

---

## API Reference

### Base URL

- Development: `http://localhost:8000/api`
- Production: Configure in frontend `.env`

### Authentication

All protected endpoints require a Bearer token in the Authorization header:

```
Authorization: Bearer <token>
```

### Endpoints

#### Authentication

##### POST /api/auth/login

Login and receive authentication token.

**Request:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "success": true,
  "token": "abc123...",
  "user": {
    "id": 1,
    "username": "admin",
    "email": "admin@smsapp.com"
  }
}
```

##### GET /api/auth/me

Get current authenticated user information.

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "username": "admin",
    "email": "admin@smsapp.com"
  }
}
```

##### POST /api/auth/logout

Logout and revoke current token.

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

#### SMS Inbox

##### GET /api/sms/inbox

Get list of inbox messages.

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page
- `unread_only` (optional): Filter unread messages

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "phone_number": "+1234567890",
      "message_text": "Hello",
      "received_at": "2024-01-01 12:00:00",
      "is_read": false
    }
  ],
  "total": 100
}
```

##### POST /api/sms/inbox/sync

Sync messages from GoIP device.

**Response:**
```json
{
  "success": true,
  "message": "Sync completed",
  "synced": 5,
  "new": 3
}
```

##### PUT /api/sms/inbox/{id}/read

Mark a message as read.

**Response:**
```json
{
  "success": true,
  "message": "Message marked as read"
}
```

##### PUT /api/sms/inbox/read-all

Mark all messages as read.

**Response:**
```json
{
  "success": true,
  "message": "All messages marked as read"
}
```

##### GET /api/sms/inbox/{id}

Get message details.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "phone_number": "+1234567890",
    "message_text": "Hello",
    "received_at": "2024-01-01 12:00:00",
    "is_read": false
  }
}
```

#### SMS Outbox

##### GET /api/sms/outbox

Get list of sent messages.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "phone_number": "+1234567890",
      "message_text": "Hello",
      "status": "sent",
      "sent_at": "2024-01-01 12:00:00"
    }
  ]
}
```

##### POST /api/sms/send

Send a single SMS message.

**Request:**
```json
{
  "phone_number": "+1234567890",
  "message_text": "Hello, this is a test message"
}
```

**Response:**
```json
{
  "success": true,
  "message": "SMS sent successfully",
  "data": {
    "id": 1,
    "status": "sent"
  }
}
```

##### GET /api/sms/outbox/{id}

Get sent message details.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "phone_number": "+1234567890",
    "message_text": "Hello",
    "status": "sent",
    "sent_at": "2024-01-01 12:00:00"
  }
}
```

#### SMS Blast

##### GET /api/sms/blast

Get list of SMS blast batches.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Marketing Campaign",
      "total_recipients": 100,
      "sent_count": 95,
      "failed_count": 5,
      "status": "completed"
    }
  ]
}
```

##### POST /api/sms/blast

Create a new SMS blast batch (manual entry).

**Request:**
```json
{
  "name": "Marketing Campaign",
  "message_text": "Hello from our campaign",
  "phone_numbers": ["+1234567890", "+0987654321"]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Batch created",
  "data": {
    "id": 1,
    "status": "pending"
  }
}
```

##### POST /api/sms/blast/upload-csv

Create a new SMS blast batch from CSV file.

**Request:** (multipart/form-data)
- `file`: CSV file with phone numbers
- `name`: Batch name
- `message_text`: SMS message text

**CSV Format:**
```csv
phone_number
+1234567890
+0987654321
```

**Response:**
```json
{
  "success": true,
  "message": "Batch created from CSV",
  "data": {
    "id": 1,
    "total_recipients": 100
  }
}
```

##### GET /api/sms/blast/{id}

Get batch details.

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Marketing Campaign",
    "total_recipients": 100,
    "sent_count": 95,
    "failed_count": 5,
    "status": "completed",
    "recipients": [...]
  }
}
```

##### GET /api/sms/blast/{id}/progress

Get batch progress.

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 100,
    "sent": 95,
    "failed": 5,
    "pending": 0,
    "progress_percent": 95
  }
}
```

#### Utility Endpoints

##### GET /api/health

Health check endpoint.

**Response:**
```json
{
  "status": "ok"
}
```

##### GET /api/cleanup-tokens

Cleanup expired authentication tokens.

**Response:**
```json
{
  "status": "ok",
  "message": "Expired tokens cleaned up"
}
```

---

## Database Schema

### users

User accounts table.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| username | VARCHAR(255) | Unique username |
| email | VARCHAR(255) | User email |
| password | VARCHAR(255) | Hashed password |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### user_tokens

Authentication tokens table.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| token | VARCHAR(64) | Unique token string |
| expires_at | TIMESTAMP | Token expiration |
| revoked | TINYINT(1) | Revocation flag |
| created_at | TIMESTAMP | Creation timestamp |

### sms_messages

Inbox messages table.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| phone_number | VARCHAR(20) | Sender phone number |
| message_text | TEXT | Message content |
| received_at | TIMESTAMP | Receive timestamp |
| goip_date | VARCHAR(50) | Original GoIP date |
| synced_at | TIMESTAMP | Sync timestamp |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### sms_outbox

Outgoing messages table.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| phone_number | VARCHAR(20) | Recipient phone number |
| message_text | TEXT | Message content |
| goip_line | INT | GoIP line number |
| status | ENUM | pending, sent, failed |
| sent_at | TIMESTAMP | Send timestamp |
| error_message | TEXT | Error details (if failed) |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### sms_batches

SMS blast batches table.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Foreign key to users |
| name | VARCHAR(255) | Batch name |
| total_recipients | INT | Total recipients |
| sent_count | INT | Successfully sent count |
| failed_count | INT | Failed count |
| status | ENUM | pending, processing, completed, failed |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

### sms_batch_recipients

Individual recipients in batches.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| batch_id | INT | Foreign key to sms_batches |
| phone_number | VARCHAR(20) | Recipient phone number |
| status | ENUM | pending, sent, failed |
| error_message | TEXT | Error details (if failed) |
| sent_at | TIMESTAMP | Send timestamp |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Update timestamp |

---

## Frontend Guide

### Project Structure

```
app/ui/src/
├── views/              # Page components
│   ├── Login.vue       # Login page
│   ├── Dashboard.vue   # Dashboard/home
│   ├── Inbox.vue       # SMS inbox
│   ├── Compose.vue     # Send single SMS
│   ├── Blast.vue       # SMS blasting
│   └── BlastHistory.vue # Blast history
├── components/         # Reusable components
│   └── Layout.vue      # Main layout
├── services/           # API services
│   └── api.ts          # Axios API client
├── stores/             # Pinia stores
│   └── auth.ts         # Auth state
└── router/             # Vue Router
    └── index.ts        # Route definitions
```

### API Service

The API service (`services/api.ts`) handles all HTTP requests:

```typescript
import api from '@/services/api'

// Login
const response = await api.login(username, password)

// Get inbox
const inbox = await api.getInbox()

// Send SMS
await api.sendSms(phoneNumber, message)
```

### State Management

Authentication state is managed with Pinia:

```typescript
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// Check if authenticated
if (authStore.isAuthenticated) {
  // User is logged in
}

// Get current user
const user = authStore.user
```

### Routing

Protected routes require authentication:

```typescript
{
  path: '/dashboard',
  name: 'Dashboard',
  component: () => import('../views/Dashboard.vue'),
  meta: { requiresAuth: true }
}
```

---

## GoIP Integration

### GoIP Device Communication

The application communicates with GoIP devices via HTTP API using the `GoIpTrait`.

**Location:** `app/api/app/Traits/GoIpTrait.php`

### Configuration

Configure GoIP settings in `config/database.php` or `.env`:

```php
GOIP_ADDR=192.168.1.3      # GoIP device IP
GOIP_USER=admin           # GoIP username
GOIP_PASSWORD=admin       # GoIP password
GOIP_LINE=1               # GoIP line number
```

### SMS Retrieval

The sync process:
1. Connects to GoIP device
2. Retrieves SMS inbox
3. Parses messages
4. Stores in database (with deduplication)

### SMS Sending

Sending process:
1. Validates phone number and message
2. Sends HTTP POST to GoIP device
3. Updates status in database
4. Handles errors and retries

---

## Authentication

### Token-Based Authentication

The application uses a simple token-based authentication system:

1. **Login**: User provides username/password
2. **Token Generation**: Server generates unique token
3. **Token Storage**: Token stored in `user_tokens` table
4. **Token Validation**: Each request validates token
5. **Token Expiry**: Tokens expire after configured hours (default: 24)

### Token Format

- **Type**: Random hex string (64 characters)
- **Storage**: Database table `user_tokens`
- **Expiry**: Configurable (default: 24 hours)
- **Revocation**: Tokens can be revoked on logout

### Security Considerations

- Tokens are stored securely in database
- Expired tokens are automatically invalidated
- Tokens can be manually revoked
- Password hashing using PHP's `password_hash()`

---

## Troubleshooting

### Database Connection Issues

**Problem:** Cannot connect to database

**Solutions:**
1. Verify MySQL container is running:
   ```bash
   docker-compose ps
   ```

2. Check database credentials in `config/database.php`

3. Test database connection:
   ```bash
   php app/api/test-db-config.php
   ```

4. Ensure database exists:
   ```bash
   php app/api/setup-db.php
   ```

### API Not Responding

**Problem:** API returns errors or doesn't respond

**Solutions:**
1. Check PHP is installed and running:
   ```bash
   php --version
   ```

2. Verify port 8000 is available

3. Check PHP error logs

4. Verify all files are in place (especially `app/autoload.php`)

### Frontend Issues

**Problem:** Frontend doesn't load or shows errors

**Solutions:**
1. Install dependencies:
   ```bash
   cd app/ui
   npm install
   ```

2. Check Node.js version:
   ```bash
   node --version  # Should be 18+
   ```

3. Verify API URL in `.env`:
   ```env
   VITE_API_URL=http://localhost:8000/api
   ```

4. Check browser console for errors

5. Clear browser cache

### GoIP Connection Issues

**Problem:** Cannot connect to GoIP device

**Solutions:**
1. Verify GoIP device is powered on and accessible

2. Check network connectivity:
   ```bash
   ping 192.168.1.3  # Replace with your GoIP IP
   ```

3. Verify GoIP credentials in configuration

4. Check GoIP device web interface is accessible

### Login Issues

**Problem:** Cannot login

**Solutions:**
1. Verify admin user exists:
   ```bash
   php app/api/setup-db.php
   ```

2. Default credentials:
   - Username: `admin`
   - Password: `admin123`

3. Check database connection

4. Verify token generation is working

---

## Development Guide

### Adding New API Endpoint

1. **Define Route** in `app/api/index.php`:
   ```php
   $router->get('/api/new-endpoint', 'NewController@method');
   ```

2. **Create Controller** in `app/api/app/Controllers/NewController.php`:
   ```php
   namespace App\Controllers;
   
   use App\Core\Controller;
   
   class NewController extends Controller
   {
       public function method()
       {
           return $this->json(['success' => true]);
       }
   }
   ```

3. **Add to Frontend API Service** in `app/ui/src/services/api.ts`

### Adding New Database Table

1. **Create Migration** or update `setup-db.php`:
   ```php
   $db->exec("
       CREATE TABLE IF NOT EXISTS new_table (
           id INT AUTO_INCREMENT PRIMARY KEY,
           ...
       ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ");
   ```

2. **Create Model** in `app/api/app/Models/NewModel.php`

3. **Update Setup Script** to include new table

### Frontend Development

1. **Create New View** in `app/ui/src/views/NewView.vue`

2. **Add Route** in `app/ui/src/router/index.ts`

3. **Update Navigation** in `app/ui/src/components/Layout.vue`

### Testing

**Database Test:**
```bash
php app/api/test-db-config.php
```

**API Test:**
Use tools like Postman or curl to test endpoints

**Frontend Test:**
Run development server and test in browser

---

## Additional Resources

- **Repository:** git@github.com:PA-Energy/sms-app.git
- **README:** See `README.md` for quick start guide
- **Handover:** See `HANDOVER.md` for handover prompt

---

## Document Information

**Prepared by:**  
Lucky John Faderon  
Software Developer

**Document Version:** 1.0  
**Last Updated:** 2024
