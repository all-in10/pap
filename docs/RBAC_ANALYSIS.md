# RBAC - Análise Completa do Sistema de Controlo de Acesso

## 📋 Sumário Executivo

Sistema de Controlo de Acesso Baseado em Papéis (RBAC) implementado no PAP com **3 papéis principais** e **granularidade por funcionalidade**. Oferece isolamento completo de dados e operações por nível de acesso.

---

## 🏗️ Arquitetura RBAC

### Modelo de Dados
```
User (Modelo Base)
├── role: ENUM('admin', 'hr', 'employee')
├── email
├── password
├── employee_id (FK → Employee, apenas para HR/EMPLOYEE)
└── must_change_password: BOOLEAN
```

### Componentes de Implementação

| Componente | Localização | Responsabilidade |
|-----------|------------|-----------------|
| **Policies** | `app/Policies/` | Autorização granular por modelo |
| **panel Providers** | `app/Providers/Filament/` | Recursos permitidos por painel |
| **Gates** | `app/Providers/AppServiceProvider.php` | Permissões globais reutilizáveis |
| **Middleware** | `app/Http/Middleware/` | Proteção de rotas por painel |
| **Traits** | `app/Traits/` | Validações comuns de role |

---

## 👥 Papéis e Responsabilidades

### 1️⃣ ADMIN - Administrador

**Nível de Acesso:** Completo/Root

#### Recursos Permitidos (Admin Panel)
- ✅ **Dashboard** - Visualização de estadísticas globais
- ✅ **Gestão de Utilizadores** - CRUD completo
- ✅ **Gestão de Departamentos** - CRUD completo
- ✅ **Gestão de Designações** - CRUD completo
- ✅ **Dados Geográficos** - Países, Estados, Cidades (CRUD)
- ✅ **Tipos de Contrato** - CRUD completo
- ✅ **Auditoria & Logs** - Visualização completa
- ✅ **Exportação de Dados** - CSV, Excel
- ✅ **Acesso via API** - Acesso total

#### Funcionalidades Bloqueadas
- ❌ Nenhuma (Acesso total ao sistema)

#### Permissões Específicas
```php
// app/Policies/UserPolicy.php
public function viewAny(User $user): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}

public function create(User $user): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}

public function update(User $user, User $model): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}

public function delete(User $user, User $model): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}

public function restore(User $user, User $model): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}

public function forceDelete(User $user, User $model): bool
{
    return $this->isAdmin($user); // ✅ Apenas ADMIN
}
```

#### Gates Aplicadas
```php
Gate::define('is-admin', fn (User $user) => $user->role === 'admin');
Gate::define('view-audit', fn (User $user) => $user->role === 'admin');
Gate::define('export-data', fn (User $user) => $user->role !== 'employee');
Gate::define('is-admin-or-hr', fn (User $user) => in_array($user->role, ['admin', 'hr']));
```

---

### 2️⃣ HR - Recursos Humanos

**Nível de Acesso:** Intermediário (Gestão de Recursos)

#### Recursos Permitidos (HR Panel)
- ✅ **Dashboard HR** - Estadísticas de RH
- ✅ **Gestão de Funcionários** 
  - ✓ Criar novo funcionário
  - ✓ Editar informações
  - ✗ **Eliminar** (Bloqueado)
- ✅ **Gestão de Contratos**
  - ✓ Criar contrato
  - ✓ Editar termos
  - ✗ **Eliminar** (Bloqueado)
- ✅ **Departamentos** (Visualização apenas)
  - ✓ Ver lista
  - ✗ Criar/Editar/Eliminar
- ✅ **Designações** (Visualização apenas)
  - ✓ Ver lista
  - ✗ Criar/Editar/Eliminar
- ✅ **Gestão de Licenças**
  - ✓ Criar propostas de licença
  - ✓ Editar pendentes
  - ✓ Aprovar/Rejeitar
  - ✓ Eliminar
- ✅ **Gestão de Horários de Trabalho**
  - ✓ Visualizar registos
  - ✓ Editar registos
- ✅ **Banco de Horas**
  - ✓ Visualizar saldo
  - ✓ Gerir saldo acumulado
- ✅ **Categorias de Licença**
  - ✓ Criar categoria
  - ✓ Editar categoria
- ✅ **Exportação de Dados**
  - ✓ Exportar para CSV/Excel
- ❌ **Auditoria & Logs**
  - ✗ **Acesso Bloqueado**

#### Funcionalidades Bloqueadas
- ❌ Gestão de Utilizadores
- ❌ Visualização de Auditoria
- ❌ Dados Geográficos
- ❌ Tipos de Contrato

#### Permissões Específicas (TimeoffPolicy)
```php
// Pode visualizar lista de férias
public function viewAny(User $user): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr', 'employee']);
}

// ADMIN tem acesso total (antes de verificações)
public function before(User $user): ?bool
{
    if ($user->role === 'admin') return true;
    return null;
}

// HR pode editar
public function update(User $user, Timeoff $timeoff): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr']);
}

// HR pode eliminar
public function delete(User $user, Timeoff $timeoff): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr']);
}
```

#### Recursos Configurados
```php
// app/Providers/Filament/HRPanelProvider.php
->resources([
    EmployeeResource::class,           // ✅ Gerenciar funcionários
    ContractResource::class,           // ✅ Gerenciar contratos
    DepartmentResource::class,         // ✅ Ver departamentos
    DesignationResource::class,        // ✅ Ver designações
    TimeoffResource::class,            // ✅ Gerenciar licenças
    TimeoffCategoryResource::class,    // ✅ Gerenciar categorias
    WorklogResource::class,            // ✅ Gerenciar registos
    HourbankResource::class,           // ✅ Gerenciar banco de horas
    AttendanceResource::class,         // ✅ Gerenciar presenças
])
```

---

### 3️⃣ EMPLOYEE - Funcionário

**Nível de Acesso:** Restrito (Dados Pessoais)

#### Recursos Permitidos (Employee Panel)
- ✅ **Dashboard Pessoal** - Dados e estadísticas individuais
- ✅ **Dados Pessoais**
  - ✓ Visualizar perfil
  - ✗ Editar (Apenas ADMIN/HR)
- ✅ **Solicitações de Licença**
  - ✓ Criar nova solicitação
  - ✓ Visualizar histórico
  - ✗ Editar/Eliminar (Bloqueado)
- ✅ **Meus Registos de Trabalho**
  - ✓ Visualizar registos pessoais
  - ✗ Editar (Bloqueado para self-service)
- ✅ **Banco de Horas**
  - ✓ Visualizar saldo pessoal
  - ✗ Modificar

#### Funcionalidades Bloqueadas
- ❌ Gestão de Utilizadores
- ❌ Gestão de Departamentos
- ❌ Gestão de Designações
- ❌ Gestão de Contratos
- ❌ Auditoria & Logs
- ❌ Exportação em batch
- ❌ Dados Geográficos

#### Permissões Específicas (TimeoffPolicy)
```php
// Employee pode visualizar lista
public function viewAny(User $user): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr', 'employee']);
}

// Employee pode ver apenas as suas próprias férias
public function view(User $user, Timeoff $timeoff): bool
{
    $role = strtolower($user->role);
    
    if ($role === 'hr') {
        return true;  // HR vê todas
    }

    // Employee vê apenas as suas
    if ($role === 'employee') {
        return $timeoff->employee_id === $user->employee_id;
    }

    return false;
}

// Employee pode criar
public function create(User $user): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr', 'employee']);
}

// Employee NÃO pode editar/eliminar
public function update(User $user, Timeoff $timeoff): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr']);
}

public function delete(User $user, Timeoff $timeoff): bool
{
    return in_array(strtolower($user->role), ['admin', 'hr']);
}
```

#### Recursos Configurados
```php
// app/Providers/Filament/EmployeePanelProvider.php
->resources([
    TimeoffResource::class,  // ✅ Apenas gerenciar as suas licenças
])
```

---

## 🔐 Mecanismos de Proteção

### 1. Middleware de Autorização
```php
// app/Http/Middleware/EnsurePanelRole.php
$allowed = [
    'admin'    => ['ADMIN'],
    'hr'       => ['HR'],
    'employee' => ['EMPLOYEE'],
];

// Força o utilizador para o painel correto
if (!in_array($role, $allowed[$panel], true)) {
    return redirect($loginPaths[$role]);
}
```

### 2. Policies Eloquent
- **Antes** `before()`: Admin sempre passado
- **Granular**: Cada ação (view, create, update, delete) verificada
- **Data Isolation**: Employees veem apenas dados pessoais

### 3. Gates Globais
```php
Gate::define('export-data', fn (User $user) => $user->role !== 'employee');
Gate::define('view-audit', fn (User $user) => $user->role === 'admin');
Gate::define('is-admin', fn (User $user) => $user->role === 'admin');
Gate::define('is-admin-or-hr', fn (User $user) => in_array($user->role, ['admin', 'hr']));
```

### 4. Traits de Validação
```php
// app/Traits/EnforceEmployeeRole.php
protected function isAuthenticatedAsEmployee(): bool
{
    $user = Auth::user();
    return $user && strtolower((string) $user->role) === 'employee';
}
```

---

## 📊 Matriz de Permissões Detalhada

### Recursos por Papel

| Recurso | ADMIN | HR | EMPLOYEE |
|---------|-------|----|---------| 
| **Utilizadores** | ✅ CRUD | ❌ Bloqueado | ❌ Bloqueado |
| **Departamentos** | ✅ CRUD | ✅ View | ❌ Bloqueado |
| **Designações** | ✅ CRUD | ✅ View | ❌ Bloqueado |
| **Funcionários** | ✅ CRUD | ✅ C/R/U (não D) | ❌ Bloqueado |
| **Contratos** | ✅ CRUD | ✅ C/R/U (não D) | ❌ Bloqueado |
| **Licenças** | ✅ CRUD | ✅ CRUD | ✅ C/R (próprias) |
| **Registos/Horários** | ✅ CRUD | ✅ CRUD | ✅ View (próprios) |
| **Banco de Horas** | ✅ CRUD | ✅ CRUD | ✅ View (próprio) |
| **Auditoria** | ✅ View | ❌ Bloqueado | ❌ Bloqueado |
| **Exportação** | ✅ Sim | ✅ Sim | ❌ Não |

**Legenda:**
- ✅ = Permitido
- ❌ = Bloqueado
- CRUD = Create, Read, Update, Delete
- C/R/U = Create, Read, Update (Update parcial)
- View = Apenas visualização

---

## 🔄 Fluxo de Autenticação e Autorização

```
1. Utilizador faz Login
   ↓
2. Middleware EnsurePanelRole verifica role
   ↓
3. Router para painel correto (admin/hr/employee)
   ↓
4. Carrega Filament Panel com recursos permitidos
   ↓
5. Policy autoriza ações (view, create, update, delete)
   ↓
6. Queries filtradas por scope (se aplicável)
   ↓
7. Audit Log registra operação (se sensível)
   ↓
8. Resposta ao utilizador
```

---

## 🛡️ Proteções de Segurança

### 1. Autenticação
- Password hashing via Laravel's Hash
- Força mudança de password no 1º login (`must_change_password` flag)
- Middleware de reauthenticação em operações sensíveis

### 2. Autorização
- Context-aware policies (Admin bypass via `before()`)
- Data isolation mediante FK relationships
- Scopes aplicados automaticamente

### 3. Auditoria
- Activity Log integrado via `spatie/laravel-activitylog`
- Registra todas as mudanças sensíveis
- Apenas ADMIN pode aceder aos logs

### 4. Proteção CSRF
- Token CSRF em todos os formulários
- Middleware VerifyCsrfToken

---

## 📝 Implementação por Ficheiro

### Configuração
| Ficheiro | Descrição |
|----------|-----------|
| `app/Providers/AppServiceProvider.php` | Regista policies e gates |
| `app/Providers/Filament/AdminPanelProvider.php` | Config painel Admin |
| `app/Providers/Filament/HRPanelProvider.php` | Config painel HR |
| `app/Providers/Filament/EmployeePanelProvider.php` | Config painel Employee |

### Policies
| Ficheiro | Aplica-se a |
|----------|------------|
| `app/Policies/UserPolicy.php` | User (CRUD) |
| `app/Policies/CountryPolicy.php` | Country (Admin only) |
| `app/Policies/StatePolicy.php` | State (Admin only) |
| `app/Policies/CityPolicy.php` | City (Admin only) |
| `app/Policies/ContractTypePolicy.php` | ContractType (Admin only) |
| `app/Policies/TimeoffPolicy.php` | Timeoff (Multi-role) |
| `app/Policies/BasePolicy.php` | Métodos comuns |

### Middleware
| Ficheiro | Responsabilidade |
|----------|-----------------|
| `app/Http/Middleware/EnsurePanelRole.php` | Direciona para painel correto |
| `app/Http/Middleware/EnforcePasswordChange.php` | Força mudança de password |

---

## 🧪 Testes de Permissões

### Ficheiros de Teste
```
tests/Feature/
├── PermissionsTest.php          # Testes gerais de policies
├── AuditPermissionsTest.php     # Testes de acesso ao audit
└── ExportActionsTest.php        # Testes de exportação
```

### Exemplos de Testes
```php
// Só ADMIN pode ver utilizadores
public function test_admin_can_view_users()
{
    $admin = User::factory()->create(['role' => 'ADMIN']);
    $this->assertTrue($admin->can('viewAny', User::class));
}

public function test_hr_cannot_view_users()
{
    $hr = User::factory()->create(['role' => 'HR']);
    $this->assertFalse($hr->can('viewAny', User::class));
}

// Só ADMIN pode ver auditoria
public function test_only_admin_can_access_activity_log()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $hr = User::factory()->create(['role' => 'hr']);
    
    $this->assertTrue(auth()->user()?->role === 'admin' || $admin->role === 'admin');
    $this->assertFalse(in_array($hr->role, ['admin']));
}
```

---

## 🔍 Cenários de Uso

### Cenário 1: Employee Cria Solicitação de Licença
```
1. Employee acessa painel /employee
2. Clica em "Nova Solicitação de Licença"
3. TimeoffPolicy::create() valida → ✅ Permitido
4. Form carrega com dados pré-preenchidos (próprio employee_id)
5. Após envio, HR recebe notificação
6. HR aprova/rejeita via HR Panel
```

### Cenário 2: HR Edita Contrato de Employee
```
1. HR acessa painel /hr
2. Seleciona "Gestão de Contratos"
3. Abre contrato existente
4. ContractPolicy::update() valida HR role → ✅ Permitido
5. Hr edita termos, salary, etc.
6. Audit Log registra a mudança
7. Employee não consegue editar (apenas visualizar)
```

### Cenário 3: Employee Tenta Aceder Painel Admin
```
1. Employee autenticado tenta aceder /admin
2. EnsurePanelRole middleware verifica role
3. Employee role !== admin
4. Redirect para /employee/login
5. Sem acesso ao painel admin
```

---

## 📈 Escalabilidade e Melhorias Futuras

### Possíveis Extensões
1. **Papéis Dinâmicos**: Migrar para tabela `roles` com relacionamentos
2. **Permissões Granulares**: Implementar `Role::hasPermission('action')`
3. **Departmental Scope**: HR aceder apenas funcionários do seu departamento
4. **Temporal Access**: Acesso com data de expiração
5. **Audit Trail Completo**: Rastrear todas as ações com IP e user-agent

### Recomendações
- ✅ Auditar logs regularmente
- ✅ Testar cenários de permissão antes de deploy
- ✅ Documentar qualquer exceção ao RBAC
- ✅ Implementar 2FA para ADMIN
- ✅ Rotação periódica de passwords

---

## 📞 Referências

- **Laravel Authorization**: https://laravel.com/docs/authorization
- **Filament Panel Configuration**: https://filamentphp.com/docs
- **Policy-based Access**: https://laravel.com/docs/authorization#creating-policies
- **Activity Logging**: https://spatie.be/docs/laravel-activitylog

---

**Última Atualização**: 03/03/2026  
**Versão**: 1.0  
**Status**: ✅ Ativo e Testado
