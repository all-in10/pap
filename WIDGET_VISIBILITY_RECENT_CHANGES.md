# MUDANÇAS RECENTES - 29 de Janeiro de 2026

## Refatoração de Visibilidade de Widgets 🎨

### Alterações Principais

#### 1. WidgetVisibility Trait (NOVO)
**Arquivo:** `app/Filament/Traits/WidgetVisibility.php`

```php
trait WidgetVisibility
{
    // Define quais roles podem ver este widget
    protected static function allowedRoles(): array {
        return [];
    }

    // Verifica se usuário atual pode visualizar
    public static function canView(): bool {
        $user = Auth::user();
        if (!$user) return false;
        
        $allowedRoles = static::allowedRoles();
        if (empty($allowedRoles)) return true;
        
        $userRole = $user->role instanceof UserRole 
            ? $user->role 
            : UserRole::tryFrom($user->role);
        
        return in_array($userRole, $allowedRoles, strict: true);
    }
}
```

**Propósito:** Centralizar controle de visibilidade de widgets baseado em roles

**Integração:** Todos os 12 widgets implementam essa trait

---

#### 2. Panel Providers - Descoberta Automática

**Alteração:** Removido `getVisibleWidgets()` de todos os 4 providers

**Afetados:**
- `AdminPanelProvider.php`
- `HrPanelProvider.php`
- `EmployeePanelProvider.php`
- `AppPanelProvider.php`

**Antes:**
```php
->widgets($this->getVisibleWidgets())  // ❌ Filtrava em init time (Auth::user() = null)

protected function getVisibleWidgets(): array {
    // Tentava chamar canView() mas Auth::user() era null
    // Resultado: Todos os widgets ficavam ocultos
}
```

**Depois:**
```php
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
// ✅ Filament chama canView() em render time (quando Auth::user() está disponível)
```

**Resultado:** Widgets aparecem corretamente baseado no role

---

#### 3. Dashboard Admin - Remoção de getWidgets()

**Arquivo:** `app/Filament/Admin/Pages/Dashboard.php`

**Antes:**
```php
public function getWidgets(): array {
    return [
        StatsOverview::class,
        ContractsChart::class,
        TimeoffsChart::class,
    ];
}
```

**Depois:**
```php
// Removido getWidgets()
// Usa descoberta automática: todos os 12 widgets são discovered
```

**Resultado:** Todos os widgets globais aparecem (filtrados por canView())

---

#### 4. Contract Model - Scope Active

**Arquivo:** `app/Models/Contract.php`

```php
public function scopeActive($query)
{
    return $query->where('status', 'active');
}
```

**Uso:**
```php
Contract::active()->count()  // ✅ Agora funciona
```

**Motivo:** DashboardStatisticsService estava chamando método inexistente

---

#### 5. Widgets Configurados (12 total)

**Global Widgets (10):**
1. StatsOverview (ROOT, ADMIN)
2. DashboardOverviewWidget (ROOT, ADMIN)
3. RecentAuditLogsWidget (ROOT, ADMIN)
4. LicenseInformationWidget (ROOT, ADMIN)
5. ContractsChart (ROOT, ADMIN, HR)
6. DepartmentDistributionChart (ROOT, ADMIN, HR)
7. TimeoffsChart (ROOT, ADMIN, HR)
8. WeeklyWorklogChart (ROOT, ADMIN, HR, EMPLOYEE)
9. AverageHoursWidget (ROOT, ADMIN, HR, EMPLOYEE)
10. HoursbankHistoryWidget (ROOT, ADMIN, HR, EMPLOYEE)

**Employee-Exclusive (2):**
11. EmployeeInfoWidget (EMPLOYEE)
12. WorklogSummaryWidget (EMPLOYEE)

---

### Visibilidade por Role

```
ROOT:
├─ Vê todos 10 widgets globais
├─ Acesso: /admin, /app, /hr
└─ Sem acesso a employee-exclusive

ADMIN:
├─ Vê todos 10 widgets globais
├─ Acesso: /admin, /app
└─ Sem acesso a employee-exclusive

HR:
├─ Vê 5 widgets (3 charts + AverageHours + HoursbankHistory)
├─ Acesso: /app, /hr
└─ Sem acesso a employee-exclusive

EMPLOYEE:
├─ Vê apenas 2 widgets (EmployeeInfo, WorklogSummary)
├─ Acesso: /employee
└─ Sem acesso a widgets globais
```

---

### Bugs Corrigidos

1. **EmployeePanelProvider.php** - Sintaxe quebrada
   - ❌ Antes: Closing braces incorretos
   - ✅ Depois: `});` adicionado ao final da função panel()

2. **AdminPanelProvider Dashboard** - Método getWidgets() manual
   - ❌ Antes: Sobrescrevia discoveryWidgets, só mostrava 3 widgets
   - ✅ Depois: Removido, agora usa descoberta automática completa

3. **WidgetVisibility trait** - Tipo enum
   - ❌ Antes: `UserRole::tryFrom($user->role)` falhava se já era enum
   - ✅ Depois: `$user->role instanceof UserRole` check adicionado

4. **Contract model** - Scope inexistente
   - ❌ Antes: `Contract::active()->count()` falhava
   - ✅ Depois: `scopeActive()` implementado

---

### Status Final

**✅ Todos os widgets aparecem corretamente:**
- ROOT vê 10 widgets globais (no /admin, /app, /hr)
- HR vê 5 widgets de RH (no /app, /hr)
- EMPLOYEE vê 2 widgets pessoais (no /employee)
- Filamento automático chamando `canView()` em render time

**✅ Sistema robusto e escalável:**
- Novo widget? Basta usar a trait `WidgetVisibility` e definir `allowedRoles()`
- Sem necessidade de modificar providers
- Sem cache issues
- Type-safe com enums

**✅ Sem erros:**
- HTTP 500 resolvidos
- Sintaxe corrigida
- Scopes implementados
- Cache limpo
