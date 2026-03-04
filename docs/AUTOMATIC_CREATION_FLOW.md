# 🔄 Fluxo Visual de Criação Automática: Employee → User → Contract → Hourbank

**Versão:** 1.0  
**Data:** 2026-03-04  
**Status:** ✅ Documentado

---

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Fluxo Detalhado](#fluxo-detalhado)
3. [Entidades e Atributos](#entidades-e-atributos)
4. [Mecanismo de Trigger](#mecanismo-de-trigger)
5. [Tratamento de Erros](#tratamento-de-erros)
6. [Cache para Notificações](#cache-para-notificações)
7. [Validações e Regras](#validações-e-regras)
8. [Exemplo Prático](#exemplo-prático)

---

## 📊 Visão Geral

O sistema implementa um fluxo **cascata automático** onde a criação de um **Employee** (Funcionário) dispara automaticamente a criação de 3 entidades relacionadas:

```
Employee Created
    ↓
[Observer Triggered]
    ↓
├─→ User (Conta de Acesso)
├─→ Contract (Contrato de Trabalho)
└─→ Hourbank (Banco de Horas)
```

**Responsável:** `App\Observers\EmployeeObserver`  
**Trigger:** Evento Eloquent `created()` do Model Employee  
**Local:** `/app/Observers/EmployeeObserver.php`

---

## 🔄 Fluxo Detalhado

### 1️⃣ EMPLOYEE CRIADO

**Disparador:** Filament Resource ou Console Command

```php
// Dados de entrada
$employee = Employee::create([
    'first_name' => 'João',
    'last_name' => 'Silva',
    'email' => 'joao.silva@example.com',
    'date_hired' => '2025-01-15',
    'designation_id' => 1,
    // ... outros campos
]);

// Observer Listen: EmployeeObserver::created()
```

---

### 2️⃣ USER CRIADO AUTOMATICAMENTE

**Condição:** Email do Employee não existe na tabela Users

**Atributos Definidos:**

| Atributo | Valor | Origem |
|----------|-------|--------|
| `name` | Concatenado | `first_name` + `last_name` |
| `email` | Copiado | `employee.email` |
| `password` | Hashed | `env('DEFAULT_USER_PASSWORD')` |
| `role` | Fixo | `'employee'` |
| `employee_id` | FK | `employee.id` |
| `must_change_password` | Flag | `true` |

**Lógica:**

```php
$existingUser = User::where('email', $employee->email)->first();

if (!$existingUser) {
    $user = User::create([
        'name' => trim($employee->first_name . ' ' . $employee->last_name),
        'email' => $employee->email,
        'password' => Hash::make(env('DEFAULT_USER_PASSWORD', 'ChangeMe123!')),
        'role' => 'employee',
        'employee_id' => $employee->id,
        'must_change_password' => true,
    ]);
    
    // Guardar no Cache para notificação
    cache()->put("employee_{$employee->id}_created_user", [
        'id' => $user->id,
        'email' => $user->email,
        'role' => $user->role,
    ], now()->addMinutes(5));
}
```

**Resultado:**

- ✅ Usuário criado com acesso automático
- 🔐 Senha definida como `DEFAULT_USER_PASSWORD`
- 🔄 Usuário obrigado a mudar senha no primeiro login
- 📧 Email igual ao do Employee para fácil identificação

---

### 3️⃣ CONTRACT CRIADO AUTOMATICAMENTE

**Condição:** Sempre criado após Employee

**Atributos Definidos:**

| Atributo | Valor | Origem |
|----------|-------|--------|
| `contract_type_id` | FK | `ContractType` com nome `'sem_termo'` |
| `salary` | Decimal | `designation.base_salary` |
| `start_date` | Data | `employee.date_hired` |
| `date_hired` | Data | `employee.date_hired` |
| `status` | String | `'active'` |

**Lógica:**

```php
$baseSalary = $employee->designation ? $employee->designation->base_salary : 0;
$contractType = ContractType::firstWhere('name', 'sem_termo');

$contract = $employee->contracts()->create([
    'contract_type_id' => $contractType->id ?? null,
    'salary' => $baseSalary,
    'start_date' => $employee->date_hired,
    'date_hired' => $employee->date_hired,
    'status' => 'active',
]);

// Guardar no Cache para notificação
cache()->put("employee_{$employee->id}_created_contract", [
    'id' => $contract->id,
    'contract_type' => $contractType->name ?? 'N/A',
    'salary' => $baseSalary,
    'start_date' => $employee->date_hired?->format('d/m/Y'),
], now()->addMinutes(5));
```

**Resultado:**

- 📋 Contrato indefinido (`sem_termo`)
- 💰 Salário baseado na Designação
- ✅ Contrato ativado imediatamente
- 📅 Data início = Data contratação do Employee

---

### 4️⃣ HOURBANK CRIADO AUTOMATICAMENTE

**Condição:** Sempre criado após Contract

**Atributos Definidos:**

| Atributo | Valor | Origem |
|----------|-------|--------|
| `balance_hours` | Float | `0` (Inicializado vazio) |
| `last_accrual_date` | Data | `employee.date_hired` |

**Lógica:**

```php
$hourbank = $employee->hourbanks()->create([
    'balance_hours' => 0,
    'last_accrual_date' => $employee->date_hired ?? now(),
]);

// Guardar no Cache para notificação
cache()->put("employee_{$employee->id}_created_hourbank", [
    'id' => $hourbank->id,
    'balance_hours' => $hourbank->balance_hours,
    'last_accrual_date' => $hourbank->last_accrual_date->format('d/m/Y'),
], now()->addMinutes(5));
```

**Resultado:**

- 🏦 Registro de banco de horas criado
- 🔄 Saldo inicial = 0 horas
- 📅 Última acumulação = Data contratação
- 📊 Pronto para acumular horas automaticamente

---

## 📊 Entidades e Atributos

### Employee

```php
[
    'id'          => 1,
    'first_name'  => 'João',
    'last_name'   => 'Silva',
    'email'       => 'joao.silva@example.com',
    'nif'         => '123456789',
    'nss'         => '987654321',
    'phone_number'=> '+55 11 99999-9999',
    'date_of_birth' => '1990-05-15',
    'date_hired'  => '2025-01-15',
    'is_active'   => true,
    'department_id' => 2,
    'designation_id' => 3,
]
```

### User (Criado Automático)

```php
[
    'id'        => 1,
    'name'      => 'João Silva',
    'email'     => 'joao.silva@example.com',
    'password'  => '$2y$12$...',  // Hashed
    'role'      => 'employee',
    'employee_id' => 1,
    'must_change_password' => true,
]
```

### Contract (Criado Automático)

```php
[
    'id'              => 1,
    'employee_id'     => 1,
    'contract_type_id' => 2,     // sem_termo
    'salary'          => 2500.00,
    'start_date'      => '2025-01-15',
    'end_date'        => null,   // Indefinido
    'status'          => 'active',
]
```

### Hourbank (Criado Automático)

```php
[
    'id'              => 1,
    'employee_id'     => 1,
    'balance_hours'   => 0.0,
    'last_accrual_date' => '2025-01-15',
]
```

---

## 🎯 Mecanismo de Trigger

### Como Funciona

1. **Event Listener Registrado** no `Employee::booted()`

```php
protected static function booted()
{
    // Registar o Observer para gerir criação de User, Contract e Hourbank
    static::observe(EmployeeObserver::class);
}
```

2. **Observer Execute** quando Employee é criado

```php
class EmployeeObserver
{
    public function created(Employee $employee)
    {
        // Lógica executada automaticamente após INSERT
    }
}
```

3. **Sequência de Execução**

```
Employee::create() 
    ↓
[Eloquent Boot Sequence]
    ↓
Observer::creating()      // Antes de INSERT
    ↓
[DATABASE INSERT]
    ↓
Observer::created()       // Depois de INSERT ← AQUI EXECUTA NOSSO CÓDIGO
    ↓
├─ User::create()
├─ Contract::create()
└─ Hourbank::create()
    ↓
[Retorna Employee com Relações]
```

---

## ⚠️ Tratamento de Erros

Todos os blocos utilizam **try-catch** para prevenir erros em cascata:

```php
try {
    // Criação de User
} catch (\Exception $e) {
    // Log comentado (desabilitado em produção)
    // \Log::error('Erro ao criar User: ' . $e->getMessage());
}

try {
    // Criação de Contract
} catch (\Exception $e) {
    // Log comentado
}

try {
    // Criação de Hourbank
} catch (\Exception $e) {
    // Log comentado
}
```

### Cenários de Erro Tratados

| Cenário | Ação |
|---------|------|
| Email já existe | Skip User, continuar com Contract e Hourbank |
| Designation sem base_salary | Usar `0` como salário |
| ContractType `sem_termo` indefinida | Usar `null` para contract_type_id |
| Exceção genérica | Silenciar e continuar (evitar crash) |

---

## 📦 Cache para Notificações

### Objetivo
Guardar dados dos registros criados para notificação no Filament Resource

### Implementação

```php
// User criado
cache()->put("employee_{$employee->id}_created_user", [
    'id' => $user->id,
    'email' => $user->email,
    'role' => $user->role,
], now()->addMinutes(5));

// Contract criado
cache()->put("employee_{$employee->id}_created_contract", [
    'id' => $contract->id,
    'contract_type' => $contractType->name ?? 'N/A',
    'salary' => $baseSalary,
    'start_date' => $employee->date_hired?->format('d/m/Y'),
], now()->addMinutes(5));

// Hourbank criado
cache()->put("employee_{$employee->id}_created_hourbank", [
    'id' => $hourbank->id,
    'balance_hours' => $hourbank->balance_hours,
    'last_accrual_date' => $hourbank->last_accrual_date->format('d/m/Y'),
], now()->addMinutes(5));
```

### TTL (Time To Live)
**5 minutos** - Tempo suficiente para Filament recuperar e exibir notificação

### Resgate do Cache

```php
// No Filament Resource
$userData = cache()->get("employee_{$employee->id}_created_user");

if ($userData) {
    // Exibir notificação de sucesso
    Notification::make()
        ->success()
        ->title('User criado com sucesso!')
        ->body("Email: {$userData['email']}")
        ->send();
}
```

---

## ✅ Validações e Regras

### Antes da Criação

1. **Employee Validation**
   - ✓ Email obrigatório e único
   - ✓ first_name e last_name obrigatórios
   - ✓ date_hired válida

2. **Sem Validação Adicional** para User, Contract, Hourbank
   - Criados automaticamente sem checks extras
   - Herdam validações do Employee

### Durante a Criação

1. **User**
   - Verifica duplicação de email
   - Hash automático de senha (Model)

2. **Contract**
   - Busca contract_type (fallback para null)
   - Busca base_salary (fallback para 0)

3. **Hourbank**
   - Sem validações adicionais
   - Valores padrão sempre aplicados

### Pós-Criação

1. **Relacionamentos** definidos automaticamente
2. **Activity Tracing** registrado (RecordsActivity trait)
3. **Soft Deletes** habilitados (Employee, Contract)

---

## 💡 Exemplo Prático

### Cenário: Criar um novo Employee

```php
// 1. Filament Form Submission
$employee = Employee::create([
    'first_name'    => 'Maria',
    'last_name'     => 'Santos',
    'email'         => 'maria.santos@company.com',
    'nif'           => '987654321',
    'date_hired'    => now(),
    'designation_id'=> 5,     // Designer, base_salary = 3000
    'department_id' => 2,     // Design
]);

// 2. EmployeeObserver::created() DISPARA AUTOMATICAMENTE
// 
// Resultado 1: User criado
// Email: maria.santos@company.com
// Password: Hash de 'ChangeMe123!'
// Role: employee
//
// Resultado 2: Contract criado  
// Type: sem_termo
// Salary: 3000.00
// Start: 2026-03-04
// Status: active
//
// Resultado 3: Hourbank criado
// Balance: 0 horas
// Last Accrual: 2026-03-04

// 3. Cache guardado para notificação
// "employee_1_created_user"
// "employee_1_created_contract"
// "employee_1_created_hourbank"

// 4. Filament exibe sucesso
✅ Employee, User, Contract e Hourbank criados com sucesso!
```

---

## 📁 Arquivos Relacionados

- **Model:** [app/Models/Employee.php](../app/Models/Employee.php)
- **Observer:** [app/Observers/EmployeeObserver.php](../app/Observers/EmployeeObserver.php)
- **User Model:** [app/Models/User.php](../app/Models/User.php)
- **Contract Model:** [app/Models/Contract.php](../app/Models/Contract.php)
- **Hourbank Model:** [app/Models/Hourbank.php](../app/Models/Hourbank.php)

---

## 🔐 Variáveis de Ambiente

```env
# Senha padrão para novos usuários
DEFAULT_USER_PASSWORD=ChangeMe123!
```

---

## 📝 Notas Importantes

1. **Ordem de Criação é Crítica**
   - User → Contract → Hourbank (nessa sequência)

2. **Sem Transações Explícitas**
   - Cada criar é independente
   - Erro em um não reverte os outros

3. **Cache Temporário**
   - Apenas para notificações
   - Limpo automaticamente após 5min

4. **Password Hash**
   - Sempre hashed via `Hash::make()`
   - Senhas visíveis apenas no logs do Observer (comentados)

5. **Soft Deletes**
   - Employee e Contract suportam soft deletes
   - Hourbank não (dados históricos)

---

## 🚀 Próximas Melhorias

- [ ] Adicionar rollback / transações explícitas
- [ ] Sistema de filas para criação assíncrona
- [ ] Email de notificação ao Employee
- [ ] Geração de senha mais segura
- [ ] Log estruturado para auditoria

---

**Última Atualização:** 2026-03-04  
**Autor:** Sistema de Documentação Automática
