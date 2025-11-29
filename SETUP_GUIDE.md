# PICKS Cache Simulator - Full Stack Setup Guide

## Quick Start Overview

This project now has a complete full-stack implementation:
- **Frontend**: React with Vite (TypeScript support ready)
- **Backend**: Laravel 11 with REST API and authentication
- **Database**: MySQL with user and simulation storage
- **Authentication**: JWT tokens via Laravel Sanctum

## Prerequisites

### System Requirements
- **Node.js** 16+ (for frontend)
- **PHP** 8.1+ (for backend)
- **Composer** (PHP package manager)
- **MySQL** 8.0+ OR SQLite (for development)
- **Git** (optional)

### Windows Installation

#### Node.js
1. Download from https://nodejs.org (LTS version)
2. Run installer and follow steps

#### PHP
1. Download from https://www.php.net/downloads
2. Or use: https://windows.php.net/download/
3. Add PHP to System PATH

#### Composer
1. Download installer from https://getcomposer.org/download/
2. Run installer
3. Verify: `composer --version`

#### MySQL
1. Download from https://www.mysql.com/downloads/mysql/
2. Use MySQL Workbench for management
3. Create database: `picks_simulator`

---

## Setup Instructions

### Part 1: Frontend Setup

```bash
# Navigate to frontend directory
cd c:\Users\amral\OneDrive - TUM\Desktop\PICKS

# Install dependencies
npm install

# Create .env file for frontend
echo "VITE_API_URL=http://localhost:8000/api" > .env.local

# Start development server
npm run dev

# Frontend will be available at http://localhost:5173
```

### Part 2: Backend Setup

```bash
# Navigate to backend directory
cd backend

# Install dependencies
composer install

# Create .env file from example
copy .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env (see below)
```

#### Configure Database in `.env`

For **MySQL**:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=picks_simulator
DB_USERNAME=root
DB_PASSWORD=your_password
```

For **SQLite** (easier for development):
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

#### Run Migrations

```bash
# Create tables
php artisan migrate

# Or with fresh database
php artisan migrate:fresh
```

#### Start Backend Server

```bash
# Start development server
php artisan serve

# Backend will be available at http://localhost:8000
```

---

## Testing the Full Stack

### 1. Register a New User

**Frontend**: Navigate to http://localhost:5173
1. Click "Register" tab
2. Fill in credentials:
   - Name: "Test User"
   - Email: "test@example.com"
   - Password: "TestPassword123!"
   - Confirm: "TestPassword123!"
3. Click "Create Account"

**API Test (Postman/cURL)**:
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPassword123!",
    "password_confirmation": "TestPassword123!"
  }'
```

### 2. Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "TestPassword123!"
  }'
```

**Response**:
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "is_tum_verified": false
  },
  "token": "1|abc123xyz..."
}
```

### 3. Save a Simulation

```bash
curl -X POST http://localhost:8000/api/simulations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -d '{
    "name": "My First Simulation",
    "description": "Testing cache configuration",
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
    }
  }'
```

### 4. Retrieve Simulations

```bash
curl -X GET "http://localhost:8000/api/simulations?page=1&per_page=15" \
  -H "Authorization: Bearer 1|abc123xyz..."
```

---

## Key Features Implemented

### Frontend
- ✅ **History Button** - Added next to Theory button with matching design
- ✅ **History Page** - View, load, and delete past simulations
- ✅ **Authentication UI** - Updated Login/Register forms
- ✅ **API Integration** - Axios client with auto token injection

### Backend
- ✅ **User Management** - Registration, login, profile
- ✅ **TUM Integration** - Framework for TUM Online verification
- ✅ **Simulation CRUD** - Create, read, update, delete operations
- ✅ **Pagination** - List simulations with pagination
- ✅ **Authorization** - Protect endpoints, ensure users can only access their data
- ✅ **CORS** - Allow frontend-backend communication

### Database
- ✅ **Users Table** - Secure password storage, TUM verification tracking
- ✅ **Simulations Table** - Full simulation history with timestamps
- ✅ **Indexes** - Optimized queries for performance

---

## Important File Locations

### Frontend
- `src/components/Auth/LoginOverlay.jsx` - Authentication form
- `src/components/Pages/HistoryPage.jsx` - Simulation history display
- `src/components/Layout/Header.jsx` - Navigation with History button
- `src/api/client.js` - API client setup
- `src/api/endpoints.js` - API endpoint functions

### Backend
- `app/Models/User.php` - User model with relationships
- `app/Models/Simulation.php` - Simulation model
- `app/Http/Controllers/Api/AuthController.php` - Auth endpoints
- `app/Http/Controllers/Api/SimulationController.php` - Simulation endpoints
- `routes/api.php` - All API routes
- `database/migrations/` - Database structure
- `config/` - Configuration files

---

## Common Issues & Fixes

### Issue: CORS Error
**Error**: `Access to XMLHttpRequest at 'http://localhost:8000/api/auth/login' has been blocked by CORS policy`

**Solution**:
1. Check `.env` has: `CORS_ALLOWED_ORIGINS=http://localhost:5173`
2. Restart Laravel server
3. Clear browser cache

### Issue: Database Connection Failed
**Error**: `SQLSTATE[HY000] [2002] No connection could be made`

**Solution**:
1. Verify MySQL is running
2. Check DB credentials in `.env`
3. Create database: `CREATE DATABASE picks_simulator;`
4. Run migrations: `php artisan migrate`

### Issue: "Migrate command not found"
**Solution**:
```bash
composer install
php artisan migrate
```

### Issue: Node modules not found
**Solution**:
```bash
npm install
npm run dev
```

---

## Environment Variables

### Frontend (.env.local)
```env
VITE_API_URL=http://localhost:8000/api
```

### Backend (.env)
```env
APP_NAME="PICKS Cache Simulator"
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=picks_simulator
DB_USERNAME=root
DB_PASSWORD=

CORS_ALLOWED_ORIGINS=http://localhost:5173,http://localhost:3000

TUM_ONLINE_API_URL=https://campus.tum.de/tumonline/rest/v1
TUM_ONLINE_CLIENT_ID=your_client_id
TUM_ONLINE_CLIENT_SECRET=your_client_secret
```

---

## Development Workflow

### Daily Development

1. **Start Backend**:
```bash
cd backend
php artisan serve
```

2. **Start Frontend** (new terminal):
```bash
npm run dev
```

3. **Access Application**:
   - Frontend: http://localhost:5173
   - API: http://localhost:8000/api

### Make Code Changes

- Frontend changes auto-reload (Vite hot reload)
- Backend changes require server restart

### Database Changes

If you need to modify database structure:

```bash
# Create new migration
php artisan make:migration add_new_column_to_simulations

# Edit migration file in database/migrations/

# Run migration
php artisan migrate

# Rollback if needed
php artisan migrate:rollback
```

---

## Next Steps

### Future Enhancements

1. **Real TUM Online Integration**
   - Implement OAuth with TUM Campus
   - Auto-populate user from TUM credentials

2. **Advanced Features**
   - Simulation comparison
   - Export results to PDF
   - Shared simulations

3. **Performance**
   - Add Redis caching
   - Optimize database queries
   - CDN for static assets

4. **Deployment**
   - Docker containerization
   - AWS/Azure deployment
   - CI/CD pipeline

---

## Support

For issues or questions:
1. Check the README.md files in backend/ and frontend/
2. Review Laravel documentation: https://laravel.com/docs
3. Check React documentation: https://react.dev

---

## Summary

You now have:
✅ Full authentication system (registration, login, logout)
✅ User data isolation (each user sees only their simulations)
✅ Simulation history storage in database
✅ History page with load/delete functionality
✅ Proper API error handling
✅ CORS configuration for frontend-backend communication

Start both servers and enjoy! 🚀
