@echo off
REM PICKS Cache Simulator - Quick Start Script for Windows

echo.
echo ====================================================
echo PICKS Cache Simulator - Full Stack Development
echo ====================================================
echo.

REM Check if Node.js is installed
node --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Node.js is not installed or not in PATH
    echo Download from: https://nodejs.org
    pause
    exit /b 1
)

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: PHP is not installed or not in PATH
    echo Download from: https://www.php.net/downloads
    pause
    exit /b 1
)

REM Check if Composer is installed
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo Error: Composer is not installed
    echo Download from: https://getcomposer.org/download/
    pause
    exit /b 1
)

echo ✓ Node.js found: %NODE_VERSION%
echo ✓ PHP found: %PHP_VERSION%
echo ✓ Composer found
echo.

REM Create logs directory
if not exist "logs" mkdir logs

echo Step 1: Checking dependencies...
echo.

REM Check frontend dependencies
if not exist "node_modules" (
    echo Installing frontend dependencies...
    call npm install
    if %errorlevel% neq 0 (
        echo Error installing frontend dependencies
        pause
        exit /b 1
    )
)

REM Check backend dependencies
if not exist "backend\vendor" (
    echo Installing backend dependencies...
    cd backend
    call composer install
    if %errorlevel% neq 0 (
        echo Error installing backend dependencies
        cd ..
        pause
        exit /b 1
    )
    cd ..
)

echo ✓ Dependencies installed
echo.

echo Step 2: Checking environment files...
echo.

REM Create .env.local if it doesn't exist
if not exist ".env.local" (
    echo Creating .env.local...
    (
        echo VITE_API_URL=http://localhost:8000/api
    ) > .env.local
    echo ✓ Created .env.local
)

REM Create backend .env if it doesn't exist
if not exist "backend\.env" (
    echo Creating backend\.env...
    cd backend
    copy .env.example .env >nul
    cd ..
    echo ✓ Created backend\.env
    echo.
    echo IMPORTANT: Edit backend\.env and configure:
    echo   - DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE
    echo   - DB_USERNAME, DB_PASSWORD
    echo.
    echo Then run: cd backend ^&^& php artisan migrate
    echo.
    pause
    exit /b 0
)

REM Generate Laravel key if not already set
cd backend
for /f "tokens=*" %%A in (app/app.php) do (
    if "%%A" neq "" (
        set APP_KEY_SET=1
    )
)
if not defined APP_KEY_SET (
    echo Generating Laravel application key...
    call php artisan key:generate
    echo ✓ Key generated
)
cd ..
echo.

echo Step 3: Checking database...
echo.

REM Check if migrations have been run (basic check)
if not exist "backend/database/database.sqlite" (
    if not exist "backend/database" mkdir backend/database
    echo.
    echo Note: Database not found. 
    echo After starting backend, run migrations:
    echo   cd backend
    echo   php artisan migrate
    echo.
)

echo Step 4: Starting development servers...
echo.

REM Clear screen
cls

echo.
echo ====================================================
echo PICKS Cache Simulator - Development Servers
echo ====================================================
echo.
echo Frontend (React Vite):  http://localhost:5173
echo Backend (Laravel):      http://localhost:8000
echo API Docs:               http://localhost:8000/api
echo.
echo Press Ctrl+C to stop any server
echo.
echo ====================================================
echo.

REM Start both servers in separate windows
echo Starting Backend (Laravel)...
start "PICKS Backend - Laravel" cmd /k "cd backend ^& php artisan serve"

timeout /t 2

echo Starting Frontend (React)...
start "PICKS Frontend - React" cmd /k "npm run dev"

timeout /t 3

echo.
echo Servers started! Check the terminal windows.
echo.
echo Next steps:
echo   1. Open http://localhost:5173 in your browser
echo   2. Register a new account
echo   3. Try the simulator
echo   4. Check History page to save/load simulations
echo.
echo To stop servers: Close the terminal windows or press Ctrl+C
echo.

pause
