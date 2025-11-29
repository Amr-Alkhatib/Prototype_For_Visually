# PICKS Cache Simulator - Backend Setup Guide

This is the Laravel 11 backend API for the PICKS Cache Simulator application. It provides authentication, user management, and simulation history storage.

## Features

- ✅ User Registration and Login (local credentials)
- ✅ TUM Online Integration (optional verification)
- ✅ JWT Token Authentication (Laravel Sanctum)
- ✅ Simulation History Storage and Retrieval
- ✅ RESTful API with pagination and filtering
- ✅ CORS configured for frontend communication
- ✅ Database migrations for users and simulations

## Prerequisites

- PHP 8.1 or higher
- Composer
- MySQL 8.0 or higher (or SQLite for development)
- Node.js and npm (for frontend integration)

## Installation

### 1. Initial Setup

```bash
# Navigate to backend directory
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Database Configuration

Edit `.env` file and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=picks_simulator
DB_USERNAME=root
DB_PASSWORD=your_password
```

For SQLite (development), use:
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 3. Create Database and Run Migrations

```bash
# Create database (MySQL)
mysql -u root -p -e "CREATE DATABASE picks_simulator;"

# Or for SQLite, migrations will create automatically

# Run migrations
php artisan migrate
```

### 4. Configure CORS and Services

Edit `.env` and set:

```env
# Frontend URL for CORS
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

# TUM Online API (optional)
TUM_ONLINE_API_URL=https://campus.tum.de/tumonline/rest/v1
TUM_ONLINE_CLIENT_ID=your_client_id
TUM_ONLINE_CLIENT_SECRET=your_client_secret
```

### 5. Start the Development Server

```bash
# Start Laravel development server
php artisan serve

# Server will run at http://localhost:8000
```

## API Endpoints

### Authentication

#### Register
```
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "tum_id": "ab12cdef" (optional)
}
```

**Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "tum_id": null,
    "is_tum_verified": false
  },
  "token": "1|abc..."
}
```

#### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "Password123!"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {...},
  "token": "1|abc..."
}
```

#### Logout
```
POST /api/auth/logout
Authorization: Bearer {token}
```

#### Get Current User
```
GET /api/auth/me
Authorization: Bearer {token}
```

#### Verify with TUM Online
```
POST /api/auth/verify-tum
Authorization: Bearer {token}
Content-Type: application/json

{
  "tum_id": "ab12cdef",
  "password": "tum_password"
}
```

### Simulations

#### Get All User Simulations
```
GET /api/simulations?per_page=15&sort_by=created_at&order=desc
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "My First Simulation",
      "description": "Testing cache behavior",
      "configuration": {
        "l1Size": 32,
        "l2Size": 256,
        "blockSize": 64
      },
      "results": {...},
      "notes": "Some notes",
      "created_at": "2024-11-29T10:00:00Z",
      "updated_at": "2024-11-29T10:00:00Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### Save New Simulation
```
POST /api/simulations
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Cache Test Run",
  "description": "Testing L1 and L2 cache",
  "configuration": {
    "l1Size": 32,
    "l2Size": 256,
    "blockSize": 64,
    "l1Associativity": 8,
    "l2Associativity": 8
  },
  "results": {
    "hits": 1500,
    "misses": 45,
    "hitRate": 0.97
  },
  "notes": "Good performance with this config"
}
```

**Response (201):**
```json
{
  "message": "Simulation saved successfully",
  "simulation": {...}
}
```

#### Get Specific Simulation
```
GET /api/simulations/{id}
Authorization: Bearer {token}
```

#### Update Simulation
```
PUT /api/simulations/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated name",
  "notes": "Updated notes",
  "results": {...}
}
```

#### Delete Simulation
```
DELETE /api/simulations/{id}
Authorization: Bearer {token}
```

#### Bulk Delete Simulations
```
POST /api/simulations/bulk-delete
Authorization: Bearer {token}
Content-Type: application/json

{
  "ids": [1, 2, 3]
}
```

## Frontend Integration

### Install Frontend Dependencies

In the frontend directory, install axios:

```bash
npm install axios
```

### Create API Client

Create `src/api/client.js`:

```javascript
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

const client = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
  },
  withCredentials: true,
});

// Add token to requests
client.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default client;
```

### Update Frontend Environment

Create `.env` in frontend:

```
VITE_API_URL=http://localhost:8000/api
```

### Connect Components

Update your LoginOverlay component to use the API.

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    tum_id VARCHAR(255) UNIQUE NULL,
    is_tum_verified BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX(email),
    INDEX(tum_id)
);
```

### Simulations Table
```sql
CREATE TABLE simulations (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    name VARCHAR(255) NULL,
    description TEXT NULL,
    configuration LONGTEXT NOT NULL,
    results LONGTEXT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX(user_id),
    INDEX(created_at),
    INDEX(user_id, created_at)
);
```

## Development Tools

### Create Seeders (optional)

Create test data:

```bash
php artisan tinker
```

Then in Tinker:
```php
User::factory(5)->create();
Simulation::factory(10)->create();
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Deployment

### Production Build

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set production environment
APP_ENV=production
APP_DEBUG=false
```

### Server Requirements

- PHP 8.1+
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension

## Troubleshooting

### CORS Issues

If you get CORS errors:

1. Check `.env` has correct `CORS_ALLOWED_ORIGINS`
2. Ensure `config/cors.php` is properly configured
3. Verify frontend URL matches exactly (including port)

### Database Connection Errors

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Token Issues

Tokens are stored in `personal_access_tokens` table. To clear:

```bash
php artisan tinker
>>> DB::table('personal_access_tokens')->truncate();
```

## Support & Documentation

- Laravel Documentation: https://laravel.com/docs
- Laravel Sanctum: https://laravel.com/docs/11.x/sanctum
- API Documentation: See endpoints section above

## License

MIT License
