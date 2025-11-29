# PICKS Cache Simulator - Architecture Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                      Frontend (React + Vite)                        │
│                       http://localhost:5173                          │
├─────────────────────────────────────────────────────────────────────┤
│ • Authentication UI (Login/Register)                               │
│ • Cache Simulator Visualization                                     │
│ • History Page (Load/Delete Simulations)                           │
│ • Theory Page                                                       │
│ • Axios API Client with JWT Token Injection                        │
└─────────────────────────────┬───────────────────────────────────────┘
                              │
                    HTTP/REST API calls
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                    Backend (Laravel 11)                             │
│                    http://localhost:8000                            │
├─────────────────────────────────────────────────────────────────────┤
│ Authentication Controller                                            │
│ ├─ /api/auth/register (POST)                                       │
│ ├─ /api/auth/login (POST)                                          │
│ ├─ /api/auth/logout (POST)                                         │
│ ├─ /api/auth/me (GET)                                              │
│ └─ /api/auth/verify-tum (POST)                                     │
│                                                                      │
│ Simulation Controller                                                │
│ ├─ /api/simulations (GET - paginated)                              │
│ ├─ /api/simulations (POST - create)                                │
│ ├─ /api/simulations/{id} (GET)                                     │
│ ├─ /api/simulations/{id} (PUT - update)                            │
│ ├─ /api/simulations/{id} (DELETE)                                  │
│ └─ /api/simulations/bulk-delete (POST)                             │
│                                                                      │
│ Middleware:                                                          │
│ ├─ CORS (for frontend communication)                               │
│ ├─ JWT Auth (Sanctum - token verification)                         │
│ └─ Validation (request data validation)                            │
└─────────────────────────────┬───────────────────────────────────────┘
                              │
                         Database queries
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────────┐
│                   MySQL Database                                     │
│                 picks_simulator database                             │
├─────────────────────────────────────────────────────────────────────┤
│ users                       │ simulations                           │
│ ├─ id (PK)                 │ ├─ id (PK)                           │
│ ├─ name                    │ ├─ user_id (FK)                      │
│ ├─ email (UNIQUE)          │ ├─ name                              │
│ ├─ password (hashed)       │ ├─ description                       │
│ ├─ tum_id (UNIQUE)         │ ├─ configuration (JSON)              │
│ ├─ is_tum_verified         │ ├─ results (JSON)                    │
│ ├─ created_at              │ ├─ notes                             │
│ └─ updated_at              │ ├─ created_at                        │
│                             │ └─ updated_at                        │
│                             │                                       │
│ personal_access_tokens (Sanctum)                                   │
│ ├─ id (PK)                                                         │
│ ├─ tokenable_id (FK to users)                                      │
│ ├─ token (hashed)                                                  │
│ └─ expires_at (optional)                                           │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow

### Authentication Flow
```
1. User Registration
   Frontend → POST /api/auth/register → Backend → Hash password → Save to DB
   ↓ Returns token
   Frontend stores token in localStorage

2. User Login
   Frontend → POST /api/auth/login → Backend → Verify credentials → Generate token
   ↓ Returns token
   Frontend stores token in localStorage

3. Protected Request
   Frontend (with token) → API endpoint → Middleware checks token
   ↓ If valid: process request
   ↓ If invalid: return 401 Unauthorized
```

### Simulation Save Flow
```
1. User runs simulation
2. Frontend collects configuration and results
3. User clicks "Save Simulation"
4. Frontend → POST /api/simulations with:
   - name
   - configuration (JSON)
   - results (JSON)
5. Backend:
   - Validates data
   - Verifies user owns request
   - Stores in database
6. Returns simulation ID and confirmation
```

### Simulation Load Flow
```
1. User visits History page
2. Frontend → GET /api/simulations (with user's token)
3. Backend:
   - Retrieves user from token
   - Queries simulations where user_id = authenticated_user
   - Returns paginated results
4. Frontend displays list
5. User clicks "Load"
6. Frontend gets full simulation configuration
7. Applies settings to simulator
```

## Authentication & Security

### JWT Token (Sanctum)
- Generated on login/registration
- Stored in frontend localStorage
- Sent with every protected request as: `Authorization: Bearer {token}`
- Laravel middleware validates token before allowing access
- Tokens can expire (configurable)

### Password Security
- Passwords hashed using bcrypt (Laravel default)
- Never stored in plain text
- Validated against requirements on registration

### User Data Isolation
- Each user can only access their own simulations
- Backend enforces `user_id` check on queries
- CORS prevents unauthorized frontend access

## Key Technologies

### Frontend Stack
- **React 18.3** - UI framework
- **Vite 5.1** - Build tool and dev server
- **React Router v6** - Client-side routing
- **Axios 1.6** - HTTP client
- **Tailwind CSS** - Styling
- **Chart.js** - Data visualization

### Backend Stack
- **Laravel 11** - PHP framework
- **Sanctum 4.0** - API authentication
- **MySQL 8.0+** - Database
- **Composer** - PHP package manager

### Deployment Stack
- **PHP 8.1+** - Runtime
- **Node.js 16+** - Frontend build
- **MySQL/MariaDB** - Database server

## Performance Considerations

### Database Optimization
- Indexed columns: email, tum_id, user_id, created_at
- Composite index on (user_id, created_at) for efficient queries
- Pagination to limit result sets

### API Optimization
- Stateless design (no server sessions)
- JWT tokens - no database queries for validation
- CORS headers pre-calculated

### Frontend Optimization
- Code splitting with Vite
- Lazy loading of routes
- Local token caching to avoid re-authentication

## Scalability Considerations

### Current Architecture (Single Server)
- Good for: Development, small user base (<1000 users)
- Limitation: Single point of failure

### Future Scaling
1. **Load Balancing**
   - Multiple Laravel instances
   - Shared database
   - Redis for session management

2. **Database Scaling**
   - Read replicas for queries
   - Write-through cache layer
   - Database indexing strategy

3. **File Storage**
   - Move to cloud storage (AWS S3, Azure Blob)
   - CDN for static assets

## Testing Strategy

### Unit Tests
- Controller logic validation
- Model relationships
- API endpoint validation

### Integration Tests
- Full authentication flow
- Simulation CRUD operations
- Permission verification

### E2E Tests
- Login/Register workflow
- Save and load simulation
- History page functionality

## Environment Configuration

### Development
```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite (or mysql)
```

### Production
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql
```

## File Organization

```
PICKS/
├── frontend/
│   ├── src/
│   │   ├── api/
│   │   │   ├── client.js (API client setup)
│   │   │   └── endpoints.js (API functions)
│   │   ├── components/
│   │   │   ├── Auth/
│   │   │   ├── Pages/
│   │   │   └── ...
│   │   └── App.jsx
│   ├── .env.local (frontend config)
│   └── package.json
│
└── backend/
    ├── app/
    │   ├── Models/
    │   │   ├── User.php
    │   │   └── Simulation.php
    │   ├── Http/
    │   │   ├── Controllers/Api/
    │   │   │   ├── AuthController.php
    │   │   │   └── SimulationController.php
    │   │   └── Middleware/
    │   ├── routes/
    │   │   └── api.php
    │   └── config/
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── .env (backend config)
    ├── composer.json
    └── README.md
```

## Error Handling

### Frontend
- API errors caught by axios interceptor
- 401 errors trigger logout
- User-friendly error messages

### Backend
- Input validation at controller level
- Database transaction rollback on error
- Consistent JSON error responses

```json
{
  "error": "Error message",
  "errors": {
    "field": ["Field validation error"]
  }
}
```

## Future Enhancements

1. **Email Verification**
   - Send confirmation email on registration
   - Prevent using unverified accounts

2. **Password Reset**
   - Forgot password flow
   - Token-based reset links

3. **User Profiles**
   - Edit user information
   - Avatar upload
   - Preferences

4. **Social Features**
   - Share simulations
   - Comments/annotations
   - Leaderboards

5. **Analytics**
   - Track simulation runs
   - Popular configurations
   - Performance metrics

6. **API Rate Limiting**
   - Prevent abuse
   - Fair usage policy
   - Throttling

---

This architecture provides a solid foundation for the PICKS Cache Simulator with room for growth and enhancement.
