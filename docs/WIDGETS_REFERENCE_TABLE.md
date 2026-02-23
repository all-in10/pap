# 📋 WIDGETS - Tabela de Referência Rápida

## 📑 Tabela Completa de Todos os Widgets

| # | Nome do Widget | Panel | Tipo | Categoria | Arquivo | Sort | Descrição Rápida |
|---|---|---|---|---|---|---|---|
| 1 | GeneralStats | Global | Stats | - | `GeneralStats.php` | 1 | 📊 4 KPIs: Users, Employees, Contracts, Timeoffs |
| 2 | EmployeeInfoWidget | Global | Custom | - | `EmployeeInfoWidget.php` | - | 👤 Info do usuário logado + Banco horas + Licenças |
| 3 | AttendanceOverviewWidget | Admin | Stats | Presença | `Admin/AttendanceOverviewWidget.php` | 1 | ✅ Taxa %, Faltas, Média horas/dia |
| 4 | DepartmentStatsWidget | Admin | Stats | Departamentos | `Admin/DepartmentStatsWidget.php` | 2 | 📊 Total deptos, colaboradores, top depto |
| 5 | ContractOverviewWidget | Admin | Stats | Contratos | `Admin/ContractOverviewWidget.php` | 3 | 📋 Contratos ativos, inativos, taxa renovação |
| 6 | ContractTypeDistributionWidget | Admin | Chart | Contratos | `Admin/ContractTypeDistributionWidget.php` | 4 | 📊 Gráfico barras: Distribuição tipos contrato |
| 7 | DepartmentChartWidget | Admin | Chart | Departamentos | `Admin/DepartmentChartWidget.php` | 5 | 📈 Gráfico barras: Colaboradores por depto (top 10) |
| 8 | ContractStatusChartWidget | Admin | Chart | Contratos | `Admin/ContractStatusChartWidget.php` | 6 | 🥧 Gráfico pizza: Status contratos |
| 9 | AttendanceChartWidget | Admin | Chart | Presença | `Admin/AttendanceChartWidget.php` | 7 | 📉 Gráfico linha: Presença por dia do mês |
| 10 | EmployeeDirectoryWidget | HR | Stats | Funcionários | `HR/EmployeeDirectoryWidget.php` | - | 👥 Total, ativos, inativos |
| 11 | ContractExpirationAlertWidget | HR | Stats | Contratos | `HR/ContractExpirationAlertWidget.php` | - | ⚠️ Vencidos, vencendo 30/90 dias |
| 12 | PendingTimeoffsWidget | HR | Stats | Licenças | `HR/PendingTimeoffsWidget.php` | - | 📅 Pendentes, aprovadas, rejeitadas, próximas |
| 13 | MyAttendanceWidget | Employee | Stats | Presença | `Employee/MyAttendanceWidget.php` | - | ✅ Minha presença, faltas, atrasos (mês) |
| 14 | MyTimeoffHistoryWidget | Employee | Stats | Licenças | `Employee/MyTimeoffHistoryWidget.php` | - | 📅 Minhas solicitações (status) |
| 15 | HourBankDetailWidget | Employee | Stats | Banco Horas | `Employee/HourBankDetailWidget.php` | - | ⏰ Saldo, acumulado, utilizado |

---

## 🎯 Visualizar por Critério

### **POR PAINEL**

#### Admin Panel (7)
```
┌─────────────────────────────────────────────────────────┐
│ AttendanceOverviewWidget ........................... [1] │
│ DepartmentStatsWidget ............................. [2] │  Stats: 3
│ ContractOverviewWidget ............................ [3] │
│                                                         │
│ ContractTypeDistributionWidget ................... [4] │
│ DepartmentChartWidget ............................ [5] │  Charts: 4
│ ContractStatusChartWidget ........................ [6] │
│ AttendanceChartWidget ............................ [7] │
└─────────────────────────────────────────────────────────┘
```

#### HR Panel (3)
```
┌─────────────────────────────────────────────────────────┐
│ EmployeeDirectoryWidget         (Funcionários)          │  Stats Only: 3
│ ContractExpirationAlertWidget   (Contratos)             │
│ PendingTimeoffsWidget           (Licenças)              │
└─────────────────────────────────────────────────────────┘
```

#### Employee Panel (3)
```
┌─────────────────────────────────────────────────────────┐
│ MyAttendanceWidget              (Presença)              │  Stats Only: 3
│ MyTimeoffHistoryWidget          (Licenças)              │
│ HourBankDetailWidget            (Banco Horas)           │
└─────────────────────────────────────────────────────────┘
```

#### Global (2)
```
┌─────────────────────────────────────────────────────────┐
│ GeneralStats                    (Todos os painéis)      │  Stats: 1
│ EmployeeInfoWidget              (Todos os painéis)      │  Custom: 1
└─────────────────────────────────────────────────────────┘
```

---

### **POR TIPO**

#### 🟢 StatsOverviewWidget (11)
```
Admin:       AttendanceOverview, DepartmentStats, ContractOverview
HR:          EmployeeDirectory, ContractExpirationAlert, PendingTimeoffs
Employee:    MyAttendance, MyTimeoffHistory, HourBankDetail
Global:      GeneralStats
```

#### 🔵 ChartWidget (3)
```
Admin:       DepartmentChart, ContractStatusChart, ContractTypeDistribution, AttendanceChart
```

#### 🟡 Custom Widget (1)
```
Global:      EmployeeInfoWidget (com Blade template)
```

---

### **POR CATEGORIA DE DADOS**

#### 📊 Departamentos (2 widgets)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| DepartmentStatsWidget | Stats | Admin | Total, colaboradores, top |
| DepartmentChartWidget | Chart | Admin | Distribuição gráfica |

#### 📋 Contratos (5 widgets)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| ContractOverviewWidget | Stats | Admin | Ativos, inativos, taxa |
| ContractStatusChartWidget | Chart | Admin | Status (pie) |
| ContractTypeDistributionWidget | Chart | Admin | Tipos (bar) |
| ContractExpirationAlertWidget | Stats | HR | Vencimentos |
| GeneralStats | Stats | Global | Contratos ativos |

#### ✅ Presença (3 widgets)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| AttendanceOverviewWidget | Stats | Admin | Taxa, faltas, horas |
| AttendanceChartWidget | Chart | Admin | Evolução diária |
| MyAttendanceWidget | Stats | Employee | Presença pessoal |

#### 📅 Licenças/Férias (3 widgets)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| PendingTimeoffsWidget | Stats | HR | Status de solicitações |
| MyTimeoffHistoryWidget | Stats | Employee | Histórico pessoal |
| GeneralStats | Stats | Global | Pendentes |

#### ⏰ Banco de Horas (1 widget)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| HourBankDetailWidget | Stats | Employee | Saldo pessoal |

#### 👥 Funcionários (2 widgets)
| Widget | Tipo | Panel | Dados |
|--------|------|-------|-------|
| EmployeeDirectoryWidget | Stats | HR | Total, ativos |
| EmployeeInfoWidget | Custom | Global | Info completa |

---

## 📌 Dependencies & Models

### Cada Widget Usa:

| Widget | Models Usados | Relações |
|--------|---|---|
| GeneralStats | User, Employee, Contract, Timeoff | - |
| EmployeeInfoWidget | Employee, Hourbank, Timeoff | Auth::user() |
| AttendanceOverviewWidget | Attendance, Employee | whereDate |
| DepartmentStatsWidget | Department, Employee | withCount |
| ContractOverviewWidget | Contract | where status |
| ContractTypeDistributionWidget | Contract | groupBy type |
| DepartmentChartWidget | Department, Employee | withCount, orderBy |
| ContractStatusChartWidget | Contract | groupBy status |
| AttendanceChartWidget | Attendance, Employee | whereDate, count |
| EmployeeDirectoryWidget | Employee | where is_active |
| ContractExpirationAlertWidget | Contract | where end_date |
| PendingTimeoffsWidget | Timeoff | where status, where date |
| MyAttendanceWidget | Attendance, Employee | current user |
| MyTimeoffHistoryWidget | Timeoff, Employee | current user |
| HourBankDetailWidget | Hourbank, Employee | current user, latest |

---

## 🔍 Guia de Busca

### Procurando um widget que mostra...

**"...quantidade de..."**
→ Procure em `StatsOverviewWidget` (Admin/HR/Employee)

**"...gráfico, evolução, distribuição..."**
→ Procure em `ChartWidget` (Admin only)

**"...informações de departamento"**
→ `DepartmentStatsWidget` ou `DepartmentChartWidget`

**"...informações de contrato"**
→ `ContractOverviewWidget` (Admin) ou `ContractExpirationAlertWidget` (HR)

**"...informações de presença"**
→ `AttendanceOverviewWidget` (Admin) ou `MyAttendanceWidget` (Employee)

**"...informações de licenças/férias"**
→ `PendingTimeoffsWidget` (HR) ou `MyTimeoffHistoryWidget` (Employee)

**"...meu banco de horas"**
→ `HourBankDetailWidget` (Employee)

**"...info completa do usuário logado"**
→ `EmployeeInfoWidget` (Global)

**"...overview geral"**
→ `GeneralStats` (Global)

---

## 🚀 Ordem de Exibição no Admin (Sort Order)

```
Position 1: GeneralStats (Global)
Position 1: AttendanceOverviewWidget (Admin) ← Mesmo sort!
Position 2: DepartmentStatsWidget
Position 3: ContractOverviewWidget
Position 4: ContractTypeDistributionWidget
Position 5: DepartmentChartWidget
Position 6: ContractStatusChartWidget
Position 7: AttendanceChartWidget
```

**Nota:** `GeneralStats` e `AttendanceOverviewWidget` têm o mesmo sort=1. A ordem final depende da ordem de registro no `AdminPanelProvider`.

---

## 📊 Estatísticas de Implementação

### Por Tipo de Widget
```
StatsOverviewWidget:  11 (79%)  ████████████░░░░░░░░░░░░
ChartWidget:           3 (21%)  ███░░░░░░░░░░░░░░░░░░░░
Custom Widget:         1 (7%)   ░░░░░░░░░░░░░░░░░░░░░░░
```

### Por Panel
```
Admin:                 7 (50%)  ███████░░░░░░░░░░░░░░░░
HR:                    3 (21%)  ███░░░░░░░░░░░░░░░░░░░░
Employee:              3 (21%)  ███░░░░░░░░░░░░░░░░░░░░
Global:                2 (14%)  ██░░░░░░░░░░░░░░░░░░░░░
```

### Por Categoria
```
Contratos:             5 (36%)  █████░░░░░░░░░░░░░░░░░░
Departamentos:         2 (14%)  ██░░░░░░░░░░░░░░░░░░░░░
Presença:              3 (21%)  ███░░░░░░░░░░░░░░░░░░░░
Licenças:              3 (21%)  ███░░░░░░░░░░░░░░░░░░░░
Banco Horas:           1 (7%)   ░░░░░░░░░░░░░░░░░░░░░░░
Funcionários:          2 (14%)  ██░░░░░░░░░░░░░░░░░░░░░
```

---

## ✅ Matriz de Implementação

```
                  SEM BLADE       COM BLADE
StatsOverviewWidget    10              1 (EmployeeInfo)
ChartWidget             3              0
Custom Widget           0              1
─────────────────────────────────────────────
Total SEM Blade:       13
Total COM BLADE:        1
Percentual:        93% vs 7%
```

---

## 🔄 Fluxo de Dados (Resumido)

```
User Auth
   ↓
Panel Selection (Admin / HR / Employee)
   ↓
GeneralStats + Panel Widgets Loaded
   ↓
Models Queried
   ├─ Department.withCount()
   ├─ Contract.where()
   ├─ Attendance.whereDate()
   ├─ Timeoff.where()
   ├─ Employee.where()
   └─ Hourbank.latest()
   ↓
Widgets Rendered
   ├─ StatsOverviewWidget → HTML Cards
   ├─ ChartWidget → Chart.js
   └─ Custom Widget → Blade Template
   ↓
Dashboard Displayed
```

---

## 🎯 Checklist de Verificação

### Ao Editar um Widget:
- [ ] Arquivo está em `/app/Filament/Widgets/{Panel}/`?
- [ ] Nome da classe segue padrão `XyzWidget`?
- [ ] Estende `StatsOverviewWidget` ou `ChartWidget`?
- [ ] Método `getStats()` ou `getData()` implementado?
- [ ] Registrado no respective `PanelProvider`?
- [ ] Sort order definido (se houver)?
- [ ] Icon e color nos Stat::make()?

### Ao Criar um Widget Novo:
1. Decidir: StatsOverviewWidget ou ChartWidget?
2. Criar arquivo em pasta apropriada
3. Herdar classe base correta
4. Implementar método required
5. Usar Models apropriados
6. Registrar no PanelProvider
7. Testar no painel Filament
8. Documentar em WIDGETS_SUMMARY.md

---

**Tabela Atualizada:** 2026-02-10  
**Total de Widgets:** 14 (+ 5 propostos)  
**Status:** ✅ Pronto para referência
