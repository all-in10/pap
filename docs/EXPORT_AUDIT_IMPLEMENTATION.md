# Sistema de Exportação e Auditoria - Guia de Implementação

## 📋 Resumo Executivo

Foram implementadas duas funcionalidades principais no projeto PAP:

### 1. **Exportação em Múltiplos Formatos (CSV, Excel, JSON)**
- Disponível apenas para usuários **Admin** e **HR**
- Bloqueada para usuários **Employee**
- Integrada em todos os resources Filament principais

### 2. **Sistema de Auditoria Completo**
- Rastreamento automático de **CRUD** (Create, Read, Update, Delete)
- Rastreamento de **Login/Logout**
- Rastreamento de **campos sensíveis** (salários, NSS, NIF, etc)
- Dashboard de auditoria acessível apenas para **Admins**
- Rastreamento de quem realizou cada ação

---

## 🚀 Funcionalidades Implementadas

### A. Exportação de Dados

#### Formatos Suportados:
- **CSV**: Formato de texto simples, compatível com Excel e Sheets
- **Excel**: Planilhas .xlsx com formatação
- **JSON**: Formato estruturado para integração com sistemas

#### Resources com Exportação:
- ✅ Funcionários (Employee)
- ✅ Contratos (Contract)
- ✅ Presenças (Attendance)
- ✅ Licenças (Timeoff)
- ✅ Benefícios (Benefit)
- ✅ Registos de Trabalho (Worklog)
- ✅ Banco de Horas (Hourbank)

#### Como Usar:

**Via Interface Filament:**
1. Acesse qualquer resource (ex: Funcionários)
2. Clique em um dos botões no topo:
   - 📊 Exportar CSV
   - 📈 Exportar Excel
   - 📄 Exportar JSON

**Via URL (Routes):**
```
GET /export/Employee/csv
GET /export/Employee/excel
GET /export/Employee/json
GET /export/Contract/csv
GET /export/Attendance/csv
# etc...
```

**Via Código:**
```php
// Controller ou Job
use App\Http\Controllers\ExportController;

// CSV
route('export.csv', 'Employee');

// Excel
route('export.excel', 'Contract');

// JSON
route('export.json', 'Attendance');
```

---

### B. Sistema de Auditoria

#### Rastreamento Automático de:

1. **Operações CRUD**
   - ✅ Criação de registos
   - ✅ Atualização de fields
   - ✅ Deleção de registos

2. **Eventos de Autenticação**
   - ✅ Login de usuários
   - ✅ Logout de usuários

3. **Campos Sensíveis** (sempre registados)
   - `nss`, `nif`, `email`, `phone_number`
   - `date_of_birth`, `salary`, `role`
   - `start_time`, `end_time`, `password`

#### Como Acessar:

**Dashboard de Auditoria (Admin Only):**
1. Acesse: `/admin/activity-logs`
2. Visualize todos os logs com filtros por:
   - Tipo de Evento (Created, Updated, Deleted, Login, Logout)
   - Usuário que realizou a ação
   - Modelo/Resource afetado
   - Data/Hora da operação

**Estrutura de Dados Registada:**
```json
{
  "id": 1,
  "log_name": "default",
  "event": "created",
  "description": "Funcionário João Silva criado",
  "subject_type": "App\\Models\\Employee",
  "subject_id": 5,
  "causer_type": "App\\Models\\User",
  "causer_id": 1,
  "causer_name": "Admin User",
  "properties": {
    "old": {...},
    "attributes": {...}
  },
  "created_at": "2026-02-14 10:30:00"
}
```

---

## 🔒 Controle de Acesso

### Permissões por Funcionalidade:

| Funcionalidade | Admin | HR | Employee |
|---|---|---|---|
| Exportar CSV | ✅ | ✅ | ❌ |
| Exportar Excel | ✅ | ✅ | ❌ |
| Exportar JSON | ✅ | ✅ | ❌ |
| Ver Dashboard Auditoria | ✅ | ❌ | ❌ |
| Visualizar Logs | ✅ | ❌ | ❌ |
| Editar Logs | ❌ | ❌ | ❌ |
| Deletar Logs | ✅ | ❌ | ❌ |

### Gates Definidas:

```php
Gate::define('export-data', fn (User $user) => $user->role !== 'employee');
Gate::define('view-audit', fn (User $user) => $user->role === 'admin');
Gate::define('is-admin', fn (User $user) => $user->role === 'admin');
Gate::define('is-admin-or-hr', fn (User $user) => in_array($user->role, ['admin', 'hr']));
```

---

## 📁 Arquivos Criados/Modificados

### Novos Arquivos:

```
app/
├── Traits/
│   ├── RecordsActivity.php          # Trait para rastreamento automático
│   └── ExportableTrait.php          # Trait para exportação (opcional)
├── Listeners/
│   ├── RecordLoginActivity.php      # Listener para logins
│   └── RecordLogoutActivity.php     # Listener para logouts
├── Policies/
│   └── BasePolicy.php               # Base com métodos comuns
├── Filament/
│   ├── Resources/
│   │   └── ActivityLogResource.php  # Resource de auditoria
│   ├── Actions/
│   │   └── ExportAction.php         # Actions de exportação
│   └── Resources/ActivityLogResource/Pages/
│       ├── ListActivityLogs.php
│       └── ViewActivityLog.php
├── Http/
│   ├── Controllers/
│   │   └── ExportController.php     # Controller de exportação
│   └── Middleware/
│       └── (existentes)
└── Providers/
    ├── EventServiceProvider.php     # Event mapping
    └── (modificado) AppServiceProvider.php
    
config/
└── activitylog.php                  # Configuração Spatie

database/
└── migrations/
    └── 2026_02_14_000001_.../activity_log_table.php

tests/Feature/
├── ExportActionsTest.php            # Testes exportação
├── AuditLoggingTest.php             # Testes auditoria
└── AuditPermissionsTest.php         # Testes permissões

routes/
└── web.php                          # Routes de export (adicionadas)
```

### Modelos Modificados:

```
app/Models/
├── Employee.php                     # + use RecordsActivity
├── User.php                         # + use RecordsActivity
├── Contract.php                     # + use RecordsActivity
├── Attendance.php                   # + use RecordsActivity
├── Timeoff.php                      # + use RecordsActivity
├── Benefit.php                      # + use RecordsActivity
├── Worklog.php                      # + use RecordsActivity
└── Hourbank.php                     # + use RecordsActivity
```

### Pages Filament Modificadas:

```
app/Filament/Resources/*/Pages/List*.php
├── ListEmployees.php                # + export actions
├── ListContracts.php                # + export actions
├── ListAttendances.php              # + export actions
├── ListTimeoffs.php                 # + export actions
├── ListBenefits.php                 # + export actions
├── ListWorklogs.php                 # + export actions
└── ListHourbanks.php                # + export actions
```

---

## 🧪 Testes

### Executar Todos os Testes:
```bash
php artisan test tests/Feature/ExportActionsTest.php
php artisan test tests/Feature/AuditLoggingTest.php
php artisan test tests/Feature/AuditPermissionsTest.php
```

### Testes Implementados:

**ExportActionsTest.php:**
- Admin pode exportar para CSV/Excel/JSON
- HR pode exportar para CSV/Excel/JSON
- Employee não pode exportar
- Usuários desautenticados são redirecionados

**AuditLoggingTest.php:**
- Criação de registro cria log
- Atualização cria log com mudanças
- Deleção cria log
- Campos sensíveis são rastreados
- Login/Logout geram logs

**AuditPermissionsTest.php:**
- Apenas admin acessa activity logs
- HR não acessa activity logs
- Employee não acessa activity logs
- Gates funcionam corretamente

---

## 🔧 Configurações

### arquivo: config/activitylog.php

```php
'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),
'delete_records_older_than_days' => 365,  // Limpar logs com >1 ano
'tracked_models' => [
    App\Models\Employee::class,
    App\Models\Contract::class,
    App\Models\Attendance::class,
    App\Models\Timeoff::class,
    App\Models\Benefit::class,
    App\Models\Worklog::class,
    App\Models\Hourbank::class,
    App\Models\User::class,
],
'sensitive_fields' => [
    'nss', 'nif', 'email', 'phone_number', 'date_of_birth',
    'salary', 'start_date', 'end_date', 'role', 'password',
    'start_time', 'end_time', 'break_start', 'break_end',
]
```

---

## 📊 Database

### Nova Tabela: activity_log

```sql
CREATE TABLE activity_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    log_name VARCHAR(255) NULLABLE,
    description TEXT NULLABLE,
    subject_id BIGINT UNSIGNED NULLABLE,
    subject_type VARCHAR(255) NULLABLE,
    causer_id BIGINT UNSIGNED NULLABLE,
    causer_type VARCHAR(255) NULLABLE,
    event VARCHAR(255) NULLABLE,
    properties JSON NULLABLE,
    batch_uuid VARCHAR(255) NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX log_name (log_name),
    INDEX subject (subject_id, subject_type),
    INDEX causer (causer_id, causer_type)
);
```

---

## 🚨 Troubleshooting

### Problema: Exports não funcionam
**Solução:**
```bash
php artisan cache:clear
php artisan config:clear
composer dump-autoload
```

### Problema: Activity logs vazios
**Verificar:**
```bash
# Confirmar que trait foi adicionado aos models
grep -r "use RecordsActivity" app/Models/

# Confirmar que migration foi executada
php artisan migrate:status | grep activity_log

# Testar manualmente
php artisan tinker
>>> activity()->log('Test log');
```

### Problema: Permissões de exportação não funcionam
**Verificar:**
```bash
# Confirmar role do usuário
php artisan tinker
>>> User::find(1)->role
>>> User::find(1)->update(['role' => 'admin'])
```

---

## 📈 Estatísticas de Implementação

| Métrica | Valor |
|---|---|
| Novos Arquivos | 16 |
| Modelos Modificados | 8 |
| Migrations Criadas | 1 |
| Testes Criados | 3 |
| Routes Adicionadas | 3 |
| Listeners Criados | 2 |
| Traits Criados | 2 |
| Resources Modificadas | 7 |

---

## ✅ Checklist de Validação

- [x] Exportação CSV funciona para admin/HR
- [x] Exportação Excel funciona para admin/HR
- [x] Exportação JSON funciona para admin/HR
- [x] Employees não conseguem exportar
- [x] Activity logs são criados automaticamente
- [x] Logins/Logouts são registados
- [x] Dashboard de auditoria é acessível apenas por admin
- [x] Campos sensíveis são rastreados
- [x] Gates de permissões funcionam
- [x] Testes cobrem principais cenários

---

## 🔮 Melhorias Futuras

1. **Exportação Agendada**
   - Agendar exports periódicos
   - Enviar por email

2. **Dados Filtrados**
   - Exportar apenas registos selecionados
   - Filtros por data, departamento, etc

3. **Compressão**
   - Zip múltiplos exports
   - Compactação automática

4. **Webhooks de Auditoria**
   - Notificações em tempo real
   - Integração com sistemas externos

5. **Graphs e Analytics**
   - Dashboard visual de atividades
   - Relatórios gráficos

---

## 📞 Suporte

Para dúvidas sobre implementação, consulte:
- Documentação Spatie Activity Log: https://spatie.be/docs/laravel-activitylog
- Documentação Maatwebsite Excel: https://docs.laravel-excel.com
- Documentação Filament: https://filamentphp.com/docs

---

**Data de Implementação:** 14 de Fevereiro de 2026  
**Versão:** 1.0.0  
**Status:** ✅ Completo e Testado
