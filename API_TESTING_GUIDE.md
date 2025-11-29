# API Testing Guide

## Overview

This guide provides comprehensive examples for testing the PICKS Cache Simulator API using various tools.

## Prerequisites

- Backend running: `php artisan serve` (http://localhost:8000)
- API base URL: `http://localhost:8000/api`

## Tools

### cURL (Command Line)
```bash
# Basic request
curl -X GET http://localhost:8000/api/simulations

# With authentication
curl -X GET http://localhost:8000/api/simulations \
  -H "Authorization: Bearer YOUR_TOKEN"

# With JSON data
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"Test123!"}'
```

### Postman
1. Create new collection "PICKS API"
2. Set base URL: `{{BASE_URL}}/api`
3. Create variables:
   - BASE_URL: http://localhost:8000
   - TOKEN: (will be filled after login)

### Insomnia
Similar to Postman, supports environment variables and request sequences.

---

## Authentication Endpoints

### 1. Register User

**Endpoint:** `POST /auth/register`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "SecurePassword123!",
  "password_confirmation": "SecurePassword123!",
  "tum_id": "ab12cdef"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePassword123!",
    "password_confirmation": "SecurePassword123!",
    "tum_id": "ab12cdef"
  }'
```

**Success Response (201):**
```json
{
  "message": "User registered successfully",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "tum_id": "ab12cdef",
    "is_tum_verified": false,
    "created_at": "2024-11-29T10:00:00.000000Z",
    "updated_at": "2024-11-29T10:00:00.000000Z"
  },
  "token": "1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
}
```

**Error Response (422):**
```json
{
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password confirmation does not match."]
  }
}
```

---

### 2. Login User

**Endpoint:** `POST /auth/login`

**Request Headers:**
```
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "SecurePassword123!"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePassword123!"
  }'
```

**Success Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "tum_id": "ab12cdef",
    "is_tum_verified": false,
    "created_at": "2024-11-29T10:00:00.000000Z",
    "updated_at": "2024-11-29T10:00:00.000000Z"
  },
  "token": "1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
}
```

**Error Response (401):**
```json
{
  "error": "Invalid credentials"
}
```

---

### 3. Get Current User

**Endpoint:** `GET /auth/me`

**Request Headers:**
```
Authorization: Bearer {token}
```

**cURL:**
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
```

**Response (200):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "tum_id": "ab12cdef",
    "is_tum_verified": false,
    "created_at": "2024-11-29T10:00:00.000000Z",
    "updated_at": "2024-11-29T10:00:00.000000Z"
  }
}
```

---

### 4. Logout

**Endpoint:** `POST /auth/logout`

**Request Headers:**
```
Authorization: Bearer {token}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
```

**Response (200):**
```json
{
  "message": "Logout successful"
}
```

---

### 5. Verify with TUM Online

**Endpoint:** `POST /auth/verify-tum`

**Request Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "tum_id": "ab12cdef",
  "password": "tum_password"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/verify-tum \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K" \
  -d '{
    "tum_id": "ab12cdef",
    "password": "tum_password"
  }'
```

**Success Response (200):**
```json
{
  "message": "TUM verification successful",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "tum_id": "ab12cdef",
    "is_tum_verified": true,
    "created_at": "2024-11-29T10:00:00.000000Z",
    "updated_at": "2024-11-29T10:00:00.000000Z"
  }
}
```

---

## Simulation Endpoints

### 1. Get All User Simulations (Paginated)

**Endpoint:** `GET /simulations`

**Query Parameters:**
- `page` (optional, default: 1)
- `per_page` (optional, default: 15)
- `sort_by` (optional, default: created_at)
- `order` (optional, default: desc)

**Request Headers:**
```
Authorization: Bearer {token}
```

**cURL:**
```bash
curl -X GET "http://localhost:8000/api/simulations?page=1&per_page=10&sort_by=created_at&order=desc" \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Cache Test Run",
      "description": "Testing L1 and L2 cache behavior",
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
        "hitRate": 0.9706
      },
      "notes": "Good performance with this configuration",
      "created_at": "2024-11-29T10:00:00.000000Z",
      "updated_at": "2024-11-29T10:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost:8000/api/simulations?page=1",
    "last": "http://localhost:8000/api/simulations?page=2",
    "next": "http://localhost:8000/api/simulations?page=2",
    "prev": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 2,
    "per_page": 10,
    "to": 10,
    "total": 15
  }
}
```

---

### 2. Create New Simulation

**Endpoint:** `POST /simulations`

**Request Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "New Simulation",
  "description": "Testing cache parameters",
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
    "hitRate": 0.9706
  },
  "notes": "Initial test run"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/simulations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K" \
  -d '{
    "name": "New Simulation",
    "description": "Testing cache parameters",
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
      "hitRate": 0.9706
    },
    "notes": "Initial test run"
  }'
```

**Response (201):**
```json
{
  "message": "Simulation saved successfully",
  "simulation": {
    "id": 2,
    "user_id": 1,
    "name": "New Simulation",
    "description": "Testing cache parameters",
    "configuration": {...},
    "results": {...},
    "notes": "Initial test run",
    "created_at": "2024-11-29T11:00:00.000000Z",
    "updated_at": "2024-11-29T11:00:00.000000Z"
  }
}
```

---

### 3. Get Specific Simulation

**Endpoint:** `GET /simulations/{id}`

**Request Headers:**
```
Authorization: Bearer {token}
```

**cURL:**
```bash
curl -X GET http://localhost:8000/api/simulations/1 \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
```

**Response (200):**
```json
{
  "id": 1,
  "user_id": 1,
  "name": "Cache Test Run",
  "description": "Testing L1 and L2 cache behavior",
  "configuration": {...},
  "results": {...},
  "notes": "Good performance with this configuration",
  "created_at": "2024-11-29T10:00:00.000000Z",
  "updated_at": "2024-11-29T10:00:00.000000Z"
}
```

**Error Response (404):**
```json
{
  "error": "Simulation not found"
}
```

---

### 4. Update Simulation

**Endpoint:** `PUT /simulations/{id}`

**Request Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body (partial update):**
```json
{
  "name": "Updated Simulation Name",
  "notes": "Updated notes",
  "results": {
    "hits": 2000,
    "misses": 30,
    "hitRate": 0.9852
  }
}
```

**cURL:**
```bash
curl -X PUT http://localhost:8000/api/simulations/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K" \
  -d '{
    "name": "Updated Simulation Name",
    "notes": "Updated notes",
    "results": {
      "hits": 2000,
      "misses": 30,
      "hitRate": 0.9852
    }
  }'
```

**Response (200):**
```json
{
  "message": "Simulation updated successfully",
  "simulation": {
    "id": 1,
    "name": "Updated Simulation Name",
    "notes": "Updated notes",
    "results": {...},
    "updated_at": "2024-11-29T11:30:00.000000Z"
  }
}
```

---

### 5. Delete Simulation

**Endpoint:** `DELETE /simulations/{id}`

**Request Headers:**
```
Authorization: Bearer {token}
```

**cURL:**
```bash
curl -X DELETE http://localhost:8000/api/simulations/1 \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K"
```

**Response (200):**
```json
{
  "message": "Simulation deleted successfully"
}
```

---

### 6. Bulk Delete Simulations

**Endpoint:** `POST /simulations/bulk-delete`

**Request Headers:**
```
Content-Type: application/json
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "ids": [1, 2, 3]
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/simulations/bulk-delete \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 1|4oUZPaX1TAu8gBFV2L9kKXz3jQ5Mg7yqP8x9rZ2K" \
  -d '{
    "ids": [1, 2, 3]
  }'
```

**Response (200):**
```json
{
  "message": "Simulations deleted successfully"
}
```

---

## Error Responses

### 400 - Bad Request
```json
{
  "error": "Invalid request"
}
```

### 401 - Unauthorized
```json
{
  "error": "Unauthenticated"
}
```

### 422 - Validation Error
```json
{
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

### 404 - Not Found
```json
{
  "error": "Resource not found"
}
```

### 500 - Server Error
```json
{
  "error": "Server error occurred"
}
```

---

## Complete Test Workflow

### 1. Register User
```bash
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "TestPass123!",
    "password_confirmation": "TestPass123!"
  }' | jq -r '.token')
echo "Token: $TOKEN"
```

### 2. Create Simulation
```bash
curl -X POST http://localhost:8000/api/simulations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Test Simulation",
    "configuration": {"l1Size": 32, "l2Size": 256, "blockSize": 64}
  }'
```

### 3. Get All Simulations
```bash
curl -X GET "http://localhost:8000/api/simulations" \
  -H "Authorization: Bearer $TOKEN"
```

### 4. Update Simulation
```bash
curl -X PUT http://localhost:8000/api/simulations/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Updated Test Simulation",
    "notes": "Updated via API"
  }'
```

### 5. Delete Simulation
```bash
curl -X DELETE http://localhost:8000/api/simulations/1 \
  -H "Authorization: Bearer $TOKEN"
```

### 6. Logout
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

## Response Status Codes

| Code | Status | Description |
|------|--------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required/failed |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation error |
| 500 | Server Error | Internal server error |

---

## Best Practices

1. **Always include Authorization header** for protected endpoints
2. **Save token** after login for subsequent requests
3. **Test validation** with invalid data (empty fields, wrong types)
4. **Check error messages** for debugging
5. **Use pagination** when retrieving large result sets
6. **Implement retry logic** for network failures
7. **Don't expose tokens** in logs or version control

---

## Debugging Tips

### Enable Query Logging in Laravel
```bash
# In backend .env
DB_LOG=true
DB_QUERY_DEBUG=true
```

### Check Backend Logs
```bash
# In backend directory
tail -f storage/logs/laravel.log
```

### Test with Verbose cURL
```bash
curl -v http://localhost:8000/api/simulations
```

### Use Browser DevTools
- Open http://localhost:5173
- Network tab → filter by XHR
- Check request/response headers and body

---

## Performance Testing

### Load Test with Apache Bench
```bash
ab -n 100 -c 10 http://localhost:8000/api/simulations
```

### Monitor Database Queries
```bash
# MySQL
SHOW PROCESSLIST;
SHOW STATUS LIKE 'Questions';
```

---

This guide should cover all API testing scenarios. Happy testing! 🧪
