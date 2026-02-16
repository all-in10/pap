# Sistema Automático de Criação de Employee com Gatilhos

## 📋 Resumo da Implementação

Implementou-se um sistema de gatilhos (Observer Pattern) que automaticamente cria um User, Contract e Hourbank quando um novo Employee é criado. O sistema também apresenta notificações Filament personalizadas com detalhes dos itens criados.

---

## 🔧 Componentes Implementados

### 1. **EmployeeObserver.php** - `app/Observers/EmployeeObserver.php`
- **Responsabilidade:** Interceptar o evento `created` do Employee e executar criações automáticas
- **Ações:**
  - ✅ Cria um User com nome e email do Employee
    - Role: `employee` (acesso limitado)
    - Password: Hashed via DEFAULT_USER_PASSWORD (.env)
    - must_change_password: `true` (força mudança na 1ª vez)
  - ✅ Cria um Contract automático
    - Tipo: "sem_termo" (indefinido)
    - Salário: Baseado na Designation do Employee
    - Status: "active"
  - ✅ Cria um Hourbank automático
    - Balance: 0 horas
    - Last accrual date: Data de contratação
  - ✅ Armazena dados em cache para notificações
  - ✅ Tratamento de erros com logging

**Related Files:**
- [app/Models/Employee.php](app/Models/Employee.php) - Registação do Observer
- [app/Models/User.php](app/Models/User.php) - Modelo de User
- [app/Models/Contract.php](app/Models/Contract.php) - Modelo de Contract
- [app/Models/Hourbank.php](app/Models/Hourbank.php) - Modelo de Hourbank

### 2. **CreateEmployee.php** - `app/Filament/Resources/EmployeeResource/Pages/CreateEmployee.php`
- **Responsabilidade:** Mostrar notificações ao utilizador após criação bem-sucedida
- **Features:**
  - Recupera dados do cache deixados pelo Observer
  - Mostra 4 notificações Toast:
    1. Notificação de User criado (com role e status)
    2. Notificação de Contract criado (com tipo, salário, data)
    3. Notificação de Hourbank criado (com saldo e accrual date)
    4. Notificação consolidada final (com checkbox de sucesso)
  - Cada notificação tem ícone e cor personalizada

**Example Notifications:**
```
🎉 Utilizador Criado
Utilizador joao@empresa.com criado com sucesso!
Role: employee
Status: Deve alterar senha no primeiro acesso

📄 Contrato Criado
Contrato criado com sucesso!
Tipo: sem_termo
Salário: €1500
Data Início: 16/02/2026

⏰ Banco de Horas Criado
Banco de horas criado com sucesso!
Saldo Inicial: 0h
Data Accrual: 16/02/2026

✅ Employee Criado
Employee João Silva criado com ✓ Utilizador ✓ Contrato ✓ Banco Horas associados com sucesso!
```

### 3. **Validação de Email** - `app/Rules/ValidEmailDomain.php`
- **Compatível com:** Sistema de criação automática
- **Garante:** Emails com domínio válido (ex: usuario@empresa.com, NOT teste@teste)
- Aplicado em:
  - Employee Resource
  - User Resource

---

## 🔗 Relações entre Models

```
Employee
├── hasMany(Contract)
├── hasMany(Hourbank)
├── hasOne(User) - via User.employee_id
├── belongsTo(Department)
├── belongsTo(Designation)
├── belongsTo(Country/State/City)
└── hasMany(Worklog, Timeoff, Benefit, Attendance)

User
├── belongsTo(Employee) - via employee_id (unique)
├── hasMany(tokens, notifications, etc)
└── filled: name, email, password, role, must_change_password

Contract
├── belongsTo(Employee)
├── belongsTo(ContractType)
└── fields: type, salary, start_date, status

Hourbank
├── belongsTo(Employee)
└── fields: balance_hours, last_accrual_date
```

---

## 📊 Fluxo de Criação (Flow Diagram)

```
1. Utilizador cria Employee no Filament
   ↓
2. CreateEmployee form valida dados
   ├─ Email deve ter formato válido (custom rule)
   ├─ Campos obrigatórios
   └─ Relações estrangeiras
   ↓
3. Employee record é saved na DB
   ↓
4. EmployeeObserver::created() é disparado
   ├─ Cria User (email único, role=employee)
   │  └─ Armazena em cache: employee_{id}_created_user
   ├─ Cria Contract (type, salary, dates)
   │  └─ Armazena em cache: employee_{id}_created_contract
   ├─ Cria Hourbank (initial balance=0)
   │  └─ Armazena em cache: employee_{id}_created_hourbank
   └─ Log de qualquer erro em logs
   ↓
5. CreateEmployee::afterCreate() mostra notificações
   ├─ Recupera dados do cache
   ├─ Dispara 4 Toast notifications
   └─ Limpa cache após 5 minutos
   ↓
6. Utilizador vê feedback visual completo
```

---

## ✅ Testes Implementados

### Feature Tests: `tests/Feature/EmployeeAutomaticCreationTest.php`

**Todos os testes passam ✓**

1. ✅ `creates user automatically when employee is created`
   - Verifica que User é criado com email/nome corretos
   - Valida role='employee' e must_change_password=true
   - Checa associação employee_id

2. ✅ `creates contract automatically when employee is created`
   - Verifica que Contract é criado
   - Valida status='active' e datas

3. ✅ `creates hourbank automatically when employee is created`
   - Verifica que Hourbank é criado
   - Valida balance_hours=0

4. ✅ `does not create duplicate user if email already exists`
   - Testa proteção contra emails duplicados
   - Verifica que não há múltiplos users

5. ✅ `user password is hashed when employee is created`
   - Valida que senha está hasheada corretamente
   - Testa integração com DEFAULT_USER_PASSWORD

6. ✅ `clears cache after notification data is retrieved`
   - Verifica que dados são guardados em cache
   - Validação para notificações Filament

**Test Output:**
```
PASS  Tests\Feature\EmployeeAutomaticCreationTest
✓ it creates user automatically when employee is created           0.25s
✓ it creates contract automatically when employee is created       0.02s
✓ it creates hourbank automatically when employee is created       0.02s
✓ it does not create duplicate user if email already exists        0.02s
✓ it user password is hashed when employee is created              0.02s
✓ it clears cache after notification data is retrieved             0.03s

Tests:    6 passed (18 assertions)
Duration: 0.38s
```

---

## 🔐 Segurança e Validações

### Email Validation
- ✅ Custom Rule: `ValidEmailDomain` rejeita `teste@teste`
- ✅ Aceita: `usuario@empresa.com`, `user@domain.co.uk`, etc.

### User Creation Safety
- ✅ Email único (constraint na DB)
- ✅ Verifica email existente antes de criar
- ✅ Password hashed (bcrypt)
- ✅ Força mudança de senha no 1º acesso
- ✅ Role limited `'employee'` por padrão

### Data Integrity
- ✅ Foreign keys com cascadeOnDelete
- ✅ Unique constraint em User.employee_id
- ✅ Validação de datas (date_of_birth, date_hired)
- ✅ Error logging em caso de falha

---

## 🚀 Como Usar

### Criar um novo Employee (via Filament)

1. Navegar para `Funcionários` → `Criar Funcionário`
2. Preencher formulário:
   - Nome, Email (ex: joao@empresa.com)
   - Localização (País, Estado, Cidade)
   - Departamento, Designação
   - Datas (Nascimento, Contratação)
   - Contacto, Morada, etc.
3. Clicar "Criar"
4. Sistema automaticamente:
   - ✅ Cria User com senha padrão
   - ✅ Cria Contract indefinido
   - ✅ Cria Hourbank com 0 horas
   - ✅ Mostra 4 notificações de sucesso

### Via API/Seeder

```php
$employee = Employee::create([
    'first_name' => 'João',
    'last_name' => 'Silva',
    'email' => 'joao@empresa.com',
    'country_id' => 1,
    'state_id' => 1,
    'city_id' => 1,
    'department_id' => 1,
    'date_hired' => now(),
    'date_of_birth' => now()->subYears(30),
    'nss' => '123456789',
    'nif' => '987654321',
    'phone_number' => '9123456789',
    'address' => 'Rua Teste, 123',
    'zip_code' => '1000-001',
]);

// Automático:
// - User criado com email='joao@empresa.com', role='employee'
// - Contract criado with status='active'
// - Hourbank criado with balance_hours=0
```

---

## 📝 Variáveis de Ambiente

### .env Configuration

```env
# Senha padrão para novos Users
DEFAULT_USER_PASSWORD=ChangeMe123!

# Cache (para notificações)
CACHE_DRIVER=redis (ou file, database, etc)
```

---

## 🐛 Error Handling

Se algo der errado durante criação automática:

1. **Erro ao criar User:**
   - Log: `Erro ao criar User para Employee: {mensagem}`
   - Notificação: Uma das 4 notificações não aparecerá
   - Database: Employee é criado mesmo assim (só falhao o User)

2. **Erro ao criar Contract:**
   - Log: `Erro ao criar Contract para Employee: {mensagem}`
   - Contract não aparecerá, mas Employee e User serão criados

3. **Erro ao criar Hourbank:**
   - Log: `Erro ao criar Hourbank para Employee: {mensagem}`
   - Hourbank não aparecerá, mas resto será criado

**Recomendação:** Monitorar `storage/logs/laravel.log` para erros

---

## 📚 Ficheiros Modificados/Criados

### Criados
- ✅ `app/Observers/EmployeeObserver.php` - Observer principal
- ✅ `tests/Feature/EmployeeAutomaticCreationTest.php` - Feature tests

### Modificados
- ✅ `app/Models/Employee.php` - Registação de Observer
- ✅ `app/Filament/Resources/EmployeeResource/Pages/CreateEmployee.php` - Notificações
- ✅ `app/Models/User.php` - No changes (já tinha employee_id)
- ✅ `app/Rules/ValidEmailDomain.php` - Existente (validação de email)

### Migrations
- ❌ Nenhuma nova migration (usou estrutura existente com employee_id em users)

---

## 🔍 Troubleshooting

### "User não foi criado, mas Contract foi"
- Verificar se email já existe em Users table
- Verificar erro em logs: `ERROR Erro ao criar User para Employee`

### Notificações não aparecem
- Verificar se `Cache::put()` está funcionando (config/cache.php)
- Verificar se Filament Notifications estão ativas

### Email invalid error
- Verificar se email segue formato válido (ex: usuario@empresa.com)
- Rejeita: teste@teste, @domain.com, user@

### Password não pode ser verificada
- Garantir que DEFAULT_USER_PASSWORD está setado em .env
- Verificar que Hash::check() está funcionando

---

## 📞 Suporte

Para questões sobre este sistema:

1. Verificar logs: `tail -f storage/logs/laravel.log`
2. Rodar testes: `php artisan test tests/Feature/EmployeeAutomaticCreationTest.php`
3. Verificar cache: `php artisan tinker` → `cache()->get('employee_1_created_user')`
4. Validar notificações: Criar Employee via Filament e observar Toast messages
