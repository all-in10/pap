# Reestruturação de Widgets - Políticas de Visibilidade

## Resumo

Todos os widgets foram reestruturados para aparecerem apenas para usuários que fazem sentido, usando políticas de filtro baseadas em roles (papéis).

## Implementação

### 1. Trait WidgetVisibility

Criada a trait `App\Filament\Traits\WidgetVisibility` que fornece:

- **`allowedRoles(): array`** - Método que cada widget deve implementar para retornar os roles que podem visualizá-lo
- **`canView(): bool`** - Método estático que verifica se o usuário atual tem permissão para ver o widget

Uso:
```php
use WidgetVisibility;

protected static function allowedRoles(): array
{
    return [
        UserRole::ADMIN,
        UserRole::ROOT,
    ];
}
```

### 2. Classificação de Widgets

#### 🔐 Admin/Root Only (Global)
- **StatsOverview** - Estatísticas globais de usuários, funcionários, contratos e férias
- **DashboardOverviewWidget** - Visão geral do dashboard com KPIs
- **RecentAuditLogsWidget** - Logs de auditoria do sistema
- **LicenseInformationWidget** - Informações de licença

#### 👥 HR + Admin/Root
- **ContractsChart** - Gráfico de contratos por tipo
- **DepartmentDistributionChart** - Distribuição de funcionários por departamento
- **TimeoffsChart** - Gráfico de tipos de solicitações de férias
- **WeeklyWorklogChart** - Horas registradas na semana (pode ser útil para HR revisar)
- **AverageHoursWidget** - Média de horas (pode ser útil para HR revisar)

#### 👤 Employee Only
- **EmployeeInfoWidget** - Informações pessoais do funcionário
- **WorklogSummaryWidget** - Resumo recente de ponto
- **HoursbankHistoryWidget** - Histórico do banco de horas (também visível para HR)

#### 📊 Employee + HR (Métricas Pessoais/de Equipe)
- **HoursbankHistoryWidget** - Histórico de banco de horas
- **AverageHoursWidget** - Média de horas

### 3. Panel Providers Atualizados

Cada Panel Provider (`AdminPanelProvider`, `HrPanelProvider`, `EmployeePanelProvider`, `AppPanelProvider`) foi atualizado com:

```php
->widgets($this->getVisibleWidgets())

protected function getVisibleWidgets(): array
{
    // Descobre todos os widgets
    // Filtra apenas aqueles onde canView() retorna true
    // Retorna apenas os visíveis para o usuário atual
}
```

## Fluxo de Filtragem

1. **Descoberta de Widgets**: `discoverWidgets()` descobre todos os widgets do sistema
2. **Filtragem de Widgets**: `getVisibleWidgets()` filtra baseado em:
   - Verificação se a classe tem o método `canView()`
   - Verificação do role do usuário autenticado
   - Retorno apenas dos widgets permitidos
3. **Renderização**: Filament renderiza apenas os widgets retornados

## Vantagens

✅ **Segurança**: Widgets sensíveis não aparecem para usuários não autorizados
✅ **UX Melhorada**: Usuários só veem widgets relevantes para suas funções
✅ **Manutenção**: Fácil adicionar novos widgets ou mudar políticas
✅ **Escalabilidade**: Sistema segue o padrão de policies do Laravel
✅ **Configurável**: Cada widget controla sua própria visibilidade

## Exemplo de Uso para Novo Widget

```php
<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Traits\WidgetVisibility;
use Filament\Widgets\ChartWidget;

class MyNewWidget extends ChartWidget
{
    use WidgetVisibility;

    protected static ?string $heading = 'Meu Novo Widget';

    protected static function allowedRoles(): array
    {
        return [
            UserRole::ADMIN,
            UserRole::HR,
        ];
    }

    // ... resto da implementação
}
```

## Estrutura de Arquivos

```
app/
├── Filament/
│   ├── Traits/
│   │   └── WidgetVisibility.php          (trait para controlar visibilidade)
│   ├── Widgets/
│   │   ├── StatsOverview.php             (Admin/Root)
│   │   ├── DashboardOverviewWidget.php   (Admin/Root)
│   │   ├── RecentAuditLogsWidget.php     (Admin/Root)
│   │   ├── LicenseInformationWidget.php  (Admin/Root)
│   │   ├── ContractsChart.php            (HR/Admin/Root)
│   │   ├── DepartmentDistributionChart.php (HR/Admin/Root)
│   │   ├── TimeoffsChart.php             (HR/Admin/Root)
│   │   ├── WeeklyWorklogChart.php        (Employee/HR)
│   │   ├── AverageHoursWidget.php        (Employee/HR)
│   │   ├── EmployeeInfoWidget.php        (Employee)
│   │   ├── WorklogSummaryWidget.php      (Employee)
│   │   └── HoursbankHistoryWidget.php    (Employee/HR)
│   └── Concerns/
│       └── WidgetFiltering.php           (utilitário de filtragem)
└── Providers/
    └── Filament/
        ├── AdminPanelProvider.php        (com getVisibleWidgets())
        ├── HrPanelProvider.php           (com getVisibleWidgets())
        ├── EmployeePanelProvider.php     (com getVisibleWidgets())
        └── AppPanelProvider.php          (com getVisibleWidgets())
```

## Testes Recomendados

1. **Login como Admin** → Verificar se todos os widgets globais aparecem
2. **Login como HR** → Verificar se widgets de HR + HR+Employee aparecem
3. **Login como Employee** → Verificar se apenas widgets de Employee aparecem
4. **Verificar Logs de Auditoria** → Confirmar que não aparecem para Employee/HR
5. **Verificar DashboardOverviewWidget** → Confirmar que não aparece para Employee

## Notas

- A trait WidgetVisibility não afeta widgets do Filament nativo (AccountWidget, FilamentInfoWidget)
- Os widgets ainda são descobertos automaticamente via `discoverWidgets()` para evitar duplicação
- O filtro acontece em tempo de renderização, mantendo performance
- Sistema segue o padrão de policies de autorização do Laravel
