# RBAC Quick Reference Guide

## 📌 Sumário Executivo

| Papel | Painel | Recursos | Restrições |
|-----:|--------|----------|-----------|
| **ADMIN** | `/admin` | Todos | Nenhuma |
| **HR** | `/hr` | Funcionários, Contratos, Licenças, Registos | Sem gestão de utilizadores/auditoria |
| **EMPLOYEE** | `/employee` | Dados pessoais, Suas licenças, Seus registos | Apenas dados próprios |

---

## 🔑 Keys Files (Essencial Conhecer)

### 1. **Providers (Configuração)**
```
app/Providers/
├── AppServiceProvider.php          ← Gates e Policies
├── Filament/AdminPanelProvider.php    ← Recursos do Admin
├── Filament/HRPanelProvider.php       ← Recursos do HR
└── Filament/EmployeePanelProvider.php ← Recursos do Employee
```

### 2. **Policies (Autorização)**
```
app/Policies/
├── BasePolicy.php                  ← Métodos comuns
├── UserPolicy.php                  ← Apenas ADMIN
├── CountryPolicy.php               ← Apenas ADMIN
├── StatePolicy.php                 ← Apenas ADMIN
├── CityPolicy.php                  ← Apenas ADMIN
├── ContractTypePolicy.php          ← Apenas ADMIN
└── TimeoffPolicy.php               ← Multi-role
```

### 3. **Middleware (Proteção de Rotas)**
```
app/Http/Middleware/
├── EnsurePanelRole.php            ← Valida painel/role
└── EnforcePasswordChange.php      ← Força mudança 1º login
```

---

## 🚀 Como Adicionar Nova Funcionalidade com RBAC

### Passo 1: Definir Policy
```php
// app/Policies/NewFeaturePolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\NewFeature;

class NewFeaturePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        if ($this->isAdmin($user)) return true;
        if ($this->isAdminOrHR($user)) return true;
        return false;
    }

    public function view(User $user, NewFeature $model): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function update(User $user, NewFeature $model): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function delete(User $user, NewFeature $model): bool
    {
        return $this->isAdmin($user); // Apenas ADMIN
    }
}
```

### Passo 2: Registar Policy
```php
// app/Providers/AppServiceProvider.php
use App\Policies\NewFeaturePolicy;
use App\Models\NewFeature;

Gate::policy(NewFeature::class, NewFeaturePolicy::class);
```

### Passo 3: Adicionar Resource a Painel
```php
// app/Providers/Filament/HRPanelProvider.php
->resources([
    // ... outros recursos
    NewFeatureResource::class, // ✅ Adicionar aqui
])
```

### Passo 4: Proteger em Views/Controllers
```php
// Em Blade Template
@can('view', $newFeature)
    {{ $newFeature->name }}
@endcan

// Em Controller
if ($user->cannot('update', $newFeature)) {
    abort(403, 'Não autorizado');
}

// Em Filament Resource
public static function canEdit(Model $record): bool
{
    return auth()->user()?->can('update', $record) ?? false;
}
```

---

## 🔍 Verificações Rápidas

### Verificar Role do Utilizador
```php
// Em qualquer contexto
auth()->user()->role; // 'admin', 'hr', ou 'employee'

// Com comparação
if (auth()->user()->role === 'admin') { ... }

// Com array
if (in_array(auth()->user()->role, ['admin', 'hr'])) { ... }

// Com Trait
if ($this->isAuthenticatedAsEmployee()) { ... }
```

### Verificar Permissão
```php
// Gate
if (Gate::allows('export-data')) { ... }
if (Gate::denies('view-audit')) { ... }

// Policy (Eloquent)
if ($user->can('update', $employee)) { ... }
if ($user->cannot('delete', $employee)) { ... }

// Blade
@if(auth()->user()->can('update', $employee))
    <!-- Mostrar botão editar -->
@endif
```

### Bloquear Acesso
```php
// Em Controller
$this->authorize('view', $employee);  // ✅ Lança 403 se não permitido

// Em Policy Method
return $this->isAdmin($user); // ✅ Auto-lança 403 se false
```

---

## 📊 Matriz de Permissões Rápida

### Operações por Papel

#### ADMIN ✅
| Recurso | Create | Read | Update | Delete |
|---------|--------|------|--------|--------|
| Utilizadores | ✅ | ✅ | ✅ | ✅ |
| Departamentos | ✅ | ✅ | ✅ | ✅ |
| Funcionários | ✅ | ✅ | ✅ | ✅ |
| Contratos | ✅ | ✅ | ✅ | ✅ |
| Licenças | ✅ | ✅ | ✅ | ✅ |
| Auditoria | ❌ | ✅ | ❌ | ❌ |

#### HR 👥
| Recurso | Create | Read | Update | Delete |
|---------|--------|------|--------|--------|
| Funcionários | ✅ | ✅ | ✅ | ❌ |
| Contratos | ✅ | ✅ | ✅ | ❌ |
| Licenças | ✅ | ✅ | ✅ | ✅ |
| Departamentos | ❌ | ✅ | ❌ | ❌ |
| Utilizadores | ❌ | ❌ | ❌ | ❌ |
| Auditoria | ❌ | ❌ | ❌ | ❌ |

#### EMPLOYEE 👤
| Recurso | Create | Read | Update | Delete |
|---------|--------|------|--------|--------|
| Suas Licenças | ✅ | ✅ | ❌ | ❌ |
| Dados Pessoais | ❌ | ✅ | ❌ | ❌ |
| Seus Registos | ❌ | ✅ | ❌ | ❌ |
| Banco Horas | ❌ | ✅ | ❌ | ❌ |
| Resto | ❌ | ❌ | ❌ | ❌ |

---

## 🧪 Testes Rápidos

### Unit Test
```php
public function test_admin_can_create_user()
{
    $admin = User::factory()->create(['role' => 'admin']);
    $this->assertTrue($admin->can('create', User::class));
}

public function test_employee_cannot_create_user()
{
    $employee = User::factory()->create(['role' => 'employee']);
    $this->assertFalse($employee->can('create', User::class));
}
```

### Feature Test
```php
public function test_admin_can_access_audit_log()
{
    $admin = User::factory()->create(['role' => 'admin']);
    
    $this->actingAs($admin)
        ->get('/admin/activity-logs')
        ->assertSuccessful();
}

public function test_employee_blocked_from_audit_log()
{
    $employee = User::factory()->create(['role' => 'employee']);
    
    $this->actingAs($employee)
        ->get('/admin/activity-logs')
        ->assertForbidden();
}
```

---

## 🛡️ Checklist de Segurança

### Antes de Colocar em Produção
- [ ] Toda nova funcionalidade tem Policy definida
- [ ] Policy está registada em AppServiceProvider
- [ ] Resource adicionado apenas aos painéis corretos
- [ ] Testes de permissão passam (admin, hr, employee)
- [ ] Dados sensíveis não são expostos (ex: salários vistos por HR)
- [ ] Employee não consegue aceder dados de outros employees
- [ ] Admin consegue contornar todas as autorizações
- [ ] Audit log registra operações críticas
- [ ] Password change forced no 1º login
- [ ] Middleware EnsurePanelRole redireciona corretamente

---

## 🔗 Fluxo Completo de uma Ação

```
1. Employee submete formulário de Licença
   ↓
2. Middleware valida autenticação ✅
   ↓
3. EnsurePanelRole valida painel (/employee) ✅
   ↓
4. TimeoffPolicy::create() executado
   - Admin: ✅ Permitido (before)
   - HR: ✅ Permitido
   - Employee: ✅ Permitido
   ↓
5. Query executada: Timeoff::create($data)
   - employee_id auto-preenchido com auth()->user()->employee_id
   ↓
6. Audit log registado: "Timeoff criado"
   ↓
7. Notificação enviada para HR
   ↓
8. Response enviado: 200 OK / "Solicitação submetida"
```

---

## ⚠️ Erros Comuns

### ❌ Erro 1: Esquecer Registar Policy
```php
// ERRADO - Policy nunca é acionada
Gate::policy(NewModel::class, NewModelPolicy::class);
// Falta adicionar a linha acima!

// CORRETO
Gate::policy(NewModel::class, NewModelPolicy::class);
```

### ❌ Erro 2: Esquecer Autorização em Controller
```php
// ERRADO - Qualquer um consegue aceder
public function destroy(Employee $employee)
{
    $employee->delete(); // Sem verificação!
    return back();
}

// CORRETO
public function destroy(Employee $employee)
{
    $this->authorize('delete', $employee); // ✅ Valida
    $employee->delete();
    return back();
}
```

### ❌ Erro 3: Não Filtrar Dados
```php
// ERRADO - Employee vê todas as licenças
$timeoffs = Timeoff::all();

// CORRETO - Employee vê apenas as suas
$timeoffs = Timeoff::where('employee_id', auth()->user()->employee_id)->get();
```

### ❌ Erro 4: Confundir Role String
```php
// ERRADO - Role é lowercase na DB
if ($user->role === 'ADMIN') { ... }

// CORRETO
if ($user->role === 'admin') { ... }

// TAMBÉM CORRETO (case-insensitive)
if (strtolower($user->role) === 'admin') { ... }
```

---

## 📞 Links & Recursos

- **Policy Documentation**: https://laravel.com/docs/authorization
- **Filament Authorization**: https://filamentphp.com/docs
- **Activity Log**: https://spatie.be/docs/laravel-activitylog
- **Database Migrations**: `/database/migrations/`
- **Test Suite**: `/tests/Feature/`

---

## 🎯 Próximos Passos

1. **Implementar 2FA** para ADMIN
2. **Adicionar Departmental Scoping** para HR (HR apenas vê seu departamento)
3. **Temporal Access** - Permissões com data de validade
4. **Role-based Reports** - Relatórios específicos por papel
5. **API Authentication** - OAuth2 para integrações

---

**Last Updated**: 03/03/2026  
**Maintainers**: Development Team  
**Questions?** Consulta [RBAC_ANALYSIS.md](RBAC_ANALYSIS.md)
