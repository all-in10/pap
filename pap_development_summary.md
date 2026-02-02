# Resumo do Desenvolvimento da Aplicação PAP

## Visão Geral
Este documento resume o processo de desenvolvimento, decisões arquitetónicas e funcionalidades principais implementadas na aplicação PAP até 2 de Fevereiro de 2026.

---

## Registo de Alterações

- **2026-02-02**: Melhorias de auditoria, widgets, layout e internacionalização.
  - Branch ativa: `a-santa-redencao`, sincronizada com `origin/a-santa-redencao`.
  - Auditoria reforçada: todos os modelos principais (User, Employee, Worklog) agora registam criação, edição e remoção automaticamente nos audit logs. Tentativas de acesso negado a painéis também são auditadas.
  - Migração de audit logs atualizada para incluir campo `description`.
  - Seeder atualizado para criar registos de auditoria ao criar utilizadores.
  - Tradução completa para PT-PT, incluindo mensagens de validação e interface.
  - PasswordChangeController ajustado para mensagens e lógica em português.
  - Novo layout do dashboard admin: widgets compactos, responsivos e organizados em 3 colunas.
  - Novos widgets Filament:
    - `GlobalAverageHours`: média semanal de horas por colaborador.
    - `WeeklyAverageBulletChart`: gráfico de pontos mostrando evolução semanal de horas.
    - `DepartmentDistributionChart`: gráfico doughnut da distribuição de pessoal por departamento.
  - Widget `TimeoffsChart` ajustado para visualização doughnut e cores consistentes.
  - Removido teste `PanelRootAccessTest.php` (não relevante para o fluxo atual).
  - Diretório `resources/lang/pt-pt` criado para traduções personalizadas.
  - Vários ficheiros modificados para refletir as melhorias acima.

- **2026-02-01**: Correção de aviso Intelephense, reforço do fluxo de login e políticas de acesso a painéis.
  - Corrigido o aviso Intelephense P1013 "Undefined method 'user'" em `AuditLogResource` (tipagem e assinatura `Tables\Table`).
  - Adicionada a importação `use Illuminate\Support\Facades\Auth;` e substituído `auth()->user()` por `Auth::user()` em `app/Filament/Resources/AuditLogResource.php` para resolver o diagnóstico de análise estática.
  - Implementado fallback de redirecionamento de login: o `AuthController` descarta `url.intended` quando aponta para um painel que não corresponde ao role do usuário e redireciona de forma segura para `panelPath()`.
  - Adicionado `panelPath()` em `app/Models/User.php` para centralizar destinos por role e remediar erros de análise estática relacionados.
  - Criado listener `EnforceIntendedPanelOnLogin` (registrado em `EventServiceProvider`) que intercepta o evento `Login` e substitui o `url.intended` quando aponta para um painel que não corresponde ao role do usuário — isso cobre logins via Filament e outras rotas de forma uniforme.
  - Refatorado middleware `RedirectAuthenticatedFromLogin` para sempre redirecionar autenticados diretamente para `$user->panelPath()` (melhora consistência e evita redirecionamentos erróneos).
  - Ajustados middlewares `EnsureAdminPanelAccess`, `EnsureHrPanelAccess` e `EnsureEmployeePanelAccess` para usarem `panelPath()` como destino de fallback e registar tentativas negadas no sistema de auditoria (`Audit::recordPanelAccessDenied`).
  - Adicionados testes de integração (`tests/Feature/PanelAccessTest.php`) cobrindo cenários críticos: HR não acede ao admin, admin não acede ao employee, e override do `url.intended` no login.
  - Atualizado `bootstrap/providers.php` para registar `EventServiceProvider` e garantir que o listener de login esteja activo.
  - Recomenda-se reindexar o servidor de linguagem (Intelephense) e executar a suíte de testes (pest) para validar todas as mudanças.


- **2026-01-20**: Padronização de senha padrão para usuários.
  - Adicionado evento `creating` no modelo `User` para definir senha padrão "passexemplo123" (hasheada) e `must_change_password = true` quando a senha não for fornecida na criação.
  - Atualizado `UserFactory` para usar a mesma senha padrão e flag de mudança obrigatória.
  - Isso garante que novos usuários tenham uma senha placeholder e sejam obrigados a alterá-la no primeiro acesso.

- **2026-01-16**: Correção da política de mudança de password, remoção de departamentos do dashboard de funcionários e simplificação do layout.
  - Corrigido middleware `ForcePasswordChange` para redirecionar corretamente para `/password/change` em vez de `/admin/change-password`, e expandido para cobrir todos os painéis (app, hr, admin).
  - Atualizado `PasswordChangeController` para redirecionar para `/app` após mudança de password bem-sucedida.
  - Adicionado controlo de acesso `canAccess()` ao `DepartmentResource` para restringir visibilidade apenas a HR, Admin e Root (escondendo de funcionários).
  - Simplificado dashboard removendo `TimeoffsChart` e alterando layout para coluna única (de 2 para 1 coluna).
  - Testes manuais: política de password agora funciona corretamente; departamentos não aparecem no menu de funcionários; dashboard mais limpo e simples.

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

- **2026-01-08**: Traduções para PT-PT e correções de testes unitários.
  - Traduzi rótulos de navegação, campos de formulários, colunas de tabelas, infolists e mensagens de validação para **Português (PT-PT)** em vários Resources do Filament: `City`, `State`, `Country`, `Employee`, `User`, `Contract`, `Worklog`, `Timeoff`, `Hoursbank`, `Designation`, `Department`, `ContractType`, bem como nas páginas `Settings` e `ChangePassword`.
  - Ajustei mensagens de validação e textos dinâmicos (ex.: mensagens de erro de intervalos, opções de status e labels de botões) para português e coerência UX.
  - Corrigi regras de autorização que afetavam testes: `Access::hasRole` foi ajustado para tratar `employee` como igualdade estrita; `WorklogPolicy` foi atualizado para permitir que HR/Admin/Root façam update/delete de worklogs.
  - Rodei a suíte de testes e confirmei que agora todos os testes passam (8 passed).
  - Commits relacionados:
    - `ed6a659` — i18n: traduzir labels/forms/tables/resources para PT-PT
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

- **2026-01-16**: Criação de painéis adicionais, correções de Vite e melhorias na página de login.
  - Criado `AppPanelProvider` (`app/Providers/Filament/AppPanelProvider.php`) com caminho `/app` e login em `/app/login`, configurado como painel padrão com widgets compartilhados (StatsOverview, ContractsChart, TimeoffsChart).
  - Criado `HrPanelProvider` (`app/Providers/Filament/HrPanelProvider.php`) com caminho `/hr` para área dedicada ao RH, incluindo dashboard (`app/Filament/Hr/Pages/Dashboard.php`) com widgets compartilhados para acesso às mesmas informações com controle de níveis.
  - Implementado middleware `EnsureHrPanelAccess` (`app/Http/Middleware/EnsureHrPanelAccess.php`) para restringir acesso ao painel HR apenas para roles HR, ADMIN e ROOT, redirecionando outros conforme o role.
  - Removido `@vite` de `welcome.blade.php` e `login.blade.php` para corrigir erro de manifest não encontrado, substituindo por estilos inline de Tailwind para evitar dependência de Vite.
  - Atualizado `login.blade.php` para incluir o logo TeamCore via `@include('filament.brand')` e corrigido `brand.blade.php` para usar logo transparente em light/dark mode.
  - Atualizado `routes/web.php` para redirecionar `/login` para `/app/login`.
  - Registrados `AppPanelProvider` e `HrPanelProvider` em `bootstrap/providers.php`.
  - Testes manuais: painéis acessíveis conforme roles (HR/Admin/Root para /hr, todos para /app); login redireciona corretamente; UI consistente com Filament sem erros de Vite.

- **2026-01-17**: Implementação de notificações para edições de itens.
  - Criado trait `NotifiesUpdatedItems` (`app/Traits/NotifiesUpdatedItems.php`) para enviar notificações separadas por item editado, persistindo no histórico de notificações e suprimindo notificações padrão do Filament.
  - Integrado trait em todas as páginas de edição (`Edit*`) — Employee, User, Contract, Hoursbank, Designation, ContractType, Department, Country, State, City, Worklog, Timeoff — para gerar notificações customizadas ao salvar edições.
  - Adicionado método `afterSave()` em cada página Edit para chamar `notifyUpdatedItems` com mensagem específica (ex.: 'Funcionário ID: {id} atualizado.').
  - Testes manuais: notificações aparecem corretamente ao editar itens; notificações padrão do Filament suprimidas.

- **2026-01-18**: Ajustes nas políticas de acesso para usuários HR.
  - Atualizado `WorklogPolicy` para restringir delete apenas a Admin/Root (removido HR da permissão de delete).
  - Adicionado `canAccess()` em `UserResource` para permitir acesso apenas a Admin/Root, bloqueando HR e outros.
  - Políticas existentes (Employee, Timeoff, Contract) já restringem delete a Admin/Root, permitindo HR create/update/view.
  - Testes manuais: HR pode criar/editar mas não deletar itens; HR não acessa UserResource; Admin/Root mantêm acesso completo.

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

_Última atualização: 20 de Janeiro de 2026