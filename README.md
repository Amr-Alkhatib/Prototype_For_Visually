# PICKS Cache Simulator - Complete Full-Stack Application

A comprehensive, interactive cache simulator for learning RISC-V memory hierarchy, built with modern web technologies.

## 🎯 Features

### ✨ Interactive Simulation
- Real-time cache behavior visualization
- L1, L2, and RAM hierarchies
- Power consumption analysis
- Dynamic data flow diagrams
- Customizable cache parameters

### 🔐 User Authentication
- User registration and login
- Secure password hashing
- JWT token-based authentication
- TUM Online integration framework

### 💾 Simulation History
- Save simulation configurations and results
- Persistent storage in database
- Load and re-run previous simulations
- Delete simulation history
- Pagination and filtering

### 📚 Educational Resources
- Comprehensive theory section
- Cache concepts explanation
- Performance metrics guidance
- AI-powered chatbot assistance

## 🏗️ Architecture

### Tech Stack

**Frontend:**
- React 18.3 with Vite
- Tailwind CSS for styling
- React Router v6 for navigation
- Axios for HTTP requests
- Chart.js for visualizations
- Lucide React for icons

**Backend:**
- Laravel 11 PHP framework
- Laravel Sanctum for API authentication
- MySQL 8.0+ database
- Composer for dependencies

**Infrastructure:**
- Docker-ready configuration
- CORS enabled for frontend-backend communication
- Production-ready error handling

## 📋 Prerequisites

- **Node.js** 16.0+ (Frontend development)
- **PHP** 8.1+ (Backend runtime)
- **Composer** 2.0+ (PHP package manager)
- **MySQL** 8.0+ (Database) - or SQLite for development
- **Git** (Optional, for version control)

## 🚀 Quick Start

### Option 1: Windows Batch Script (Easiest)

```bash
# Run the quick start script
start-dev.bat
```

This will:
- Check all prerequisites
- Install dependencies
- Create configuration files
- Start both development servers

### Option 2: Manual Setup

#### Frontend Setup
```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Access at http://localhost:5173
```

#### Backend Setup
```bash
# Navigate to backend
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database in .env
# Then run migrations
php artisan migrate

# Start development server
php artisan serve

# Access API at http://localhost:8000/api
```

## 📖 Documentation

- **[SETUP_GUIDE.md](./SETUP_GUIDE.md)** - Detailed setup instructions
- **[ARCHITECTURE.md](./ARCHITECTURE.md)** - System design and architecture
- **[backend/README.md](./backend/README.md)** - Backend-specific documentation
- **[API Endpoints](./backend/README.md#api-endpoints)** - Complete API reference

## 🔑 Key Files

### Frontend Components
```
src/
├── components/
│   ├── Auth/LoginOverlay.jsx          # Authentication interface
│   ├── Pages/HistoryPage.jsx          # Simulation history & management
│   ├── Layout/Header.jsx              # Navigation with history button
│   ├── Visualization/                 # Cache visualization components
│   └── Dashboard/                     # Simulation controls
├── api/
│   ├── client.js                      # Axios HTTP client
│   └── endpoints.js                   # API functions
└── App.jsx                            # Main app component
```

### Backend
```
backend/
├── app/
│   ├── Models/
│   │   ├── User.php                   # User model with authentication
│   │   └── Simulation.php             # Simulation data model
│   └── Http/Controllers/Api/
│       ├── AuthController.php         # Authentication endpoints
│       └── SimulationController.php   # Simulation CRUD operations
├── routes/api.php                     # API route definitions
├── database/migrations/               # Database schema
├── config/                            # Configuration files
└── .env.example                       # Environment template
```

## 🔄 Authentication Flow

```
User Registers/Logs In
         ↓
Frontend sends credentials
         ↓
Backend validates & hashes password
         ↓
JWT token generated
         ↓
Token stored in localStorage
         ↓
Token sent with API requests
         ↓
Middleware validates token
         ↓
Request processed or rejected
```

## 💾 Simulation Storage

### Local Development
- Simulations stored in MySQL/SQLite
- Each user isolated to their own records
- Timestamps track creation and updates
- Configuration and results stored as JSON

### Data Structure
```json
{
  "id": 1,
  "name": "Cache Test Run",
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

## 🧪 Testing

### Manual Testing Checklist

- [ ] Register new user
- [ ] Login with credentials
- [ ] Run a simulation
- [ ] Save simulation to history
- [ ] View history page
- [ ] Load previous simulation
- [ ] Update simulation notes
- [ ] Delete simulation
- [ ] Logout and login again
- [ ] Verify history persists

### API Testing

Use Postman, Insomnia, or cURL:

```bash
# Register
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"User","email":"test@example.com","password":"Test123!","password_confirmation":"Test123!"}'

# Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"Test123!"}'

# Get simulations
curl -X GET http://localhost:8000/api/simulations \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🐛 Troubleshooting

### Common Issues

**CORS Error:**
- Verify `CORS_ALLOWED_ORIGINS` in backend `.env`
- Check frontend URL matches exactly (including port)
- Restart backend server

**Database Connection Failed:**
- Ensure MySQL is running
- Check credentials in backend `.env`
- Create database: `CREATE DATABASE picks_simulator;`

**Migrations Failed:**
- Run: `cd backend && php artisan migrate`
- Check database permissions
- Verify PHP extensions: PDO, MySQL

**Frontend Not Loading:**
- Clear node_modules: `rm -r node_modules && npm install`
- Check Node.js version: `node --version`
- Verify port 5173 is available

**Backend Port Already in Use:**
- Change port: `php artisan serve --port=8001`
- Or kill process using port 8000

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    tum_id VARCHAR(255) UNIQUE,
    is_tum_verified BOOLEAN,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Simulations Table
```sql
CREATE TABLE simulations (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    name VARCHAR(255),
    description TEXT,
    configuration LONGTEXT,
    results LONGTEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 🌐 Environment Variables

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
DB_DATABASE=picks_simulator
DB_USERNAME=root
DB_PASSWORD=
CORS_ALLOWED_ORIGINS=http://localhost:5173
TUM_ONLINE_API_URL=https://campus.tum.de/tumonline/rest/v1
```

## 🚢 Deployment

### Production Checklist

- [ ] Set `APP_DEBUG=false` in backend
- [ ] Set `APP_ENV=production`
- [ ] Configure CORS for production domain
- [ ] Use environment-specific database
- [ ] Enable HTTPS
- [ ] Set up SSL certificates
- [ ] Configure email service
- [ ] Setup automated backups
- [ ] Enable monitoring and logging
- [ ] Configure CDN for assets

### Docker Deployment

```dockerfile
# Dockerfile (example)
FROM php:8.1-fpm
RUN composer install --no-dev
RUN php artisan migrate --force
```

## 🎓 Learning Resources

- [Laravel Documentation](https://laravel.com/docs)
- [React Documentation](https://react.dev)
- [Cache Concepts](./src/components/Pages/TheoryPage.jsx)
- [API Architecture](./ARCHITECTURE.md)

## 👥 Team & Credits

**PICKS Team** - Technical University of Munich
- Cache Simulator: Educational tool for computer architecture
- Backend Architecture: RESTful API design
- Frontend: Interactive user interface

## 📝 License

MIT License - See LICENSE file for details

## 🤝 Contributing

1. Create feature branch: `git checkout -b feature/your-feature`
2. Make changes and commit
3. Push to branch
4. Create pull request

## 📞 Support

For issues or questions:

1. Check [SETUP_GUIDE.md](./SETUP_GUIDE.md)
2. Review [ARCHITECTURE.md](./ARCHITECTURE.md)
3. Check backend [README.md](./backend/README.md)
4. Review API endpoints documentation

## 🗺️ Project Structure

```
PICKS/
├── 📄 README.md                    # This file
├── 📄 SETUP_GUIDE.md              # Detailed setup instructions
├── 📄 ARCHITECTURE.md             # System architecture
├── 📄 .env.local                  # Frontend environment
├── 📄 start-dev.bat               # Windows quick start
├── 📦 package.json                # Frontend dependencies
├── 🎨 vite.config.js              # Vite configuration
├── 🎨 tailwind.config.js          # Tailwind CSS config
├── src/                           # React source code
│   ├── api/                       # API client
│   ├── components/                # React components
│   ├── context/                   # React context
│   ├── App.jsx                    # Main component
│   └── index.css                  # Global styles
├── public/                        # Static assets
└── backend/                       # Laravel backend
    ├── 📄 README.md              # Backend documentation
    ├── 📄 composer.json          # PHP dependencies
    ├── 📄 .env.example           # Backend environment
    ├── app/                      # Application code
    ├── routes/                   # API routes
    ├── database/                 # Migrations & seeds
    ├── config/                   # Configuration
    └── bootstrap/                # Bootstrap files
```

## ✅ Completed Features

- [x] React + Vite frontend
- [x] Laravel 11 backend
- [x] User authentication (registration/login)
- [x] JWT token authentication
- [x] Simulation CRUD operations
- [x] Database schema design
- [x] API endpoint implementation
- [x] Frontend-backend integration
- [x] History page with load/delete
- [x] CORS configuration
- [x] Error handling
- [x] Documentation
- [x] Quick start setup

## 🔮 Future Enhancements

- [ ] TUM Online OAuth integration
- [ ] Email verification
- [ ] Password reset functionality
- [ ] User profiles
- [ ] Simulation sharing
- [ ] Advanced analytics
- [ ] Performance optimization
- [ ] Mobile responsive improvements
- [ ] Dark/Light theme persistence
- [ ] Export to PDF
- [ ] Batch simulation runs
- [ ] Collaboration features

---

**Version:** 3.0 Full Stack
**Last Updated:** November 29, 2024
**Status:** Production Ready ✅

Start the application with `start-dev.bat` (Windows) or follow manual setup in [SETUP_GUIDE.md](./SETUP_GUIDE.md)
