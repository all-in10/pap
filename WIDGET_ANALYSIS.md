# Análise de Widgets por Panel Provider

## 📊 Estrutura Atual

### Panel Providers Identificados
1. **AdminPanelProvider** - Dashboard administrativo
2. **EmployeePanelProvider** - Painel de funcionários
3. **HRPanelProvider** - Painel de RH

### Widgets Atuais (2 apenas)
- `GeneralStats.php` - Stats Overview com 4 métricas
- `EmployeeInfoWidget.php` - Informações do funcionário logado

---

## 🎯 Recomendações de Novos Widgets

### **ADMIN PANEL** 
**Objetivo:** Visão executiva e monitoramento geral

#### 1. **DepartmentStatsWidget**
- Colaboradores por departamento (gráfico de barras)
- Departamento com mais colaboradores
- Card com contadores

#### 2. **ContractOverviewWidget**
- Contatos ativos vs inativos
- Contratos vencendo (próximos 30 dias)
- Taxa de renovação

#### 3. **AttendanceOverviewWidget**
- Taxa de presença global (%)
- Faltas no mês atual
- Média de horas trabalhadas

#### 4. **BenefitsDistributionWidget**
- Benefícios mais utilizados
- Colaboradores por tipo de benefício
- Gráfico de pizza ou barra

#### 5. **UpcomingTimeoffsWidget**
- Licenças/férias aprovadas próximas
- Pendentes por departamento
- Calendário visual

#### 6. **HourBankSummaryWidget**
- Banco de horas total (agregado)
- Colaboradores com saldo positivo/negativo
- Alertas para saldos críticos

---

### **HR PANEL**
**Objetivo:** Gerenciamento operacional de RH

#### 1. **EmployeeDirectoryWidget**
- Lista recentes de colaboradores adicionados
- Filtro por departamento/designação
- Acesso rápido a perfis

#### 2. **PendingTimeoffsWidget**
- Tabela de solicitações pendentes
- Filtro por colaborador/categoria
- Ações rápidas (aprovar/rejeitar)

#### 3. **ContractExpirationAlertWidget**
- Contratos vencendo em x dias
- Status do último contrato por colaborador
- Ações para renovação

#### 4. **AttendanceAlertWidget**
- Colaboradores com muitas faltas
- Faltas não justificadas
- Notificações em tempo real

#### 5. **WorklogComplianceWidget**
- Conformidade de registros de trabalho
- Dias com horas incompletas
- Taxa de preenchimento

#### 6. **BenefitstWidget**
- Benefícios pendentes de aprovação
- Colaboradores sem benefício
- Custo total de benefícios

#### 7. **DepartmentPerformanceWidget**
- Taxa de presença por departamento
- Horas extras por departamento
- Custo de folha por departamento

---

### **EMPLOYEE PANEL**
**Objetivo:** Informações pessoais e autoatendimento

#### 1. **MyTimeoffHistoryWidget**
- Histórico das minhas licenças/férias
- Status de cada solicitação
- Display visual com badges de status

#### 2. **MyAttendanceWidget**
- Resumo de presença (mês atual)
- Faltas, atrasos, pontuação
- Tendência mensal

#### 3. **MyBenefitsWidget**
- Benefícios ativos do colaborador
- Data de validade
- Detalhes rápidos

#### 4. **MyContractWidget**
- Contrato atual
- Salário e tipo de contrato
- Data de próximas alterações

#### 5. **MyUpcomingTimeoffWidget**
- Próximas licenças aprovadas
- Contagem regressiva
- Filtro por categoria

#### 6. **HourBankDetailWidget**
- Detalhamento do banco de horas
- Histórico de movimentações
- Acumulado vs. utilizado

#### 7. **MyWorklogsWidget**
- Registros de trabalho recentes
- Horas do dia anterior/atual
- Ação para registrar nova entrada

---

## 📊 Tipos de Widgets Sugeridos

### Por Estrutura:
1. **StatsOverviewWidget** - Métricas numéricas com ícones
2. **ChartWidget** - Gráficos (barras, pizza, linha)
3. **TableWidget** - Tabelas com dados
4. **CalendarWidget** - Calendário visual
5. **AlertWidget** - Notificações de alertas
6. **ProgressWidget** - Barras de progresso

### Bibliotecas Filament Recomendadas:
- `filament/charts` - Para gráficos
- `filament/tables` - Para tabelas
- Custom blade views - Para layouts customizados

---

## 🔧 Implementação Recomendada

### Prioridade Alta (Fase 1):
1. ✅ Admin: DepartmentStats, ContractOverview, AttendanceOverview
2. ✅ HR: PendingTimeoffss, ContractExpirationAlert, EmployeeDirectory
3. ✅ Employee: MyTimeoffHistory, MyAttendance, HourBankDetail

### Prioridade Média (Fase 2):
1. BenefitsDistribution, WorklogCompliance, UpcomingTimeoffs
2. BenefitsWidget (HR), MyBenefits (Employee)

### Prioridade Baixa (Fase 3):
1. Gráficos avançados, Relatórios customizados
2. Widgets de integração

---

## 📁 Estrutura de Diretórios Sugerida

```
app/Filament/Widgets/
├── Admin/
│   ├── DepartmentStatsWidget.php
│   ├── ContractOverviewWidget.php
│   ├── AttendanceOverviewWidget.php
│   └── ...
├── HR/
│   ├── EmployeeDirectoryWidget.php
│   ├── PendingTimeoffsWidget.php
│   ├── ContractExpirationAlertWidget.php
│   └── ...
├── Employee/
│   ├── MyTimeoffHistoryWidget.php
│   ├── MyAttendanceWidget.php
│   ├── HourBankDetailWidget.php
│   └── ...
├── GeneralStats.php (shared)
└── EmployeeInfoWidget.php (shared)

resources/views/filament/widgets/
├── admin/
├── hr/
├── employee/
└── General views
```

---

## 🛠️ Exemplos de Código

### Exemplo 1: DepartmentStatsWidget (StatsOverviewWidget)
```php
<?php

namespace App\Filament\Widgets\Admin;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Department;
use App\Models\Employee;

class DepartmentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $departments = Department::withCount('employees')->get();
        $topDepartment = $departments->sortByDesc('employees_count')->first();
        
        return [
            Stat::make('Total de Departamentos', Department::count())
                ->icon('heroicon-o-building-library'),
            Stat::make('Colaboradores', Employee::count())
                ->icon('heroicon-o-user-group'),
            Stat::make('Depto. Maior', $topDepartment?->name ?? 'N/A')
                ->description($topDepartment?->employees_count . ' colaboradores')
                ->icon('heroicon-o-star'),
        ];
    }
}
```

### Exemplo 2: PendingTimeoffsWidget (Tabela)
```php
<?php

namespace App\Filament\Widgets\HR;

use Filament\Widgets\Widget;
use App\Models\Timeoff;

class PendingTimeoffsWidget extends Widget
{
    protected static string $view = 'filament.widgets.hr.pending-timeoffs-widget';
    
    public function getPendingTimeoffs()
    {
        return Timeoff::where('status', 'pending')
            ->with(['employee', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
}
```

### Exemplo 3: MyAttendanceWidget (Custom)
```php
<?php

namespace App\Filament\Widgets\Employee;

use Filament\Widgets\Widget;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyAttendanceWidget extends Widget
{
    protected static string $view = 'filament.widgets.employee.my-attendance-widget';
    
    public function getAttendanceStats()
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        $currentMonth = Carbon::now()->startOfMonth();
        $attendances = $employee->attendances()
            ->whereBetween('date', [$currentMonth, now()])
            ->get();
        
        return [
            'total_days' => $attendances->count(),
            'absences' => $attendances->where('status', 'absent')->count(),
            'lates' => $attendances->where('status', 'late')->count(),
            'attendance_rate' => ($attendances->where('status', 'present')->count() / max(1, $attendances->count())) * 100,
        ];
    }
}
```

---

## 📝 Checklist de Implementação

- [ ] Criar estrutura de diretórios para widgets/Admin, /HR, /Employee
- [ ] Implement Fase 1 widgets (9 widgets)
- [ ] Registrar widgets nos respectivos PanelProviders
- [ ] Criar arquivo Blade para cada widget (se necessário)
- [ ] Testes unitários para widgets
- [ ] Documentação de uso
- [ ] Testes end-to-end
- [ ] Deploy e feedback

---

## 🔗 Relacionamentos de Dados Disponíveis

Baseado na análise do modelo Employee:

```
Employee
├── contracts (HasMany)
├── worklogs (HasMany)
├── hourbanks (HasMany)
├── timeoffs (HasMany)
├── benefits (HasMany)
├── attendances (HasMany)
├── department (BelongsTo)
├── designation (BelongsTo)
├── country, state, city (BelongsTo)
└── user (HasOne)
```

Todos esses relacionamentos podem ser explorados nos widgets!
