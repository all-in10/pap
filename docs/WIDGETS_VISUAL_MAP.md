# 🎨 WIDGETS - Mapa Visual Estruturado

## 📊 Estrutura Hierárquica Completa

```
┌─────────────────────────────────────────────────────────────────┐
│                    FILAMENT WIDGETS (14)                         │
└─────────────────────────────────────────────────────────────────┘

├─ 🔐 ADMIN PANEL (7)
│  │
│  ├─ 📊 DEPARTAMENTOS
│  │  ├─ [Stats] DepartmentStatsWidget
│  │  │  └─ Mostra: Total deptos, Total colaboradores, Top depto
│  │  │
│  │  └─ [Chart] DepartmentChartWidget
│  │     └─ Mostra: Gráfico de barras - Colaboradores por depto
│  │
│  ├─ 📋 CONTRATOS (Visão 360°)
│  │  ├─ [Stats] ContractOverviewWidget
│  │  │  └─ Mostra: Ativos, Inativos, Taxa renovação
│  │  │
│  │  ├─ [Chart] ContractStatusChartWidget
│  │  │  └─ Mostra: Gráfico pizza - Status (ativo/inativo/pendente)
│  │  │
│  │  └─ [Chart] ContractTypeDistributionWidget
│  │     └─ Mostra: Gráfico barras - Tipos de contrato
│  │
│  └─ ✅ PRESENÇA
│     ├─ [Stats] AttendanceOverviewWidget
│     │  └─ Mostra: Taxa %, Faltas, Média de horas
│     │
│     └─ [Chart] AttendanceChartWidget
│        └─ Mostra: Gráfico linha - Presença por dia do mês
│
├─ 👥 HR PANEL (3)
│  │
│  ├─ 👤 FUNCIONÁRIOS
│  │  └─ [Stats] EmployeeDirectoryWidget
│  │     └─ Mostra: Total, Ativos, Inativos
│  │
│  ├─ ⚠️  CONTRATOS - ALERTAS
│  │  └─ [Stats] ContractExpirationAlertWidget
│  │     └─ Mostra: Vencidos, Vencendo em 30 dias, vencendo em 90 dias
│  │
│  └─ 📅 LICENÇAS / FÉRIAS
│     └─ [Stats] PendingTimeoffsWidget
│        └─ Mostra: Pendentes, Aprovadas, Rejeitadas, Próximas férias
│
├─ 👤 EMPLOYEE PANEL (3)
│  │
│  ├─ 📅 MINHAS LICENÇAS
│  │  └─ [Stats] MyTimeoffHistoryWidget
│  │     └─ Mostra: Minhas solicitações pendentes, aprovadas, rejeitadas
│  │
│  ├─ ✅ MINHA PRESENÇA
│  │  └─ [Stats] MyAttendanceWidget
│  │     └─ Mostra: Minha presença do mês, Faltas, Atrasos
│  │
│  └─ ⏰ MEU BANCO DE HORAS
│     └─ [Stats] HourBankDetailWidget
│        └─ Mostra: Meu saldo, Acumulado, Utilizado
│
└─ 🌍 GLOBAL - TODOS OS PAINÉIS (2)
   │
   ├─ [Stats] GeneralStats
   │  └─ Mostra: Total usuários, Colaboradores, Contratos ativos, Licenças pendentes
   │
   └─ [Custom Blade] EmployeeInfoWidget
      └─ Mostra: Info completa do usuário logado + Banco de horas + Licenças
```

---

## 📍 MAPA DE LOCALIZAÇÃO

```
app/Filament/Widgets/
│
├── Admin/  ..................... (7 widgets - Gerencial)
│   ├── AttendanceChartWidget.php [Sort: 7]
│   ├── AttendanceOverviewWidget.php [Sort: 1] ⭐ Primeiro
│   ├── ContractOverviewWidget.php [Sort: 3]
│   ├── ContractStatusChartWidget.php [Sort: 6]
│   ├── ContractTypeDistributionWidget.php [Sort: 4]
│   ├── DepartmentChartWidget.php [Sort: 5]
│   └── DepartmentStatsWidget.php [Sort: 2]
│
├── HR/  .......................... (3 widgets - Operacional)
│   ├── ContractExpirationAlertWidget.php
│   ├── EmployeeDirectoryWidget.php
│   └── PendingTimeoffsWidget.php
│
├── Employee/  ...................... (3 widgets - Pessoal)
│   ├── HourBankDetailWidget.php
│   ├── MyAttendanceWidget.php
│   └── MyTimeoffHistoryWidget.php
│
├── GeneralStats.php ................. (Global - Root)
└── EmployeeInfoWidget.php ........... (Global - Root + Blade)
```

---

## 🎯 FLUXO DE DADOS POR DOMÍNIO

### DEPARTAMENTOS 🏢
```
Department Model
    ↓
    ├─→ DepartmentStatsWidget
    │   ├─ count()
    │   ├─ withCount('employees')
    │   └─ max employees
    │
    └─→ DepartmentChartWidget
        ├─ withCount('employees')
        ├─ orderByDesc()
        └─ limit(10)
```

### CONTRATOS 📋
```
Contract Model
    ↓
    ├─→ ContractOverviewWidget [Admin]
    │   ├─ where status = 'active'
    │   ├─ where status = 'inactive'
    │   └─ renewal rate
    │
    ├─→ ContractStatusChartWidget [Admin]
    │   ├─ groupBy status
    │   └─ count per status
    │
    ├─→ ContractTypeDistributionWidget [Admin]
    │   ├─ groupBy type
    │   └─ count per type
    │
    ├─→ ContractExpirationAlertWidget [HR]
    │   ├─ where end_date <= today
    │   ├─ where end_date between 30-90 days
    │   └─ alerts
    │
    └─→ GeneralStats
        └─ count active contracts
```

### PRESENÇA ✅
```
Attendance Model
    ↓
    ├─→ AttendanceOverviewWidget [Admin]
    │   ├─ count records
    │   ├─ count absent (total - present)
    │   └─ avg hours_worked
    │
    ├─→ AttendanceChartWidget [Admin]
    │   ├─ daily aggregation
    │   ├─ present vs absent vs late
    │   └─ line chart
    │
    └─→ MyAttendanceWidget [Employee]
        ├─ current user attendance
        ├─ monthly stats
        └─ personal view

Worklog Model (não tem widget ainda)
    ├─ Proposto: MyWorklogsWidget
    └─ Proposto: WorklogComplianceWidget [HR]
```

### LICENÇAS / FÉRIAS 📅
```
Timeoff Model
    ↓
    ├─→ PendingTimeoffsWidget [HR]
    │   ├─ where status = 'pending'
    │   ├─ where status = 'approved'
    │   ├─ where status = 'rejected'
    │   └─ upcoming approved
    │
    ├─→ MyTimeoffHistoryWidget [Employee]
    │   ├─ current employee timeoffs
    │   ├─ by status
    │   └─ personal history
    │
    ├─→ GeneralStats
    │   └─ count pending
    │
    └─ Proposto: TimeoffTrendChart [Admin]
        └─ monthly distribution
```

### BANCO DE HORAS ⏰
```
Hourbank Model
    ↓
    ├─→ HourBankDetailWidget [Employee]
    │   ├─ latest balance
    │   ├─ accumulated
    │   └─ used
    │
    └─ Proposto: HourBankSummaryWidget [Admin]
        ├─ aggregate balance
        ├─ by department
        └─ alerts for critical
```

### FUNCIONÁRIOS 👥
```
Employee + User Model
    ↓
    ├─→ EmployeeDirectoryWidget [HR]
    │   ├─ total count
    │   ├─ active count
    │   └─ inactive count
    │
    ├─→ GeneralStats
    │   └─ total employees
    │
    └─→ EmployeeInfoWidget [Global]
        ├─ current user info
        ├─ related contract
        ├─ related hourbank
        └─ related timeoffs
```

---

## 🔄 FLUXO DE EXIBIÇÃO NO ADMIN PANEL

```
Dashboard Admin (Filament)/
│
├─ GeneralStats [Sort: 1 - Global]
│  └─ 4 KPIs: Users, Employees, Active Contracts, Pending Timeoffs
│
├─ AttendanceOverviewWidget [Sort: 1 - Admin]
│  └─ 3 Stats: Attendance %, Absences, Avg Hours
│
├─ DepartmentStatsWidget [Sort: 2 - Admin]
│  └─ 3 Stats: Total Depts, Total Employees, Top Dept
│
├─ ContractOverviewWidget [Sort: 3 - Admin]
│  └─ 3 Stats: Active, Inactive, Renewal Rate
│
├─ ContractTypeDistributionWidget [Sort: 4 - Admin]
│  └─ Chart (Bar): Contracts by Type
│
├─ DepartmentChartWidget [Sort: 5 - Admin]
│  └─ Chart (Bar): Employees per Department
│
├─ ContractStatusChartWidget [Sort: 6 - Admin]
│  └─ Chart (Pie): Contracts by Status
│
└─ AttendanceChartWidget [Sort: 7 - Admin]
   └─ Chart (Line): Daily Attendance Trend
```

---

## 📊 MATRIZ DE DADOS COBERTOS

```
                  ADMIN  │  HR   │ EMPLOYEE  │ GLOBAL
─────────────────────────┼───────┼───────────┼────────
Departamentos      ✅     │   -   │    -      │   -
Contratos          ✅     │  ✅   │    -      │  ✅
Presença           ✅     │   -   │    ✅     │  ✅
Licenças/Férias    -      │  ✅   │    ✅     │  ✅
Banco de Horas     -      │   -   │    ✅     │   -
Funcionários       -      │  ✅   │    -      │  ✅
Benefícios         ❌     │   -   │    -      │   -
Worklogs           ❌     │   -   │    -      │   -
```

---

## 🎨 TIPOS DE WIDGET

### [Stats] StatsOverviewWidget - 11 widgets
```
├── Admin (3)
│   ├── DepartmentStatsWidget
│   ├── ContractOverviewWidget
│   └── AttendanceOverviewWidget
│
├── HR (3)
│   ├── EmployeeDirectoryWidget
│   ├── ContractExpirationAlertWidget
│   └── PendingTimeoffsWidget
│
├── Employee (3)
│   ├── HourBankDetailWidget
│   ├── MyAttendanceWidget
│   └── MyTimeoffHistoryWidget
│
└── Global (2)
    └── GeneralStats
```

### [Chart] ChartWidget - 3 widgets (Admin only)
```
├── DepartmentChartWidget [Bar Chart]
├── ContractStatusChartWidget [Pie Chart]
├── ContractTypeDistributionWidget [Bar Chart]
└── AttendanceChartWidget [Line Chart]
```

### [Custom] Custom Widget + Blade - 1 widget (Global)
```
└── EmployeeInfoWidget [uses: filament.widgets.employee-info-widget.blade.php]
```

---

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

### Completado ✅
- [x] DepartmentStatsWidget
- [x] DepartmentChartWidget
- [x] ContractOverviewWidget
- [x] ContractStatusChartWidget
- [x] ContractTypeDistributionWidget
- [x] AttendanceOverviewWidget
- [x] AttendanceChartWidget
- [x] EmployeeDirectoryWidget
- [x] ContractExpirationAlertWidget
- [x] PendingTimeoffsWidget
- [x] MyTimeoffHistoryWidget
- [x] MyAttendanceWidget
- [x] HourBankDetailWidget
- [x] GeneralStats
- [x] EmployeeInfoWidget

### Proposto 🔄
- [ ] BenefitsDistributionWidget [Admin]
- [ ] HourBankSummaryWidget [Admin]
- [ ] MyWorklogsWidget [Employee]
- [ ] WorklogComplianceWidget [HR]
- [ ] TimeoffTrendChartWidget [Admin]

---

## 🚀 INDICADORES DE SAÚDE

| Aspecto | Status | Detalhes |
|---------|--------|----------|
| Cobertura Admin | ✅ Excelente | 7 widgets cubrem principais áreas |
| Cobertura HR | 🟡 Bom | 3 widgets, sem charts |
| Cobertura Employee | ✅ Completo | 3 widgets focados e independentes |
| Sem Blade | ✅ 93% | Apenas 1 widget usa Blade |
| Performance | 🟡 Revisar | Possíveis N+1 em queries |
| Organização | ✅ Excelente | Separação clara por painel/categoria |
| Testes | 🔴 Vazio | Nenhum teste unitário encontrado |

---

## 📞 GUIA RÁPIDO

### "Como adicionar um novo widget?"
1. Criar arquivo em `app/Filament/Widgets/{Admin|HR|Employee}/`
2. Estender `StatsOverviewWidget` ou `ChartWidget`
3. Registrar em respectivo `PanelProvider`
4. Definir `protected static ?int $sort = X`

### "Como verificar um widget?"
1. Abrir arquivo em `app/Filament/Widgets/{categoria}/`
2. Verificar método `getStats()` ou `getData()`
3. Testar no respectivo painel Filament

### "Onde estão os dados?"
1. Models em `app/Models/`
2. Queries são simples em `getStats()` / `getData()`
3. Sem Blade template (exceto EmployeeInfoWidget)

### "Como otimizar?"
1. Adicionar `->with()` em queries (eager load)
2. Usar `->select()` para pegar apenas colunas necessárias
3. Adicionar índices no banco de dados
4. Cache de widgets frequentes

---

**Gerado:** 2026-02-10  
**Versão:** 1.0  
**Status:** ✅ Documentação Completa
