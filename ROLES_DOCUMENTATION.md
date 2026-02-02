# Sistema de Roles - Documentação Completa

## 📋 Hierarquia de Roles

```
ROOT (Super Admin)
  ↓
ADMIN (Administrador)
  ↓
HR (Recursos Humanos)
  ↓
EMPLOYEE (Funcionário)
```

Cada nível superior herda automaticamente as permissões do nível inferior.

---

## 🔐 Implementação Técnica

### 1. **Enum (Type-Safe)**
`App\Enums\UserRole`

```php
UserRole::ROOT      // 'root'
UserRole::ADMIN     // 'admin'
UserRole::HR        // 'hr'
UserRole::EMPLOYEE  // 'employee'
```

#### Métodos do Enum:
- `hierarchy()` - Retorna array em ordem crescente de privilégios
- `hasPrivilegeOf(UserRole $role)` - Verifica hierarquia
- `label()` - Label em português
- `manageable()` - Todos os roles

---

### 2. **Modelo User**
`App\Models\User`

#### Métodos de Verificação:
```php
$user->isRoot()                          // boolean
$user->isAdmin()                         // boolean 
$user->isHr()                            // boolean 
$user->isEmployee()                      // boolean
$user->hasPrivilegeOf(UserRole $role)    // boolean
```

#### Relacionamento:
```php
$user->employee()  // hasOne Employee
```

---

### 3. **Serviço Access**
`App\Services\Access`

Centraliza toda a lógica de autorização:

```php
// Verificar role específico (suporta string ou enum)
Access::hasRole('hr', $user)                    // boolean
Access::hasRole(UserRole::ADMIN)                // boolean

// Aliases convenientes
Access::isRoot($user)                           // boolean
Access::isAdmin($user)                          // boolean
Access::isHr($user)                             // boolean
Access::isEmployeeRole($user)                   // boolean

// Gates
Access::canManageWorklogs($user)                // boolean
```

---

### 4. **Gates (Centralizados)**
`App\Providers\AuthServiceProvider`

Definidos no boot():

```php
Gate::define('manage-worklogs', function (User $user) {
    return $user->isHr() || $user->isAdmin() || $user->isRoot();
});

Gate::define('manage-employees', function (User $user) {
    return $user->isHr() || $user->isAdmin() || $user->isRoot();
});

Gate::define('manage-system', function (User $user) {
    return $user->isAdmin() || $user->isRoot();
});
```

---

### 5. **Políticas (Eloquent Policies)**
`App\Policies\*Policy`

4 políticas implementadas:
- `EmployeePolicy` - Quem pode ver/editar/deletar funcionários
- `ContractPolicy` - Quem pode ver/editar/deletar contratos
- `TimeoffPolicy` - Quem pode requerer/aprovar folgas
- `WorklogPolicy` - Quem pode registrar/editar horas

#### Padrão:
```php
public function view(?User $user, Model $model): bool
{
    if ($user === null) return false;
    if ($user->isRoot()) return true;
    if ($user->isAdmin() || $user->isHr()) return true;
    
    // Employee pode ver apenas seus próprios recursos
    if ($user->isEmployee()) {
        return $model->employee_id === $user->employee?->id;
    }
    return false;
}
```

---

## 📊 Tabela de Permissões

| Ação | Root | Admin | HR | Employee |
|------|------|-------|----|----|
| Ver Funcionários | ✅ | ✅ | ✅ | ❌ (próprio) |
| Criar Funcionário | ✅ | ✅ | ✅ | ❌ |
| Editar Funcionário | ✅ | ✅ | ✅ | ❌ |
| Deletar Funcionário | ✅ | ✅ | ❌ | ❌ |
| Ver Contratos | ✅ | ✅ | ✅ | ❌ (próprio) |
| Criar Contrato | ✅ | ✅ | ✅ | ❌ |
| Registrar Horas | ✅ | ✅ | ✅ | ✅ (próprias) |
| Ver Horas | ✅ | ✅ | ✅ | ✅ (próprias) |
| Editar Horas | ✅ | ✅ | ✅ | ❌ |
| Requisitar Folga | ✅ | ✅ | ✅ | ✅ |
| Aprovar Folga | ✅ | ✅ | ✅ | ❌ |
| Gerenciar Sistema | ✅ | ✅ | ❌ | ❌ |

---

## 🚀 Como Usar

### Em Controllers:
```php
$user = Auth::user();

if ($user->isHr()) {
    // Permitir ação HR
}
```

### Em Views Blade:
```blade
@if(Auth::user()->isAdmin())
    <!-- Mostrar apenas para admins -->
@endif

@can('manage-employees')
    <!-- Usar gate -->
@endcan
```

### No Filament:
```php
Tables\Actions\EditAction::make()
    ->visible(function ($record) {
        return Auth::user()?->can('update', $record);
    })
```

---

## 🔄 Cast em User Model

```php
protected function casts(): array
{
    return [
        'role' => UserRole::class,  // Garante type-safety
    ];
}
```

O role é armazenado como string no banco, mas retorna como Enum.

---

## ✅ Melhores Práticas

1. **Use Enums sempre que possível** para type-safety
2. **Use o Serviço Access** em vez de acessar `User::ROLE_*` diretamente
3. **Use Gates** para lógica genérica reutilizável
4. **Use Policies** para autorização ao nível do modelo
5. **Verifique `$user?->employee?->id`** quando comparar com employee_id

---

## 📝 Exemplo Completo

```php
// Verificar se user pode editar um worklog
$worklog = Worklog::find(1);
$user = Auth::user();

// Via Policy (recomendado)
if ($user?->can('update', $worklog)) {
    // Atualizar
}

// Via Gate
if (Gate::allows('manage-worklogs')) {
    // Atualizar
}

// Via método direto
if ($user?->isHr()) {
    // Atualizar
}
```

---

## 🎯 Resumo

✅ **Type-safe** com Enums
✅ **Centralizado** em Serviço e Gates
✅ **Flexível** com Policies
✅ **Consistente** na hierarquia
✅ **Documentado** e testável
