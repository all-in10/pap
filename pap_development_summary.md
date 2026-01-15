# Resumo do Desenvolvimento da Aplicação PAP

## Visão Geral
Este documento resume o processo de desenvolvimento, decisões arquitetónicas e funcionalidades principais implementadas na aplicação PAP até 15 de Janeiro de 2026.

---

## Registo de Alterações

- **2026-01-15**: Melhorias no dashboard admin, nova página de login unificada e correções de UI.
  - Criado novo dashboard personalizado para admin (`app/Filament/Admin/Pages/Dashboard.php`) com widgets de estatísticas gerais (StatsOverview), gráfico de contratos por tipo (ContractsChart) e gráfico de solicitações de férias (TimeoffsChart).
  - Implementados widgets Filament: `StatsOverview` (visão geral de usuários, funcionários, contratos ativos e férias pendentes), `ContractsChart` (gráfico de barras para contratos por tipo) e `TimeoffsChart` (gráfico de rosca para status de solicitações de férias).
  - Criada nova página de login unificada (`resources/views/login.blade.php`) com links para painéis de funcionário e admin, substituindo a lógica anterior no `welcome.blade.php`.
  - Atualizada rota `/login` em `routes/web.php` para renderizar a nova view de login em vez de redirecionar.
  - Corrigido `welcome.blade.php` para remover links de login específicos e simplificar para um link genérico.
  - Aplicadas correções de dark mode em `settings.blade.php` (página de configurações) para melhor suporte a temas escuros.
  - Reorganizadas importações em `AdminPanelProvider.php` e `Employee.php` para melhor legibilidade.
  - Adicionada linha em branco no final de `User.php` para consistência de formatação.
  - Testes manuais: dashboard carrega corretamente com widgets funcionais; página de login redireciona adequadamente; UI responsiva em light/dark mode.

- **2026-01-09**: Notificações por item criado e criação automática de Banco de Horas.
  - Adicionei o trait `NotifiesCreatedItems` para enviar notificações separadas por item criado e o trait `SuppressesDefaultFilamentNotifications` para suprimir a notificação padrão do Filament.
  - Integrei notificações em todas as páginas de criação (`Create*`) — Employee, User, Contract, Hoursbank, Designation, ContractType, Department, Country, State, City, Worklog, Timeoff — para que cada item relacionado gere a sua própria notificação.
  - Adicionei criação automática de `Hoursbank` (com `total_hours = 0`) no gancho `Employee::created` quando não existia um registo associado.
  - Adicionei notificação específica que avisa quando um `Hoursbank` é criado (ex.: 'Banco de Horas criado (ID: {id}) para funcionário ID: {employee_id}').
  - Corrigi `canAccess()`/`canCreate()` em resources relevantes e atualizei as páginas de listagem para usar `::canCreate()` para exibir corretamente o botão "Criar".
  - Testes manuais realizados: notificações custom aparecem corretamente e a notificação padrão do Filament foi suprimida.
  - Apliquei confirmação de cancelamento ao cancelar edições (trait `ConfirmsCancelAction`) em todas as páginas Create/Edit do Filament para prevenir perda não intencional de alterações; commitei as mudanças (commit: `7df642e`).

- **2026-01-08**: Traduções para PT-BR e correções de testes unitários.
  - Traduzi rótulos de navegação, campos de formulários, colunas de tabelas, infolists e mensagens de validação para **Português (pt‑BR)** em vários Resources do Filament: `City`, `State`, `Country`, `Employee`, `User`, `Contract`, `Worklog`, `Timeoff`, `Hoursbank`, `Designation`, `Department`, `ContractType`, bem como nas páginas `Settings` e `ChangePassword`.
  - Ajustei mensagens de validação e textos dinâmicos (ex.: mensagens de erro de intervalos, opções de status e labels de botões) para português e coerência UX.
  - Corrigi regras de autorização que afetavam testes: `Access::hasRole` foi ajustado para tratar `employee` como igualdade estrita; `WorklogPolicy` foi atualizado para permitir que HR/Admin/Root façam update/delete de worklogs.
  - Rodei a suíte de testes e confirmei que agora todos os testes passam (8 passed).
  - Commits relacionados:
    - `ed6a659` — i18n: traduzir labels/forms/tables/resources para PT-BR
    - `3fcc187` — fix(tests): ajustar Access::hasRole e WorklogPolicy para corresponder às expectativas dos testes

- **2025-12-10**: Implementar Painel de Funcionários com controlo de acesso baseado em funções.
  - `app/Providers/Filament/EmployeePanelProvider.php`: criado novo fornecedor de painel para funcionários no caminho `/employee` com login e autenticação dedicados.
  - `app/Filament/Pages/EmployeeDashboard.php`: página do painel mostrando histórico de registos de trabalho do funcionário, departamento, total de banco de horas e formulário de pedido de férias.
  - `resources/views/filament/pages/employee-dashboard.blade.php`: vista renderizando tabela de registos de trabalho, banco de horas, informação de departamento, formulário de pedido de férias e pedidos de férias submetidos.
  - `app/Http/Middleware/EnsureEmployeePanelAccess.php`: middleware restringindo painel `/employee` apenas para role EMPLOYEE; redireciona outros para `/admin`.
  - `app/Http/Middleware/EnsureAdminPanelAccess.php`: middleware restringindo painel `/admin` apenas para roles ADMIN/ROOT; redireciona outros para `/employee`.
  - `app/Http/Middleware/RedirectAuthenticatedFromLogin.php`: middleware redirecionando utilizadores autenticados de páginas de login para os seus respetivos painéis baseado em role.
  - `bootstrap/providers.php`: registado `EmployeePanelProvider` para descoberta de painel Filament.
  - `bootstrap/app.php`: registada middleware global `RedirectAuthenticatedFromLogin` na pilha de middleware web.
  - `app/Providers/Filament/AdminPanelProvider.php`: adicionada middleware `EnsureAdminPanelAccess` para restringir acesso ao painel admin.
  - `app/Providers/Filament/EmployeePanelProvider.php`: adicionada middleware `EnsureEmployeePanelAccess` para restringir acesso ao painel de funcionários.
  - `routes/web.php`: adicionadas rotas para redirecionar utilizadores autenticados de páginas de login para os seus painéis corretos.
  - `app/Models/Employee.php`: adicionada relação `timeoffs()` hasMany para aceder aos registos de férias do funcionário.
  - Funcionalidades do painel de funcionários: visualizar histórico de registos de trabalho, departamento, banco de horas, submeter pedidos de férias/justificativas de ausência, acompanhar o estado de pedidos de férias.

- **2025-12-03**: Introduzir comportamento de registo automático pós-alteração e registar edições recentes.
  - `database/factories/WorklogFactory.php`: converter `hours_worked` e `extra_hours` para inteiros para que os dados de registos de trabalho semeados utilizem valores de horas inteiras.
  - `app/Filament/Resources/WorklogResource.php`: colunas da tabela `hours_worked` e `extra_hours` agora apresentam inteiros completos (ex. `8h`) e lógica de cor atualizada para usar conversão de inteiros.
  - `app/Models/Worklog.php`: gancho de gravação do modelo atualizado para converter `hours_worked` e `extra_hours` para inteiros para impor arredondamento de horas inteiras no momento da gravação.
  - `pap_development_summary.md`: adicionada esta secção de Registo de Alterações; o assistente irá acrescentar entradas aqui após futuras alterações de código conforme solicitado pelo utilizador.

- **2025-12-04**: Ajustar cálculo de horas para excluir tempo de pausa (alteração ao nível do modelo).
  - `app/Models/Worklog.php`: gancho de gravação refatorizado para calcular minutos totais entre `start_time` e `end_time`, subtrair minutos de `break_start`/`break_end`, depois armazenar `hours_worked` como horas inteiras usando divisão de piso. `extra_hours` agora calculado a partir de `hours_worked` inteiro.
  - Conversões para `hours_worked` e `extra_hours` alteradas para `integer` para refletir armazenamento de horas inteiras.
  - Isto garante que o tempo de pausa é totalmente excluído de `hours_worked` e cálculos subsequentes (Hoursbank, tabelas, fábrica) permanecem consistentes.

- **2025-12-04**: Implementar isolamento de dados ao nível do funcionário para proteção de dados melhorada.
  - `app/Filament/Resources/EmployeeResource/Pages/ListEmployees.php`: adicionada substituição `getTableQuery()` para filtrar por ID do funcionário registado quando role == EMPLOYEE.
  - `app/Filament/Resources/WorklogResource/Pages/ListWorklogs.php`: adicionada substituição `getTableQuery()` para filtrar registos de trabalho por ID do funcionário registado quando role == EMPLOYEE.
  - `app/Filament/Resources/TimeoffResource/Pages/ListTimeoffs.php`: adicionada substituição `getTableQuery()` para filtrar registos de férias por ID do funcionário registado quando role == EMPLOYEE.
  - `app/Filament/Resources/HoursbankResource/Pages/ListHoursbanks.php`: adicionada substituição `getTableQuery()` para filtrar registos de banco de horas por ID do funcionário registado quando role == EMPLOYEE.
  - Todas as quatro páginas de lista agora importam a enumeração `UserRole` e verificam o role antes de aplicar filtro. Roles não-funcionário (ROOT, ADMIN, HR) veem todos os registos como antes.


## 1. Fundação do Projeto
- **Framework:** Laravel 12
- **Painel Admin:** Filament 3
- **Linguagem:** PHP 8.1+
- **Base de Dados:** MySQL
- **Estrutura Principal:**
  - Modelos, Fábricas, Migrações, Sementes
  - Recursos Filament para CRUD e Interface de Utilizador
  - Autorização via Políticas e Portões
  - Camada de Serviço para controlo de acesso

---

## 2. Gestão de Funções e Autorização
- **Funções:** ROOT, ADMIN, HR, EMPLOYEE (Enumeração UserRole)
- **Controlo de Acesso:**
  - Políticas restringem ações por função
  - Apenas ROOT pode editar/eliminar registos de trabalho
  - Funcionários podem apenas visualizar os seus próprios registos
  - Portões e serviço de Acesso padronizam verificações

---

## 3. Localização da Interface de Utilizador
- Todos os Recursos Filament traduzidos para Português:
  - Funcionário, Férias, Banco de Horas, Designação, Departamento, Contrato
  - Rótulos de navegação, campos de formulário, colunas de tabela, listas de informações

---

## 4. Melhorias de Funcionalidades de Registo de Trabalho
- **Rastreamento de Tempo de Pausa/Almoço:**
  - Migração adicionou colunas `break_start` e `break_end`
  - Modelo e Fábrica atualizados para lidar com tempos de pausa
  - Formulário inclui Seletores de Tempo para tempos de pausa
  - Validação garante que pausa está dentro das horas de trabalho e ≤ 2 horas
  - Tabela apresenta duração da pausa em formato legível por humanos

- **Cálculos de Horas:**
  - `hours_worked` e `extra_hours` calculados subtraindo duração da pausa
  - Todos os valores de horas arredondados para inteiros completos (sem decimais)
  - Fábrica gera dados de teste realistas com pausas

---

## 5. Melhorias da Interface de Utilizador
- **Distintivos de Função:**
  - Tabela UserResource apresenta funções como distintivos HTML coloridos
  - BadgeColumn preterida do Filament substituída por TextColumn + HTML personalizado

- **Ações Condicionais:**
  - Ações Editar/Eliminar apenas visíveis para utilizadores ROOT
  - Filtro de funcionário mostra apenas registos próprios

---

## 6. Validação e Testes
- Todas as migrações e sementes testadas com sucesso
- Análise estática confirma zero erros de sintaxe após cada alteração
- Análise de tempo flexível previne erros de formato entre Interface de Utilizador e BD

---

## 7. Recomendações e Próximas Etapas
- Executar migrações e sementes para aplicar o esquema mais recente
- Testar painel Filament para comportamento correto de tempo de pausa e arredondamento de horas
- Considerar melhorias: aprovação de horas extras, análises, exportação CSV, notificações

---

## 8. Estado
- Todas as funcionalidades solicitadas implementadas e validadas
- A aplicação está estável e pronta para testes adicionais ou implementação

---

_Última atualização: 15 de Janeiro de 2026