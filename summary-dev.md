# Resumo do Desenvolvimento da Aplicação PAP

## Visão Geral

Este documento resume o trabalho que desenvolvi e as decisões técnicas tomadas até 4 de Fevereiro de 2026. Descrevo as funcionalidades principais, os marcos do desenvolvimento e as ações que recomendo para os próximos passos. O sistema evoluiu para múltiplos painéis (Admin, HR, Funcionário, App) com políticas de acesso refinadas, notificações personalizadas e interface totalmente localizada para PT-PT.

---

## Registo de Alterações (Quinzenal)

- **2025-09-25 — Início do projeto:** Iniciei o repositório, defini a estrutura básica do projeto e preparei o ambiente Laravel para desenvolvimento (contêineres, scripts de inicialização e padrões de codificação).

- **2025-10-10 — Estrutura e modelos iniciais:** Defini a arquitetura de pastas e criei os primeiros modelos (User, Employee, Department, Contract). Configurei fábricas e estruturas de teste para acelerar desenvolvimento de features.

- **2025-10-25 — Migrações e autenticação:** Desenvolvi as migrações iniciais e seeders; implementei o sistema de autenticação com roles e permissões básicas para suportar os primeiros testes end-to-end.

- **2025-11-10 — Attendance e Hourbank:** Implementei o sistema de registo de ponto (`Attendance`) e o modelo de banco de horas (`Hourbank`), incluindo regras de negócio para acumulação e contabilização de horas.

- **2025-11-25 — Filament e frontend:** Comecei a construir as páginas administrativas com Filament e configurei a integração com o frontend via Vite, criando componentes e rotas iniciais para os painéis.

- **2025-12-10 — Painel de Funcionários:** Criei o Painel de Funcionários com controlo de acesso por funções, dashboards personalizados e middlewares para isolar as responsabilidades de cada painel.

- **2025-12-25 — Testes automatizados:** Garanti cobertura inicial com testes unitários e de integração usando Pest e PHPUnit; corrigi regressões encontradas durante esse ciclo.

- **2026-01-08 — Localização e políticas:** Completei a tradução da interface para PT-PT, refinei políticas de acesso e corrigi testes unitários para refletir as mudanças de mensagens e labels.

- **2026-01-09 — Notificações e automações:** Implementei notificações customizadas para criação de registros (Employees, Contracts, etc.) e automatizei a criação do banco de horas quando um novo funcionário é registado.

- **2026-01-15 — UI e Dashboards:** Redesenhei o dashboard do Admin, unifiquei a página de login e adicionei widgets que mostram métricas críticas (contratos, férias, horas), melhorando a usabilidade.

- **2026-01-16 — Permissões e painéis adicionais:** Corrigi regras de políticas que causavam acessos indevidos, simplifiquei o dashboard de funcionários e adicionei painéis separados para App e HR com regras de acesso específicas.

- **2026-01-17 — Notificações de edição:** Adicionei notificações para edições de itens importantes, com mensagens e payloads consistentes para integrações futuras.

- **2026-01-18 — Ajustes HR:** Refinei regras e fluxos para utilizadores com função HR, garantindo que possam gerir funcionários e contratos sem permissões destrutivas.

- **2026-01-20 — Segurança de acesso inicial:** Padronizei senhas iniciais para novos usuários e forcei a troca de senha no primeiro acesso para melhorar a segurança do sistema.

- **2026-02-03 — Correções finais e documentação:** Realizei um ciclo de correções finais, atualizei a documentação do projeto e preparei o repositório para entrega/testes de homologação.

- **2026-02-04 — Exportação de contratos em PDF:** Implementei geração e download de contratos em PDF usando `barryvdh/laravel-dompdf`. Instalei o pacote, criei `ContractPdfController`, adicionei a view `resources/views/contracts/pdf.blade.php`, registrei a rota `contracts.download` e integrei uma ação de download no `ContractResource` do Filament.

- **2026-02-04 — Validação de acesso por Painel:** Implementei uma validação que garante que o `role` do utilizador corresponde ao painel que está a tentar aceder. Criei o middleware `EnsurePanelRole` que é executado após a autenticação nos `PanelProvider`s (`Admin`, `HR`, `Employee`); quando o role não corresponde o utilizador recebe uma página de "Acesso Negado" com um botão que o leva à página de login correta: `/admin/login`, `/hr/login` ou `/employee/login`. Adicionei também um teste de integração inicial (`tests/Feature/PanelRoleMiddlewareTest.php`) para validar o comportamento.

- **2026-02-04 — Política de senhas e troca forçada:** Adicionei suporte para política de senha inicial e fluxo de troca obrigatória. Criei a migration que adiciona `must_change_password` e `password_changed_at` à tabela `users`, atualizei o `User` model para preencher automaticamente uma senha padrão (definível via env `DEFAULT_USER_PASSWORD`) quando uma senha não é fornecida, e marquei `must_change_password=true` nesses casos. Atualizei a `UserFactory` para usar a senha padrão e marcar `must_change_password`. Implementei o middleware `EnforcePasswordChange` que redireciona utilizadores com `must_change_password=true` para a página `/password/change` até atualizarem a senha. Criei as rotas, controller (`UserPasswordController`) e view para a alteração de senha e adicionei testes (`tests/Feature/PasswordPolicyTest.php`).

- **2026-02-10 — Widgets gráficos e visualizações de dados:** Implementei 9 novos widgets para enriquecer os dashboards: 3 widgets de estatísticas para cada painel (Admin, HR, Funcionário) seguindo o padrão `StatsOverviewWidget` sem dependência de arquivos Blade. Adicionei 4 widgets gráficos avançados ao painel Admin: `DepartmentChartWidget` (gráfico de barras horizontal), `ContractStatusChartWidget` (gráfico donut), `AttendanceChartWidget` (gráfico de linhas multi-série) e `ContractTypeDistributionWidget` (gráfico radar). Todos os widgets usam a paleta de cores corporativa (#582f0e, #7f4f24, #936639, etc.) e são totalmente responsivos.

- **2026-02-10 — Correção de cálculos de horas e pausa:** Revisei e corrigiu a lógica de cálculo de horas trabalhadas, extras e pausa na tabela `Attendance`. Identifiquei e eliminei o uso de `intdiv()` que truncava decimais (8h45m virava 8h), alterando para `round($workedMinutes / 60, 2)` que preserva precisão com 2 casas decimais. Atualizei os casts dos modelos: `hours_worked` e `extra_hours` são agora `float` ao invés de `integer`, permitindo armazenar valores como 8.75 (8h45m). Adicionei validação e logging para pausas negativas, melhorando a detecção de erros de entrada. Atualizei a exibição na tabela para `number_format($state, 2)` mostrando 8.75h ao invés de 8h.

---

## 1. Fundação do Projeto

- **Framework:** Usei Laravel 12 como backbone do projeto.
- **Painéis:** Implementei painéis com Filament 3 para Admin, HR e Funcionário.
- **Linguagem:** Desenvolvi em PHP 8.1+ com melhores práticas de PSR.
- **Base de Dados:** Optei por MySQL para persistência.
- **Estrutura Principal:**
    - Estruturei modelos, fábricas, migrações e seeders para permitir testes automatizados e rápida iteração.
    - Usei Recursos Filament para CRUD e construção da interface administrativa.
    - Centralizei autorização via Policies e Gates para manter regras consistentes.
    - Criei uma camada de serviços para funções transversais (acesso, relatórios, notificações).
    - Adicionei middleware dedicado para isolar rotas e responsabilidades por painel.

---

## 2. Gestão de Funções e Autorização

- **Funções:** Defini roles principais: `ADMIN`, `HR` e `EMPLOYEE` através de uma enumeração `UserRole`.
- **Controlo de Acesso:**
    - Implementei policies detalhadas que restringem ações com granularidade por recurso e painel.
    - Concedi a HR permissões para criar/editar funcionários, contratos e gerir férias, mas sem capacidade de deleção crítica.
    - Limitei funcionários à gestão dos seus próprios registos e submissões.
    - Modelei gates e um serviço de acesso para centralizar verificações reutilizáveis.
    - Configurei middleware que aplica automaticamente as regras de acesso por rota e painel.

---

## 3. Localização da Interface de Utilizador

- Traduzi todo o front-end administrativo e labels para Português (PT-PT): recursos, rótulos, validações e mensagens.
- Garanti consistência terminológica em Recursos Filament como Employee, Timeoff, Contract, ContractType, entre outros.

---

## 4. Funcionalidades de Registo de Trabalho e Notificações

- **Rastreamento de Pausas:** Acrescentei colunas `break_start` e `break_end` às migrations, atualizei modelos e factories e acrescentei selectors no formulário para gerir pausas.
- **Validação de Pausas:** Implementei validações que asseguram que pausas ficam dentro do período de trabalho e não excedem 2 horas.
- **Cálculo de Horas:** Calculei `hours_worked` e `extra_hours` subtraindo pausas, e normalizei resultados para valores inteiros para facilitar relatórios.
- **Notificações:** Criei notificações customizadas para eventos de criação e edição; desativei notificações genéricas do Filament onde apropriado e adicionei confirmação de ações em formulários.

---

## 5. Melhorias da Interface de Utilizador

- **Distintivos de Função:** Exibi roles como badges coloridos usando `TextColumn` com HTML customizado em vez de `BadgeColumn` para manter controle total do estilo.
- **Ações e Dashboards:** Restrinjo ações sensíveis (editar, eliminar) a `ROOT`, implementei filtros contextuais (ex.: funcionário vê apenas seus registos) e desenvolvi dashboards com widgets que mostram métricas-chave (contratos, férias, horas trabalhadas).
- **Autenticação:** Unifiquei a página de login, tornando-a responsiva e consistente entre painéis.

---

## 6. Validação e Testes

- Executei testes para migrações e seeders em ambiente de CI.
- Mantive análise estática contínua (linters) para garantir zero erros de sintaxe após mudanças.
- Escrevi e corrigi testes unitários e de integração com Pest/PHPUnit durante o desenvolvimento.
- Validei transformações de tempo para evitar inconsistências entre a UI e a base de dados.

---

## 7. Recomendações e Próximas Etapas

- Recomendo executar as migrações e seeders em ambiente de staging antes de produção.
- Sugiro testar todos os painéis Filament para permissões, dashboards e fluxos críticos.
- Recomendo adicionar testes automatizados específicos para a geração e download de PDFs (verificar cabeçalho, A4, incorporação de fontes e localização).
- Considero úteis melhorias futuras: fluxo de aprovação de horas extras, relatórios analíticos, exportação CSV e notificações avançadas.

---

## 8. Widgets e Dashboards Avançados

- **Widgets de Estatísticas:** Implementei 9 widgets seguindo o padrão `StatsOverviewWidget` (sem dependência Blade):
  - **Admin:** `GeneralStats` (usuários, colaboradores, contratos, férias), `DepartmentStatsWidget` (contagem de departamentos), `ContractOverviewWidget` (contratos ativo/inativo/expirando), `AttendanceOverviewWidget` (taxa de presença, faltas, média de horas)
  - **HR:** `EmployeeDirectoryWidget`, `PendingTimeoffsWidget`, `ContractExpirationAlertWidget`
  - **Funcionário:** `MyTimeoffHistoryWidget`, `MyAttendanceWidget`, `HourBankDetailWidget`

- **Widgets Gráficos (ChartWidget):** Adicionei 4 gráficos interativos ao painel Admin com Chart.js:
  - `DepartmentChartWidget` (Bar chart horizontal) - Top 10 departamentos por colaboradores
  - `ContractStatusChartWidget` (Doughnut chart) - Distribuição de status (Ativo/Encerrado/Suspenso)
  - `AttendanceChartWidget` (Line chart) - Tendências diárias de presença (3 séries: Presentes/Ausentes/Atrasados)
  - `ContractTypeDistributionWidget` (Radar chart) - Distribuição por tipo de contrato

- **Responsividade:** Todos os widgets adaptam-se a desktop, tablet e mobile com `responsive: true` e `maintainAspectRatio: true`.
- **Cores:** Utilizadade da paleta corporativa (#582f0e primária, #7f4f24 principal, #c2c5aa sucesso, #a68a64 perigo).

---

## 9. Melhorias no Cálculo e Registo de Horas

- **Cálculo de Horas com Decimais:**
  - Corrigido: Antes usava `intdiv()` que truncava (8h45m → 8h). Agora usa `round($workedMinutes / 60, 2)` preservando decimais (8h45m → 8.75h).
  - Casts atualizados: `hours_worked` e `extra_hours` são now `float` (antes eram `integer`).
  - Precisão: Mantém 2 casas decimais em toda a cadeia de cálculo (Model, Resource, View).

- **Validação de Pausa:**
  - Adicionado logging quando pausa negativa é detectada (erro de entrada do utilizador).
  - Reseta automaticamente para 0 com aviso nos logs (facilitará debugging).

- **Display na Interface:**
  - Tabelas agora mostram `8.75h` (número_format com 2 casas) ao invés de `8h`.
  - Formulário exibe hints: "Calculado automaticamente (ex: 8.50 = 8h30m)" e "Acima de 8 horas/dia".

---

## 10. Estado

## 10. Estado

- Implementei e validei todas as funcionalidades solicitadas.
- A funcionalidade de exportação de contratos em PDF está disponível e testada localmente.
- As políticas de acesso, notificações e dashboards estão em funcionamento conforme especificado.
- **Novo:** 9 widgets de estatísticas integrados nos 3 painéis (Admin, HR, Funcionário).
- **Novo:** 4 gráficos avançados no painel Admin (Bar, Doughnut, Line, Radar) com dados em tempo real.
- **Novo:** Cálculo de horas corrigido com precisão decimal (8.75h ao invés de truncar para 8h).
- A aplicação está estável e pronta para testes finais, homologação e implementação.

---

_Última atualização: 10 de Fevereiro de 2026_
