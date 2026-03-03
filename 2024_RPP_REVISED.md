


Curso Profissional de Técnico de Informática-Sistemas 
Código de Referência do CNQ - 481039 
Ciclo de Formação  2023/2026 
Ano letivo   2025/2026

Prova de Aptidão Profissional
Relatório
TeamCore - Uma nova gestão

Autor:
Victor Gabriel Cristino Gomes	N.º 21
Orientador/a(es):
	Zélia Capitão

Data 
01/07/2026Agradecimentos a: 
Zélia Capitão
Ana Paula Azevedo
Jorge Miguel Pereira de Sousa Sequeiros
Willian Washington
Victor Santos







Índice

Acrónimos/abreviaturas/siglas	6
Resumo	7
Introdução	8
Desenvolvimento do projeto	9
Conclusão	10
Bibliografia	11
Anexos	12




Índice de figuras

Figura 1 – Diagrama de Entidades e Relações (ER)	13
Figura 2 – Dashboard Admin com Widgets de Estatísticas	14
Figura 3 – Painel HR - Gestão de Funcionários	14
Figura 4 – Painel Funcionário - Dados Pessoais e Banco de Horas	14
Figura 5 – Diagrama RBAC - Hierarquia de Papéis e Permissões	15
Figura 6 – Registo de Presença (Attendance) com Pausas	17
Figura 7 – Visualização do Banco de Horas (Hourbank)	17
Figura 8 – Notificações Toast ao Criar Funcionário	20
Figura 9 – Diálogo de Confirmação de Ações Destrutivas	20
Figura 10 – Badges Visuais de Funções e Estados de Contrato	21
Figura 11 – Interface Responsiva - Desktop vs Mobile	21
Figura 12 – Formulário EmployeeResource - Criação de Funcionário	24
Figura 13 – Fluxo Visual de Criação Automática (Employee → User → Contract → Hourbank)	24
Figura 14 – Resource EmployeeResource - Lista e Tabela	27
Figura 15 – Resource ContractResource com Ação de Download PDF	27
Figura 16 – Resource ActivityLogResource - Histórico de Auditoria	28



Índice de gráficos

Gráfico 1 – gráfico a	2



Índice de tabelas

Tabela 1 – tabela a	2



Acrónimos/abreviaturas/siglas

CRUD  	Create, Read, Update, Delete
CSRF  	Cross-Site Request Forgery
HR 	Human Resources
MVP  	Minimal Viable Product
ORM 	Object-Relational Mapping
PAP  	Prova de Aptidão Profissional
PDF  	Portable Document Format
PME 	Pequena e Média Empresa
PT-PT 	Português de Portugal
RBAC  	Role-Based Access Control
RH  	Recursos Humanos
UI  	User Interface
UX 	User Experience

Resumo

O presente relatório de PAP descreve o desenvolvimento do TeamCore, uma aplicação de gestão de RH concebida para simplificar os processos desse setor. O projeto foi motivado pela necessidade de uma ferramenta de RH que fosse simples e fluida de usar, contrastando com a complexidade de muitos sistemas existentes.
O TeamCore foi desenvolvido com o objetivo de alcançar uma gestão abrangente de dados de funcionários, cargos e contratos, promover a automação de processos para minimizar a margem de erro, e fornecer suporte à decisão através de relatórios e gráficos estratégicos. A qualidade técnica, focada na usabilidade, segurança e performance robusta, foi um pilar central do desenvolvimento.
A metodologia de trabalho incluiu o levantamento detalhado de requisitos, a modelação da base de dados relacional, e o desenvolvimento técnico utilizando a framework Laravel com a extensão Filament para o backend e frontend. O processo foi complementado por entrevistas com profissionais da área para validação das funcionalidades essenciais (MVP) e testes rigorosos de funcionalidade.
Nota sobre o Estado do Projeto: No momento da redação deste relatório, o projeto TeamCore encontra-se em fase de desenvolvimento e testes finais, com conclusão prevista para 31 de Março. Os resultados preliminares e o progresso alcançado até o momento indicam que a aplicação finalizada terá o potencial de aumentar significativamente a eficiência operacional e fornecer dados cruciais para a tomada de decisões estratégicas no ambiente empresarial.

Introdução

 Enquadramento Teórico
A gestão eficaz de Recursos Humanos é uma das funções críticas em qualquer organização moderna. Com a transformação digital, as aplicações de software para sistemas de informação de RH tornaram-se ferramentas essenciais para otimizar processos, reduzir erros administrativos e facilitar a tomada de decisões estratégicas.
O presente projeto insere-se na área de Desenvolvimento de Sistemas de Informação, especificamente na concepção e implementação de aplicações web para gestão empresarial. Este domínio abrange:
Desenvolvimento Backend: Implementação de lógica de negócio, processamento de dados e integração de bases de dados relacionais. 
Desenvolvimento Frontend: Criação de interfaces intuitivas e responsivas que facilitam a interação do utilizador.
Arquitetura de Software: Desenho de sistemas escaláveis, seguros e fácil manutenção.
Controlo de Acesso: Implementação de mecanismos de autenticação e autorização para proteger dados sensíveis.

Fundamentação do Tema
Este projeto foi selecionado por várias razões estratégicas:

Relevância Profissional: O desenvolvimento de sistemas de RH representa uma aplicação prática e imediata dos conhecimentos adquiridos no Curso Profissional de Informática de Sistemas, incluindo programação, bases de dados, segurança e design de interface.
Problema Real: Muitas organizações, especialmente PMEs, enfrentam dificuldades em gerenciar dados de funcionários de forma centralizada e eficiente. Legacy systems frequentemente apresentam problemas de usabilidade, integração limitada e custos elevados de manutenção. Este projeto demonstra como uma solução moderna pode resolver estes problemas.
Oportunidade de Aprendizagem: O escopo do projeto permite aplicar múltiplas tecnologias atuais no mercado (Laravel, Filament, MySQL, Pest) e consolidar conhecimentos em autenticação, autorização, validação de dados e testes automatizados.

Viabilidade: O projeto é de amplitude apropriada para ser completado num ciclo de desenvolvimento estruturado, permitindo implementar uma solução funcional com qualidade técnica.

Motivação e Contexto
O TeamCore nasce da necessidade de uma ferramenta de Gestão de Recursos Humanos intuitiva e eficiente, capaz de apoiar tanto equipas de gestão como colaboradores nas operações diárias. A solução foi concebida para centralizar a gestão de funcionários, cargos e contratos, automatizar tarefas de registo de tempo e horas extras, e fornecer suporte à decisão através de relatórios e visualizações estruturadas.
 
Objetivos Principais
Este projeto PAP visa desenvolver um sistema de gestão que:
Centralizar dados de RH: Consolidar informação de funcionários, departamentos, designações, contratos e períodos de férias numa base de dados estruturada e facilmente consultável.
Automatiza registos de trabalho: Permitir o registo eficiente de horas trabalhadas, com suporte a pausas/almoços, cálculo automático de horas extras e manutenção atualizada do banco de horas individual.
Implementa controlo de acesso granular: Segregar permissões por função de utilizador (ADMIN, HR, EMPLOYEE), garantindo que cada utilizador acede apenas aos dados e funcionalidades apropriados.
Oferece experiência de utilizador otimizada: Proporcionar uma interface intuitiva, com notificações contextuais e confirmações para prevenir ações não intencionais.
Garantir a qualidade técnica: Assegurar a aplicação através de testes automatizados, validação de regras de autorização e boas práticas de segurança.

 Abordagem e Metodologia
O desenvolvimento foi realizado em ciclos iterativos, adotando:
Tecnologias modernas: Laravel 12 como framework backend, Filament 3 como administrador de painéis , PHP 8.2+ e MySQL como base de dados relacional.
Testes automatizados: Desenvolvimento orientado a testes utilizando framework Pest/PHPUnit para validação contínua de funcionalidades, regras de negócio e permissões.
Validação com profissionais: Consulta com profissionais de RH durante o desenvolvimento para validar requisitos e funcionalidades essenciais.
Boas práticas de engenharia: Isolamento de dados ao nível do modelo através de políticas Eloquent, arquitetura em camadas clara, padrões de código consistentes.



Desenvolvimento do projeto

Metodologia e ferramentas
O projeto TeamCore foi desenvolvido com a framework Laravel 12, utilizando Filament 3 como administrador de painel administrativo e PHP 8.2+ como linguagem de desenvolvimento. A persistência de dados foi implementada em MySQL, com uma arquitetura relacional suportando entidades como Funcionários, Contratos, Departamentos, Designações, Bancos de Horas, Registos de Presença e Pedidos de Licença.
O processo de desenvolvimento adotou uma metodologia iterativa com ciclos bissemanais de análise, implementação, testes e validação. Foi feita a utilização Git para controlo de versão e manutenção de uma suíte de testes automatizados que executam em cada novo commit, garantindo que regressões não ocorressem durante o desenvolvimento.


Tecnologia
Aplicação
Versão
Laravel
Framework Backend
12.0
Filament
Construção dos painéis 
3.0+
PHP
Linguagem
8.2+
MySQL
Base de Dados
8.0
Pest
Framework para teste
3.8
Vite
Bundler Frontend
6.2.4
DomPDF
Geração de PDFs
3.1




Arquitetura de Dados Relacional

![Figura 1 - Diagrama de Entidades e Relações](./images/figura1_er_diagram.png)
*Figura 1: Diagrama de Entidades e Relações (ER) - Estrutura da base de dados relacional*

O projeto suporta as seguintes entidades principais:
Funcionários (Employee): Dados pessoais, contactos e informações profissionais.
Utilizadores (User): Credenciais de acesso, papéis e permissões.
Contratos (Contract): Informações de vínculo laboral, tipo e remuneração.
Departamentos (Department): Estrutura organizacional da empresa.
Cargos (Designation): Definição de funções profissionais e salários base.
Benefícios (Benefit): Subsídios e benefícios associados a funcionários.
Banco de Horas (Hourbank): Controlo de horas acumuladas/deficitárias por funcionário.
Registos de Presença (Attendance): Registos diários de entrada/saída e pausas.
Registos de Trabalho (Worklog): Logs detalhados de atividades executadas.
Licenças e Faltas (Timeoff): Gestão de períodos de férias, licenças e justificações.
Categorias de Licença (TimeoffCategory): Tipos de ausências permitidas.
Tipos de Contrato (ContractType): Definições de tipos contratuais.
Localização: País, Estado e Cidade para preenchimento de dados de funcionários.

 Processo de Desenvolvimento
O processo de desenvolvimento adotou uma metodologia iterativa com:
 Ciclos bissemanais de análise, implementação, testes e validação.
Utilização de Git para controlo de versão.
Suite de testes automatizados executando a cada novo commit.
Garantia de que regressões não ocorrem durante o desenvolvimento.


Principais implementações
O projeto implementa uma arquitetura multi-painel com isolamento de dados por função:

![Figura 2 - Dashboard Admin](./images/figura2_dashboard_admin.png)
*Figura 2: Dashboard Admin com Widgets de Estatísticas (contratos, funcionários, presença)*

![Figura 3 - Painel HR](./images/figura3_painel_hr.png)
*Figura 3: Painel HR - Gestão de Funcionários com tabela de dados*

![Figura 4 - Painel Funcionário](./images/figura4_painel_employee.png)
*Figura 4: Painel Funcionário - Visualização de Dados Pessoais e Banco de Horas*

Painel Admin (/admin): Gestão completa do sistema
  - Gestão de utilizadores e papéis.
  - Gestão de departamentos e cargos.
  - Administração de benefícios.
  - Controlo geral da aplicação.

Painel RH (/hr): Gestão de Recursos Humanos
  - Gestão de funcionários.
  - Gestão de contratos.
  - Processamento de licenças e faltas.
  - Relatórios estratégicos e análise.

Painel Funcionário (/employee): Acesso pessoal
  - Visualização de dados pessoais.
  - Registos de trabalho próprio.
  - Solicitações de licença/ausência.
  - Visualização de banco de horas.

Características de cada painel:
Páginas dedicadas com um middleware específico para o controlo de acesso.
Isolamento de dados ao nível do modelo através de políticas Eloquent.
Componentes UI e funcionalidades condicionadas ao perfil do utilizador.
Navegação e estrutura otimizadas para cada função.

Controlo de Acesso e Segurança

![Figura 5 - Diagrama RBAC](./images/figura5_rbac_diagram.png)
*Figura 5: Diagrama RBAC - Hierarquia de Papéis (Admin, HR, Employee) e Permissões por Funcionalidade*

RBAC (Role-Based Access Control):
- Implementação de três perfis base: ADMIN, HR, EMPLOYEE.
- Políticas Eloquent para validação de autorização ao nível do modelo.
- Operações CRUD protegidas.
Isolamento de Dados:
- Cada utilizador acede apenas aos dados apropriados à sua função.
- Middleware customizado para proteção de rotas por canal/painel.
- Queries filtered ao nível do modelo através de scopes e políticas.

Segurança Implementada:
- Proteção CSRF em formulários.
- Validação de permissões em cada ação.
- Encriptação de dados sensíveis.
- Logs de auditoria de operações sensíveis.


Gestão de Horas e Banco de Horas

![Figura 6 - Registo Attendance](./images/figura6_attendance_form.png)
*Figura 6: Registo de Presença (Attendance) com Campos de Entrada/Saída e Pausas*

![Figura 7 - Banco de Horas](./images/figura7_hourbank_view.png)
*Figura 7: Visualização do Banco de Horas (Hourbank) com Saldo Acumulado e Histórico*

Registos de Trabalho:
- Entrada/saída com timestamp automático.
- Suporte a múltiplos registos por dia.
- Cálculo automático de durações.

Pausas e Intervalos:
- Campos 'break_start' / 'break_end'.
- Exclusão automática de tempo de pausa nos cálculos.
- Validação de lógica de intervalos.

Cálculo de Horas:
- Arredondamento automático para horas e meia-horas.
- Integração com banco de horas do funcionário.
- Suporte a horas extras com multiplicadores diferenciados.

Banco de Horas (Hourbank):
- Atualização automática após cada registo validado.
- Rastreamento de horas acumuladas/deficitárias.
- Criação automática ao criar novo funcionário.
- Relatórios e extratos de saldo.






 Experiência do Utilizador (UX)

![Figura 8 - Notificações Toast](./images/figura8_notifications_toast.png)
*Figura 8: Notificações Toast ao Criar Funcionário - 4 Mensagens de Sucesso (Utilizador, Contrato, Banco de Horas)*

![Figura 9 - Diálogos de Confirmação](./images/figura9_confirmation_dialogs.png)
*Figura 9: Diálogo de Confirmação de Ações Destrutivas com Avisos Visuais*

![Figura 10 - Badges Visuais](./images/figura10_badges_states.png)
*Figura 10: Badges Visuais - Indicadores de Funções (Admin/HR/Employee) e Estados de Contrato (Ativo/Encerrado/Suspenso)*

![Figura 11 - Responsividade](./images/figura11_responsive_design.png)
*Figura 11: Interface Responsiva - Comparação Desktop vs Tablet vs Mobile*

Tradução PT-PT:
- Rótulos e campos do formulário em português.
- Mensagens de validação localizadas.
- Colunas de tabelas traduzidas.
- Datas e números formatados para PT-PT.

Notificações Contextuais:
- Trait 'NotifiesCreatedItems' para notificações por recurso.
- Mensagens específicas por ação realizada.

Confirmações de Ação:
- 'ConfirmsCancelAction' para evitar cancelamentos não intencionais.
- Diálogos de confirmação em ações destrutivas.
- Avisos de mudanças não salvas.

Badges Visuais:
- Indicadores de função (role) na tabela de utilizadores.
- Estados visuais para contratos (ativo, encerrado, suspenso).
- Indicadores de status de licenças.

Interface Responsiva:
- Suporte completo a dispositivos móveis.
- Layout adaptativo para tablets e desktop.
- Navegação otimizada para múltiplos tamanhos de ecrã.
 Melhorias e Otimizações

Padronização de Senhas:
- Definição de senha padrão para novos utilizadores.
- Obrigatoriedade de alteração no primeiro acesso.
- Reforço de políticas de segurança.

Isolamento de Dados de Funcionário:
- Proteção de privacidade e conformidade LGPD.
- Dados pessoais acessíveis apenas aos autorizados.
- Logs de acesso a dados sensíveis.

Actions Condicionadas:
- Botões e ações visíveis apenas para perfis autorizados.
- Desativação contextual de funcionalidades.
- Guia visual das permissões do utilizador.

Boas Práticas de Engenharia:
- Código limpo e bem estruturado.
- Padrões consistentes em toda a codebase.
- Naming conventions claras e significativas.
- Separação de responsabilidades clara.
Validação de E-mail e Automação de Criação

![Figura 12 - Formulário EmployeeResource](./images/figura12_employee_form.png)
*Figura 12: Formulário de Criação de Funcionário (EmployeeResource) com Validação de E-mail*

![Figura 13 - Fluxo de Automação](./images/figura13_employee_creation_flow.png)
*Figura 13: Fluxo Visual de Criação Automática - Employee → User → Contract → Hourbank*

Validação Rigorosa de E-mail:
Custom Rule `ValidEmailDomain` que rejeita e-mails sem extensão de domínio válida (ex: rejeita `teste@teste`, aceita `usuario@empresa.com`).
Regex validação: `/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/` garante TLD mínimo de 2 caracteres.
Aplicado em Employee e User Resources em formulários de criação/edição.
Proteção contra e-mails inválidos em toda a aplicação.

Sistema Automático de Gatilhos (Observer Pattern):
`EmployeeObserver` que intercepta criação de Employee e automaticamente:
Cria User com email/nome do Employee (role: `employee`, senha default do .env, must_change_password: true).
Cria Contract (tipo indefinido, salário da designação, status ativo).
Cria Hourbank*(saldo inicial 0 horas, accrual_date = data contratação).
Armazena em cache dados para notificações Filament.
Tratamento de erros com logging automático.

Notificações Customizadas (Filament Toast):
4 notificações ao criar Employee:
  1. Utilizador criado (email, role, status força troca).
  2. Contrato criado (tipo, salário, data início).
  3. Banco de horas criado (saldo inicial, accrual_date).
  4. Consolidada final (checkmarks de sucesso).
Cada notificação com ícone e cor personalizada para feedback visual claro

Proteção contra Metadata:
`email_verified_at`  em UserResource sempre é auto-preenchido com a data atual.
`created_at` e `updated_at` nunca editáveis em formulários.
Campos metadata visíveis apenas em tabelas como `.toggleable(isToggledHiddenByDefault: true)`.
ActivityLogResource mostra `created_at` desabilitado (read-only).




Recursos Filament Implementados

![Figura 14 - Resource EmployeeResource](./images/figura14_employee_resource.png)
*Figura 14: Resource EmployeeResource - Lista com Tabela, Filtros e Ações*

![Figura 15 - Resource ContractResource](./images/figura15_contract_resource.png)
*Figura 15: Resource ContractResource com Ação de Download PDF de Contrato*

![Figura 16 - Resource ActivityLogResource](./images/figura16_activitylog_resource.png)
*Figura 16: Resource ActivityLogResource - Visualização de Histórico de Auditoria com Filtros*

O projeto implementa 15 Resources Filament para gestão:


Resource
Modelo
EmployeeResource
Employee
ContractResource
Contract
DepartmentResource
Department
DesignationResource
Designation
UserResource
User
BenefitResource
Benefit
AttendanceResource
Attendance
WorklogResource
Worklog
TimeoffResource
Timeoff
TimeoffCategoryResource
Timeoffcategory
HourbankResource
Hourbank
ContractTypeResource
ContractType
ActivityLogResource
Activity
StateResource
State
CityResource
City
CountryResource
Country



Cada Resource inclui:
- Formulários com validações apropriadas.
- Tabelas com ordenação e pesquisa.
- Ações customizadas por função.
- Isolamento de dados por RBAC.



“Figura”
Figura 1–figura a


“Gráfico”
Gráfico 1 – gráfico a


“Tabela”
Tabela 1 – tabela a


Conclusão






Bibliografia






Anexos





