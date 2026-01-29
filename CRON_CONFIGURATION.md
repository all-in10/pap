# Agendamento de Tarefas CRON - TeamCore HR System

## Configuração em `routes/console.php`

Adicione os comandos abaixo para serem executados automaticamente:

```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Verificar contratos expirando e enviar reminders
Artisan::command('notifications:check-expiring-contracts', function () {
    $count = \App\Services\NotificationService::checkExpiringContracts();
    $this->info("Checked {$count} expiring contracts");
})->purpose('Check for expiring contracts')->daily();

// Limpar notificações antigas
Artisan::command('notifications:cleanup', function () {
    $deleted = \App\Services\NotificationService::deleteOldNotifications(90);
    $this->info("Deleted {$deleted} old notifications");
})->purpose('Delete old notifications')->monthly();
```

## Configuração do Linux/cPanel Cron

Para executar os comandos acima:

```bash
# Executar tarefas agendadas a cada minuto (Laravel Scheduler)
* * * * * cd /path/to/teamcore && php artisan schedule:run >> /dev/null 2>&1
```

## Tarefas Agendadas

| Tarefa | Frequência | Comando | Descrição |
|--------|-----------|---------|-----------|
| Verificar Contratos Expirando | Daily (01:00 AM) | `notifications:check-expiring-contracts` | Verifica contratos vencendo nos próximos 30 dias |
| Limpeza de Notificações | Monthly (1º dia) | `notifications:cleanup` | Remove notificações com mais de 90 dias |
| Queue Worker | Always Running | `queue:work` | Processa jobs assíncrono (emails, etc) |

## Executar Manualmente

Para testar as tarefas:

```bash
# Verificar contratos expirando
php artisan notifications:check-expiring-contracts

# Limpar notificações antigas
php artisan notifications:cleanup

# Processar queue jobs
php artisan queue:work

# Executar todas as tarefas agendadas
php artisan schedule:run
```

## Monitoramento

Para monitorar as tarefas agendadas, use:

```bash
# Ver resultado das tarefas (Laravel 11+)
php artisan schedule:list

# Ver histórico de execução
tail -f storage/logs/laravel.log
```

## Troubleshooting

Se as tarefas não estão sendo executadas:

1. **Verifique se o cron está configurado:**
   ```bash
   crontab -l
   ```

2. **Teste o acesso ao artisan:**
   ```bash
   php artisan tinker
   ```

3. **Verifique permissões:**
   ```bash
   chmod 775 storage bootstrap
   ```

4. **Confirme que o caminho está correto:**
   ```bash
   which php
   # Use o caminho completo do PHP no cron
   ```

5. **Verifique logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
