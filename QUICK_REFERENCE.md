# 🚀 PICKS Cache Simulator - Quick Reference Card

## 📍 File Locations

### Frontend Key Files
```
src/api/client.js                    → Axios HTTP client setup
src/api/endpoints.js                 → API endpoint functions
src/components/Auth/LoginOverlay.jsx → Login/Register UI
src/components/Pages/HistoryPage.jsx → Simulation history & management
src/components/Layout/Header.jsx     → Navigation bar with History button
src/App.jsx                          → Main app with routes
.env.local                           → Frontend environment config
package.json                         → Dependencies (includes axios)
```

### Backend Key Files
```
backend/app/Models/User.php                    → User model
backend/app/Models/Simulation.php              → Simulation model
backend/app/Http/Controllers/Api/AuthController.php → Login/Register endpoints
backend/app/Http/Controllers/Api/SimulationController.php → Simulation CRUD
backend/routes/api.php                        → All API routes
backend/database/migrations/                  → Database schema
backend/config/database.php                   → Database config
backend/config/cors.php                       → CORS settings
backend/.env.example                          → Environment template
backend/composer.json                         → PHP dependencies
```

### Documentation
```
README.md              → Project overview
SETUP_GUIDE.md        → Installation instructions
ARCHITECTURE.md       → System design
API_TESTING_GUIDE.md  → API endpoint examples
IMPLEMENTATION_SUMMARY.md → What was built
start-dev.bat         → Quick start script (Windows)
```

---

## 🎯 Quick Commands

### Frontend
```bash
# Install dependencies
npm install

# Start dev server (http://localhost:5173)
npm run dev

# Build for production
npm build

# Preview production build
npm preview
```

### Backend
```bash
# Navigate to backend
cd backend

# Install dependencies
composer install

# Create environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Run migrations (create tables)
php artisan migrate

# Start dev server (http://localhost:8000)
php artisan serve

# Rollback migrations
php artisan migrate:rollback

# Fresh migrations (clear + migrate)
php artisan migrate:fresh
```

### Quick Start (Windows)
```bash
# One-click setup
start-dev.bat
```

---

## 🔗 API Endpoints Summary

### Authentication (Public)
```
POST   /api/auth/register        Create new user
POST   /api/auth/login           Login user (returns token)
```

### Authentication (Protected)
```
GET    /api/auth/me              Get current user
POST   /api/auth/logout          Logout user
POST   /api/auth/verify-tum      Verify with TUM Online
```

### Simulations (Protected)
```
GET    /api/simulations          Get user's simulations (paginated)
POST   /api/simulations          Create new simulation
GET    /api/simulations/{id}     Get specific simulation
PUT    /api/simulations/{id}     Update simulation
DELETE /api/simulations/{id}     Delete simulation
POST   /api/simulations/bulk-delete   Delete multiple
```

---

## 🔐 Authentication

### Token Flow
```
Register/Login → Get Token → Store in localStorage
↓
Axios interceptor adds: Authorization: Bearer {token}
↓
Backend validates token → Grant access or reject
```

### Store Token
```javascript
localStorage.setItem('auth_token', token);
localStorage.setItem('user', JSON.stringify(userData));
```

### Use Token
```javascript
// Automatically added by axios interceptor
// No manual header needed!
const response = await simulationAPI.getAll();
```

---

## 💾 Key Data Models

### User
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "tum_id": "ab12cdef",
  "is_tum_verified": false,
  "created_at": "2024-11-29T10:00:00Z"
}
```

### Simulation
```json
{
  "id": 1,
  "name": "Cache Test",
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
  "created_at": "2024-11-29T10:00:00Z"
}
```

---

## 🌐 URLs

```
Frontend:     http://localhost:5173
Backend:      http://localhost:8000
API:          http://localhost:8000/api
Database:     (Local MySQL or SQLite)
```

---

## 📝 Environment Files

### Frontend (.env.local)
```env
VITE_API_URL=http://localhost:8000/api
```

### Backend (.env)
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=picks_simulator
DB_USERNAME=root
DB_PASSWORD=
CORS_ALLOWED_ORIGINS=http://localhost:5173
```

---

## 🚀 Startup Sequence

### Option 1: Automatic (Windows)
```
1. Run start-dev.bat
2. Wait for servers to start
3. Open http://localhost:5173
```

### Option 2: Manual
```
Terminal 1:
cd backend
php artisan serve

Terminal 2:
npm run dev

Browser:
http://localhost:5173
```

---

## ✅ Testing Workflow

```
1. Register: Create new account
2. Login: Enter credentials
3. Create: Run simulator and save simulation
4. History: Click History button
5. View: See saved simulations
6. Load: Click Load to rerun
7. Delete: Remove simulation
8. Logout: Verify token cleared
9. Relogin: Check history persists
```

---

## 🔧 Common Operations

### Create Simulation via API
```bash
curl -X POST http://localhost:8000/api/simulations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "name": "Test",
    "configuration": {"l1Size": 32, "l2Size": 256}
  }'
```

### Get All Simulations
```bash
curl -X GET http://localhost:8000/api/simulations \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Run Migrations
```bash
cd backend
php artisan migrate
```

### Clear Cache
```bash
cd backend
php artisan cache:clear
php artisan config:clear
```

---

## 📊 Response Codes

| Code | Meaning | Action |
|------|---------|--------|
| 200 | Success | Continue |
| 201 | Created | Resource created |
| 400 | Bad Request | Check data format |
| 401 | Unauthorized | Login/get token |
| 404 | Not Found | Check ID |
| 422 | Validation Error | Fix input data |
| 500 | Server Error | Check backend logs |

---

## 🐛 Troubleshooting

### CORS Error
→ Check `.env` CORS_ALLOWED_ORIGINS
→ Restart backend

### Database Error
→ Check DB credentials in `.env`
→ Create database: `CREATE DATABASE picks_simulator;`
→ Run migrations: `php artisan migrate`

### Frontend Won't Load
→ Check port 5173 available
→ Run `npm install`
→ Restart: `npm run dev`

### Backend Won't Start
→ Check port 8000 available
→ Run `composer install`
→ Check PHP version: `php --version`

---

## 📚 Documentation Map

```
Need setup help?
→ SETUP_GUIDE.md

Need architecture info?
→ ARCHITECTURE.md

Need API examples?
→ API_TESTING_GUIDE.md

Need project overview?
→ README.md

Need backend docs?
→ backend/README.md

Need implementation details?
→ IMPLEMENTATION_SUMMARY.md
```

---

## 💡 Pro Tips

1. **Use Environment Files**
   - Frontend: `.env.local`
   - Backend: `.env`
   - Never commit `.env` to git

2. **Token Management**
   - Tokens auto-stored in localStorage
   - Auto-injected in API requests
   - Expires on logout

3. **Database**
   - Use SQLite for development
   - Use MySQL for production
   - Migrations handle schema changes

4. **API Testing**
   - Use Postman or cURL
   - Always include Authorization header
   - Check response status codes

5. **Development**
   - Frontend auto-reloads with Vite
   - Backend requires restart for code changes
   - Check browser console for frontend errors
   - Check `storage/logs/laravel.log` for backend errors

---

## 🎯 Typical User Journey

```
1. User visits http://localhost:5173
2. Sees login overlay
3. Clicks "Register" tab
4. Fills form: name, email, password
5. Account created, auto-logged in
6. Redirected to simulator
7. Runs simulation
8. Clicks History button
9. Simulation auto-saved via API
10. Can load/delete from history
11. Clicks Logout
12. Session cleared
```

---

## 📱 Component Tree

```
App
├── Header (with History button)
├── Routes
│   ├── SystemDiagram (/)
│   ├── CPUPage (/cpu)
│   ├── L1Page (/l1)
│   ├── RAMPage (/ram)
│   ├── TheoryPage (/theory)
│   └── HistoryPage (/history) ← NEW
├── Chatbot
└── LoginOverlay
    ├── Login Tab (updated)
    └── Register Tab (updated)
```

---

## 🔄 API Request Flow

```
Frontend Component
        ↓
    Axios Client
        ↓
  Add Bearer Token
        ↓
    HTTP Request
        ↓
Backend Controller
        ↓
   Middleware Check
        ↓
   Verify Token
        ↓
  Process Request
        ↓
Database Query
        ↓
  JSON Response
        ↓
   Frontend Update
```

---

## 💼 Project Stats

```
Frontend Components:        10+
Backend Endpoints:          11
Database Tables:            3 (users, simulations, tokens)
Authentication Type:        JWT (Sanctum)
Frontend Framework:         React 18
Backend Framework:          Laravel 11
Database Supported:         MySQL, SQLite
Documentation Files:        6
Lines of Code:             1000+
```

---

## 🎓 Key Concepts

- **JWT Tokens:** Stateless authentication
- **RESTful API:** Standard HTTP methods for CRUD
- **CORS:** Cross-Origin Resource Sharing
- **Middleware:** Request processing layer
- **Models:** Database representations
- **Migrations:** Schema version control
- **Pagination:** Large result set handling

---

## 🚀 Next Steps

1. **Run the application**
   ```bash
   start-dev.bat
   ```

2. **Register and test**
   - Create account
   - Run simulation
   - Save to history
   - Load and verify

3. **Read documentation**
   - SETUP_GUIDE.md for installation
   - API_TESTING_GUIDE.md for endpoint testing
   - ARCHITECTURE.md for system design

4. **Customize** (optional)
   - Modify UI colors
   - Add new cache parameters
   - Extend TUM integration
   - Deploy to production

---

**Keep this card handy while developing! 📌**

Last Updated: November 29, 2024
