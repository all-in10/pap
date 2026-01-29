# TeamCore HR Management API Documentation

## Overview

The TeamCore HR Management API is a comprehensive REST API for managing employees, contracts, timeoff requests, work hours tracking, and audit logging in a modern HR system.

**Version**: 1.0.0
**Base URL**: `/api/v1`
**Documentation URL**: `/swagger-ui.html`
**OpenAPI Spec**: `/api/docs.json`

## Authentication

All endpoints (except login) require Bearer token authentication. Obtain a token by logging in:

```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Request Header
```
Authorization: Bearer {token}
```

## Response Format

All responses are in JSON format with consistent structure:

### Success Response (200, 201)
```json
{
  "id": 1,
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@example.com",
  ...
}
```

### Paginated Response (200)
```json
{
  "data": [
    { "id": 1, "name": "Item 1" },
    { "id": 2, "name": "Item 2" }
  ],
  "meta": {
    "current_page": 1,
    "total": 100,
    "per_page": 15
  },
  "links": {
    "first": "...",
    "last": "...",
    "next": "..."
  }
}
```

### Error Response (4xx, 5xx)
```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

## Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 201 | Created - Resource created successfully |
| 204 | No Content - Successful deletion |
| 400 | Bad Request - Invalid request format |
| 401 | Unauthorized - Missing or invalid token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 500 | Internal Server Error - Server error |

## Endpoints Summary

### Authentication
- `POST /auth/login` - User login
- `GET /v1/me` - Get current user
- `POST /v1/logout` - Logout

### Employees (CRUD)
- `GET /v1/employees` - List employees (with pagination & filters)
- `POST /v1/employees` - Create employee
- `GET /v1/employees/{id}` - Get employee details
- `PUT /v1/employees/{id}` - Update employee
- `DELETE /v1/employees/{id}` - Delete employee

### Timeoffs (CRUD)
- `GET /v1/timeoffs` - List timeoff requests
- `POST /v1/timeoffs` - Request timeoff
- `GET /v1/timeoffs/{id}` - Get timeoff details
- `PUT /v1/timeoffs/{id}` - Approve/reject timeoff
- `DELETE /v1/timeoffs/{id}` - Delete timeoff

### Worklogs (CRUD)
- `GET /v1/worklogs` - List worklogs
- `POST /v1/worklogs` - Create worklog
- `GET /v1/worklogs/{id}` - Get worklog details
- `PUT /v1/worklogs/{id}` - Update worklog
- `DELETE /v1/worklogs/{id}` - Delete worklog

### Audit
- `GET /audit-logs` - List audit logs

## Complete Documentation

For detailed endpoint documentation including:
- Request/response examples
- Query parameters and filters
- Validation rules
- Error codes

Visit the interactive Swagger UI at: **[/swagger-ui.html](/swagger-ui.html)**

Or view the OpenAPI specification at: **[/api/docs.json](/api/docs.json)**
