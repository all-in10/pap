# 🔌 REST API v1 - Guia Completo

**Base URL:** `http://localhost:8000/api/v1`  
**Autenticação:** Bearer Token (Sanctum)  
**Resposta:** JSON  

---

## 📋 Índice de Endpoints

- [Authentication](#authentication)
- [Employees](#employees)
- [Worklogs](#worklogs)
- [Timeoffs](#timeoffs)
- [Audit Logs](#audit-logs)

---

## 🔐 Authentication

### GET /me
Obtém dados do usuário autenticado

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Admin User",
  "email": "admin@example.com",
  "role": "admin"
}
```

---

### POST /logout
Faz logout do usuário

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

---

## 👥 Employees

### GET /employees
Lista todos os funcionários com paginação

**Query Parameters:**
| Param | Type | Description | Example |
|-------|------|-------------|---------|
| `per_page` | int | Registros por página (default: 25) | `50` |
| `department_id` | int | Filtrar por departamento | `5` |
| `designation_id` | int | Filtrar por designação | `3` |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "first_name": "João",
      "last_name": "Silva",
      "email": "joao@example.com",
      "phone": "123456789",
      "nif": "123.456.789-01",
      "nss": "123.456.789-01",
      "date_of_birth": "1990-01-15",
      "country_id": 1,
      "state_id": 1,
      "city_id": 1,
      "department_id": 5,
      "designation_id": 3,
      "created_at": "2026-01-29T10:00:00Z",
      "updated_at": "2026-01-29T10:00:00Z"
    }
  ],
  "per_page": 25,
  "total": 52,
  "last_page": 3
}
```

**Permissões:** ADMIN, HR, ROOT

---

### GET /employees/{id}
Obtém detalhes de um funcionário específico

**Response (200):**
```json
{
  "id": 1,
  "first_name": "João",
  "last_name": "Silva",
  "email": "joao@example.com",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "joao@example.com",
    "role": "employee"
  },
  "department": {
    "id": 5,
    "name": "Tecnologia"
  },
  "designation": {
    "id": 3,
    "name": "Desenvolvedor Senior"
  }
}
```

**Permissões:** ADMIN, HR, ROOT

---

### PUT /employees/{id}
Atualiza dados do funcionário

**Body:**
```json
{
  "first_name": "João",
  "last_name": "Silva",
  "email": "joao@example.com",
  "phone": "987654321",
  "date_of_birth": "1990-01-15",
  "department_id": 5,
  "designation_id": 3
}
```

**Response (200):** Dados atualizados (mesmo formato de GET /employees/{id})

**Permissões:** ADMIN, HR, ROOT

---

### DELETE /employees/{id}
Deleta um funcionário (soft delete)

**Response (200):**
```json
{
  "message": "Employee deleted"
}
```

**Permissões:** ADMIN, ROOT

---

## 📝 Worklogs

### GET /worklogs
Lista todos os registros de trabalho

**Query Parameters:**
| Param | Type | Description | Example |
|-------|------|-------------|---------|
| `per_page` | int | Registros por página | `25` |
| `employee_id` | int | Filtrar por funcionário | `1` |
| `work_date` | date | Filtrar por data (YYYY-MM-DD) | `2026-01-29` |

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "work_date": "2026-01-29",
      "start_time": "08:00",
      "break_start": "12:00",
      "break_end": "13:00",
      "end_time": "17:00",
      "hours_worked": 8,
      "extra_hours": 0,
      "created_at": "2026-01-29T09:00:00Z",
      "updated_at": "2026-01-29T17:00:00Z"
    }
  ],
  "per_page": 25,
  "total": 156,
  "last_page": 7
}
```

**Permissões:** ADMIN, HR, ROOT

---

### GET /worklogs/{id}
Obtém detalhes de um registro

**Response (200):**
```json
{
  "id": 1,
  "employee_id": 1,
  "work_date": "2026-01-29",
  "start_time": "08:00",
  "break_start": "12:00",
  "break_end": "13:00",
  "end_time": "17:00",
  "hours_worked": 8,
  "extra_hours": 0,
  "employee": {
    "id": 1,
    "first_name": "João",
    "last_name": "Silva"
  }
}
```

**Permissões:** ADMIN, HR, ROOT

---

### PUT /worklogs/{id}
Atualiza horários do worklog

**Body:**
```json
{
  "start_time": "07:30",
  "break_start": "12:00",
  "break_end": "13:00",
  "end_time": "17:30"
}
```

**Response (200):** Dados atualizados

**Permissões:** ADMIN, HR, ROOT

---

### DELETE /worklogs/{id}
Deleta um registro (soft delete)

**Response (200):**
```json
{
  "message": "Worklog deleted"
}
```

**Permissões:** ADMIN, HR, ROOT

---

## 🏖️ Timeoffs

### GET /timeoffs
Lista todas as solicitações de ausência

**Query Parameters:**
| Param | Type | Description | Example |
|-------|------|-------------|---------|
| `per_page` | int | Registros por página | `25` |
| `employee_id` | int | Filtrar por funcionário | `1` |
| `status` | string | Filtrar por status (pending/approved/rejected) | `approved` |

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "employee_id": 1,
      "type": "vacation",
      "start_date": "2026-02-01",
      "end_date": "2026-02-10",
      "days_count": 8,
      "reason": "Férias anuais",
      "status": "pending",
      "created_at": "2026-01-29T10:00:00Z",
      "updated_at": "2026-01-29T10:00:00Z"
    }
  ],
  "per_page": 25,
  "total": 24,
  "last_page": 1
}
```

**Permissões:** ADMIN, HR, ROOT

---

### POST /timeoffs
Cria uma nova solicitação de ausência

**Body:**
```json
{
  "employee_id": 1,
  "type": "vacation",
  "start_date": "2026-02-01",
  "end_date": "2026-02-10",
  "reason": "Férias anuais"
}
```

**Tipos Válidos:**
- `vacation` - Férias
- `sick_leave` - Licença médica
- `personal_leave` - Licença pessoal
- `other` - Outro

**Response (201):**
```json
{
  "id": 1,
  "employee_id": 1,
  "type": "vacation",
  "start_date": "2026-02-01",
  "end_date": "2026-02-10",
  "days_count": 8,
  "reason": "Férias anuais",
  "status": "pending",
  "created_at": "2026-01-29T10:00:00Z",
  "updated_at": "2026-01-29T10:00:00Z"
}
```

**Validações:**
- ✓ Não permite overlaps com timeoffs aprovados
- ✓ Data final >= data inicial
- ✓ Tipo deve ser válido

---

### GET /timeoffs/{id}
Obtém detalhes da solicitação

**Response (200):**
```json
{
  "id": 1,
  "employee_id": 1,
  "type": "vacation",
  "start_date": "2026-02-01",
  "end_date": "2026-02-10",
  "days_count": 8,
  "reason": "Férias anuais",
  "status": "pending",
  "employee": {
    "id": 1,
    "first_name": "João",
    "last_name": "Silva"
  }
}
```

---

### PUT /timeoffs/{id}
Atualiza status da solicitação

**Body:**
```json
{
  "status": "approved"
}
```

**Status Válidos:**
- `pending` - Pendente
- `approved` - Aprovado
- `rejected` - Rejeitado

**Response (200):** Dados atualizados

**Permissões:** ADMIN, HR, ROOT

---

### DELETE /timeoffs/{id}
Deleta a solicitação (soft delete)

**Response (200):**
```json
{
  "message": "Timeoff deleted"
}
```

**Permissões:** ADMIN, HR, ROOT

---

## 📊 Audit Logs

### GET /audit-logs
Lista logs de auditoria

**Query Parameters:**
| Param | Type | Description | Example |
|-------|------|-------------|---------|
| `per_page` | int | Registros por página | `50` |
| `model_type` | string | Filtrar por tipo de modelo | `Employee` |
| `action` | string | Filtrar por ação (create/update/delete) | `update` |

**Response (200):**
```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "action": "update",
      "model_type": "Employee",
      "model_id": 5,
      "changes": {
        "department_id": ["3", "5"],
        "updated_at": ["2026-01-28T10:00:00Z", "2026-01-29T10:00:00Z"]
      },
      "created_at": "2026-01-29T10:00:00Z"
    }
  ],
  "per_page": 50,
  "total": 342,
  "last_page": 7
}
```

**Permissões:** ADMIN, ROOT (somente)

---

## ⚠️ Respostas de Erro

### HTTP 401 - Não Autenticado
```json
{
  "message": "Unauthenticated."
}
```

### HTTP 403 - Sem Permissão
```json
{
  "message": "Você não tem permissão para executar esta ação"
}
```

### HTTP 422 - Validação Falhou
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email has already been taken."],
    "start_date": ["The start date must be a date."]
  }
}
```

### HTTP 404 - Não Encontrado
```json
{
  "message": "Resource not found"
}
```

---

## 🧪 Exemplos de Curl

### Login
```bash
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Listar Funcionários
```bash
curl -X GET "http://localhost:8000/api/v1/employees?per_page=10&department_id=5" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```

### Criar Timeoff
```bash
curl -X POST http://localhost:8000/api/v1/timeoffs \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "employee_id": 1,
    "type": "vacation",
    "start_date": "2026-02-01",
    "end_date": "2026-02-10",
    "reason": "Férias anuais"
  }'
```

### Atualizar Worklog
```bash
curl -X PUT http://localhost:8000/api/v1/worklogs/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "start_time": "07:30",
    "end_time": "17:30"
  }'
```

---

## 🔑 Permissões de Endpoints

| Endpoint | ADMIN | HR | ROOT | EMPLOYEE |
|----------|-------|----|----|----------|
| GET /employees | ✓ | ✓ | ✓ | ✗ |
| PUT /employees/{id} | ✓ | ✓ | ✓ | ✗ |
| DELETE /employees/{id} | ✓ | ✗ | ✓ | ✗ |
| GET /worklogs | ✓ | ✓ | ✓ | ✗ |
| PUT /worklogs/{id} | ✓ | ✓ | ✓ | ✗ |
| GET /timeoffs | ✓ | ✓ | ✓ | ✓ |
| POST /timeoffs | ✓ | ✓ | ✓ | ✓ |
| PUT /timeoffs/{id} | ✓ | ✓ | ✓ | ✗ |
| GET /audit-logs | ✓ | ✗ | ✓ | ✗ |

---

## 📚 Referências

- **Laravel Sanctum:** [Documentation](https://laravel.com/docs/11.x/sanctum)
- **API Resources:** `app/Http/Controllers/Api/*`
- **Routes:** `routes/api.php`
- **Tests:** `tests/Feature/Api/*`

---

**Documentação criada em:** 29 de Janeiro de 2026  
**Última atualização:** 29 de Janeiro de 2026
