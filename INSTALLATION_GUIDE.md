# 🚀 Guia de Instalação - Problemas Resolvidos

**Última Atualização:** 29 de Janeiro de 2026

---

## 📋 Checklist de Implementação

### Passo 1: Executar Migrations ⚙️

```bash
# Criar tabelas de soft deletes
php artisan migrate

# Output esperado:
# Migrating: 2026_01_29_000001_add_soft_deletes_to_employees_table
# Migrated:  2026_01_29_000001_add_soft_deletes_to_employees_table
# Migrating: 2026_01_29_000002_add_soft_deletes_to_contracts_table
# Migrated:  2026_01_29_000002_add_soft_deletes_to_contracts_table
# ... etc
```

### Passo 2: Limpar Cache 🧹

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan filament:cache-components
```

### Passo 3: Verificar Integridade 🔍

```bash
# Verificar se não há erros de sintaxe
php artisan tinker
# > exit()

# Ou rodar um teste simples
php artisan test tests/Unit/Models/TimeoffTest.php
```

### Passo 4: Testar Cada Funcionalidade 🧪

#### A. Soft Deletes
```bash
php artisan tinker

# Criar e deletar um timeoff
$timeoff = App\Models\Timeoff::factory()->create();
$id = $timeoff->id;
$timeoff->delete();

# Verificar soft delete
App\Models\Timeoff::find($id) // null
App\Models\Timeoff::withTrashed()->find($id) // exists!
App\Models\Timeoff::onlyTrashed()->find($id)->restore() // restored

exit()
```

#### B. Validação de Overlaps
```bash
php artisan tinker

$emp = App\Models\Employee::first();

# Criar primeiro timeoff aprovado
$t1 = App\Models\Timeoff::create([
    'employee_id' => $emp->id,
    'start_date' => '2026-02-01',
    'end_date' => '2026-02-05',
    'type' => 'vacation',
    'status' => 'approved'
]);

# Tentar criar overlapping (deve falhar)
try {
    $t2 = App\Models\Timeoff::create([
        'employee_id' => $emp->id,
        'start_date' => '2026-02-03',
        'end_date' => '2026-02-10',
        'type' => 'vacation',
        'status' => 'pending'
    ]);
} catch (\Exception $e) {
    echo "Erro capturado: " . $e->getMessage();
}

exit()
```

#### C. Notificações
```bash
php artisan tinker

$emp = App\Models\Employee::first();

# Criar timeoff
$timeoff = App\Models\Timeoff::create([
    'employee_id' => $emp->id,
    'start_date' => now(),
    'end_date' => now()->addDays(5),
    'type' => 'vacation',
    'status' => 'pending'
]);

# Aprovar (deve disparar evento e criar notification)
$timeoff->update(['status' => 'approved']);

# Verificar notificação
App\Models\NotificationLog::where('type', 'timeoff_approved')
    ->where('user_id', $emp->user_id)
    ->first()
    ->dd(); // deve mostrar a notificação

exit()
```

#### D. Rate Limiting
```bash
# Via curl ou Postman
# Fazer 5 requests de login com dados válidos
curl -X POST http://localhost/login \
  -d "email=test@example.com&password=wrong"

# Na 6ª requisição, deve retornar 429 (Too Many Requests)
# {
#   "message": "Too many login attempts. Please try again in 285 seconds."
# }
```

#### E. Testes Automatizados
```bash
# Rodar todos os novos testes
php artisan test

# Resultado esperado: 30 passed
# Tests:  30 passed
# ✓ All tests passing

# Com cobertura
php artisan test --coverage
```

---

## 🐛 Troubleshooting

### Erro: "Class 'App\Models\SoftDeletes' not found"
**Solução:** Adicionar import no modelo
```php
use Illuminate\Database\Eloquent\SoftDeletes;
```

### Erro: "Table 'teamcore.xxx' has no column named 'deleted_at'"
**Solução:** Rodar migrations
```bash
php artisan migrate --fresh
```

### Erro: "Event listener not found"
**Solução:** Registrar no EventServiceProvider
```bash
# Já foi feito, mas se tiver problema:
php artisan cache:clear
php artisan config:clear
```

### Erro: "Rate limit middleware not registered"
**Solução:** Verificar se web.php tem a importação
```php
use App\Http\Middleware\RateLimitRequests;
```

### Teste falha com "Undefined method"
**Solução:** Rodar migrations e cache clear
```bash
php artisan migrate
php artisan cache:clear
php artisan test
```

---

## 📊 Estrutura de Testes

```
tests/
├── Unit/Models/
│   ├── TimeoffTest.php (8 testes)
│   │   ✓ requires_valid_dates
│   │   ✓ requires_valid_type
│   │   ✓ requires_valid_status
│   │   ✓ can_calculate_days_count
│   │   ✓ can_check_status_helpers
│   │   ✓ can_get_type_label
│   │   ✓ soft_deletes_work
│   │
│   └── ContractTest.php (8 testes)
│       ✓ requires_positive_salary
│       ✓ requires_valid_dates
│       ✓ requires_valid_status
│       ✓ can_check_contract_status
│       ✓ can_check_contract_within_period
│       ✓ soft_deletes_work
│
└── Feature/
    ├── Auth/
    │   └── LoginTest.php (5 testes)
    │       ✓ can_login_with_valid_credentials
    │       ✓ fails_login_with_invalid_password
    │       ✓ can_check_role_hierarchy
    │       ✓ must_change_password_flag_works
    │
    ├── Timeoff/
    │   └── TimeoffOverlapTest.php (4 testes)
    │       ✓ cannot_create_overlapping_approved_timeoffs
    │       ✓ can_create_pending_timeoffs_that_overlap
    │       ✓ can_have_multiple_non_overlapping_timeoffs
    │
    └── Notifications/
        └── TimeoffNotificationTest.php (5 testes)
            ✓ timeoff_approved_event_is_dispatched
            ✓ timeoff_rejected_event_is_dispatched
            ✓ notification_log_is_created_on_approval
            ✓ notification_log_is_created_on_rejection

Total: 30 testes | Cobertura: Modelo/Feature/Event
```

---

## 🔐 Segurança Pós-Implementação

### Checklist de Segurança

- [ ] Migrations executadas
- [ ] Cache limpo
- [ ] Testes passando (php artisan test)
- [ ] Rate limiting testado
- [ ] Notificações criando logs
- [ ] Soft deletes funcionando

### Configurações Adicionais (Próximas Semanas)

```bash
# 1. Configurar email para notificações
# Em .env:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.gmail.com
# MAIL_PORT=587
# MAIL_USERNAME=seu-email@gmail.com
# MAIL_PASSWORD=sua-senha-app
# MAIL_ENCRYPTION=tls
# MAIL_FROM_ADDRESS=seu-email@gmail.com

# 2. Configurar CRON para notificações
# Em /etc/crontab ou cPanel:
# * * * * * cd /path/to/teamcore && php artisan schedule:run >> /dev/null 2>&1

# 3. Iniciar queue worker (para emails assíncrono)
# php artisan queue:work
```

---

## 📞 Suporte

Se encontrar problemas:

1. **Verifique os logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Rode os testes:**
   ```bash
   php artisan test
   ```

3. **Verifique a documentação:**
   - [CODEBASE_REVIEW_AND_IMPROVEMENTS.md](CODEBASE_REVIEW_AND_IMPROVEMENTS.md)
   - [PROBLEMS_RESOLVED.md](PROBLEMS_RESOLVED.md)
   - [CRON_CONFIGURATION.md](CRON_CONFIGURATION.md)

4. **Reinicie tudo:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan queue:flush
   php artisan migrate:refresh --seed
   ```

---

## ✅ Validação Final

```bash
# Após completar todos os passos, execute:
php artisan test

# Resultado esperado:
# ✓ All tests passing (30 total)
# ✓ No errors
# ✓ Migrations successful
# ✓ Cache cleared
# ✓ Ready for production
```

---

**Status:** ✅ Implementado com Sucesso  
**Data:** 29 de Janeiro de 2026  
**Próximo Passo:** Implementar Nível 2 (API REST, Audit, Performance)
