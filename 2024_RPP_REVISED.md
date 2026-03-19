


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

Acrónimos / Abreviaturas / Siglas	6
Resumo	7
Introdução	8
Enquadramento Teórico	8
Fundamentação do Tema	8
Motivação e Contexto	8
Objetivos Principais	9
Abordagem e Metodologia	9
Desenvolvimento do Projeto	10
Metodologia e Ferramentas	10
Arquitetura de Dados Relacional	11
Processo de Desenvolvimento	12
Principais Implementações	13
Painel Admin (/admin) – Gestão completa do sistema	15
Painel RH (/hr) – Gestão de Recursos Humanos	15
Painel Funcionário (/employee) – Acesso pessoal	16
Características de cada painel	16
Controlo de Acesso e Segurança	16
Gestão de Horas e Banco de Horas	17
Separação de Férias e Licenças	18
Experiência do Utilizador (UX)	20
Melhorias e Otimizações	23
Validação de E-mail e Automação de Criação	23
Recursos Filament Implementados	26
Conclusão	30
Bibliografia	32
Anexos	33



Índice de figuras

Figura 1: Diagrama de Entidades e Relações (ER) - Estrutura da base de dados relacional	12
Figura 2: Dashboard Admin com Widgets de Estatísticas (contratos, funcionários, presença)	13
Figura 3: Painel HR - Gestão de Funcionários com tabela de dados	14
Figura 4: Painel Funcionário - Dashboard com múltiplos widgets de férias, licenças, banco de horas e presença	15
Figura 5: Diagrama RBAC - Hierarquia de Papeis (Admin, HR, Employee) e Permissões por Funcionalidade	16
Figura 6: Registo de Presença (Attendance) com Campos de Entrada/Saída e Pausas	17
Figura 7: Visualização do Banco de Horas (Hourbank) com Saldo Acumulado e Histórico	18
Figura 8: Notificações Toast ao Criar Funcionário - 4 Mensagens de Sucesso (Utilizador, Contrato, Banco de Horas)	19
Figura 9: Diálogo de Confirmação de Ações Destrutivas com Avisos Visuais	20
Figura 10: Badges Visuais - Indicadores de Funções (Admin/HR/Employee) e Estados de Contrato (Ativo/Encerrado/Suspenso)	20
Figura 11: Formulário de Criação de Funcionário (EmployeeResource) com Validação de E-mail	22
Figura 12: Resource EmployeeResource - Lista com Tabela, Filtros e Ações	24
Figura 13: Resource ContractResource com Ação de Download PDF de Contrato	25
Figura 14: Resource ActivityLogResource - Visualização de Histórico de Auditoria com Filtros	26
Figura 15: Resource VacationResource - Gestão de Férias com Workflows de Aprovação	27



Índice de tabelas

Tabela 1 – tabela a	2



Acrónimos / Abreviaturas / Siglas
Sigla
Descrição
CRUD
Create, Read, Update, Delete
CSRF
Cross-Site Request Forgery
HR
Human Resources
MVP
Minimal Viable Product
ORM
Object-Relational Mapping
PAP
Prova de Aptidão Profissional
PDF
Portable Document Format
PME
Pequena e Média Empresa
PT-PT
Português de Portugal
RBAC
Role-Based Access Control
RH
Recursos Humanos
TLD
Top-Level Domain
UI
User Interface
UX
User Experience



Resumo
O presente relatório de PAP descreve o desenvolvimento do TeamCore, uma aplicação de gestão de RH concebida para simplificar os processos desse setor. O projeto foi motivado pela necessidade de uma ferramenta de RH que fosse simples e fluida de usar, contrastando com a complexidade de muitos sistemas existentes.

O TeamCore foi desenvolvido com o objetivo de alcançar uma gestão abrangente de dados de funcionários, cargos e contratos, promover a automação de processos para minimizar a margem de erro, e fornecer suporte à decisão através de relatórios e gráficos estratégicos. A qualidade técnica, focada na usabilidade, segurança e performance robusta, foi um pilar central do desenvolvimento.

A metodologia de trabalho incluiu o levantamento detalhado de requisitos, a modelação da base de dados relacional, e o desenvolvimento técnico utilizando a framework Laravel com a extensão Filament para o backend e frontend. O processo foi complementado por entrevistas com profissionais da área para validação das funcionalidades essenciais (MVP) e testes rigorosos de funcionalidade.

Nota sobre o Estado do Projeto: No momento da redação deste relatório (19 de Março de 2026), o projeto TeamCore encontra-se em fase de desenvolvimento avançado com múltiplas funcionalidades implementadas e testadas. A aplicação é totalmente funcional para gestão de RH, com separação clara entre férias e licenças, dashboard interativo para funcionários, widgets otimizados e sistema de aprovações workflows. Os resultados alcançados até ao momento indicam que a aplicação está pronta para utilização num ambiente de produção reduzido, com potencial de aumentar significativamente a eficiência operacional e fornecer dados cruciais para a tomada de decisões estratégicas no ambiente empresarial.


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
Problema Real: Muitas organizações, especialmente PMEs, enfrentam dificuldades em gerenciar dados de funcionários de forma centralizada e eficiente. Legacy systems frequentemente apresentam problemas de usabilidade, integração limitada e custos elevados de manutenção.
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
Tecnologias modernas: Laravel 12 como framework backend, Filament 3 como administrador de painéis, PHP 8.2+ e MySQL como base de dados relacional.
Testes automatizados: Desenvolvimento orientado a testes utilizando framework Pest/PHPUnit para validação contínua de funcionalidades, regras de negócio e permissões.
Validação com profissionais: Consulta com profissionais de RH durante o desenvolvimento para validar requisitos e funcionalidades essenciais.
Boas práticas de engenharia: Isolamento de dados ao nível do modelo através de políticas Eloquent, arquitetura em camadas clara, padrões de código consistentes.


Desenvolvimento do Projeto
Metodologia e Ferramentas
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

Figura 1: Diagrama de Entidades e Relações (ER) - Estrutura da base de dados relacional

O projeto suporta as seguintes entidades principais:
Funcionários (Employee): Dados pessoais, contactos e informações profissionais.
Utilizadores (User): Credenciais de acesso, papeis e permissões.
Contratos (Contract): Informações de vínculo laboral, tipo e remuneração.
Departamentos (Department): Estrutura organizacional da empresa.
Cargos (Designation): Definição de funções profissionais e salários base.
Benefícios (Benefit): Subsídios e benefícios associados a funcionários.
Banco de Horas (Hourbank): Controlo de horas acumuladas/deficitárias por funcionário.
Registos de Presença (Attendance): Registos diários de entrada/saída e pausas.
Registos de Trabalho (Worklog): Logs detalhados de atividades executadas.
Férias (Vacation): Gestão de férias anuais com saldo renovável e workflows de aprovação.
Licenças e Faltas (Timeoff): Gestão de períodos de licenças específicas (parental, saúde, família) e justificações.
Categorias de Licença (TimeoffCategory): Tipos de ausências permitidas.
Tipos de Contrato (ContractType): Definições de tipos contratuais.
Localização: País, Estado e Cidade para preenchimento de dados de funcionários.

Processo de Desenvolvimento
O processo de desenvolvimento adotou uma metodologia iterativa com:
Ciclos bissemanais de análise, implementação, testes e validação.
Utilização de Git para controlo de versão.
Suite de testes automatizados executando a cada novo commit.
Garantia de que regressões não ocorrem durante o desenvolvimento.


Principais Implementações
O projeto implementa uma arquitetura multi-painel com isolamento de dados por função:

Figura 2: Dashboard Admin com Widgets de Estatísticas (contratos, funcionários, presença).


Figura 3: Painel HR - Gestão de Funcionários com tabela de dados.

Figura 4: Painel Funcionário - Visualização de Dados Pessoais e Banco de Horas.

Painel Admin (/admin) – Gestão completa do sistema
Gestão de utilizadores e papéis.
Gestão de departamentos e cargos.
Administração de benefícios.
Controlo geral da aplicação.

Painel RH (/hr) – Gestão de Recursos Humanos
Gestão de funcionários.
Gestão de contratos.
Processamento de licenças e faltas.
Relatórios estratégicos e análise.

Painel Funcionário (/employee) – Acesso pessoal
Visualização de dados pessoais e informações profissionais.
Visualização e gestão de férias (criação de pedidos, acompanhamento de aprovações).
Visualização e gestão de licenças/ausências.
Solicitações de licença/ausência com workflows de aprovação.
Visualização de banco de horas individual com histórico.
Dashboard interativo com widgets resumidos de férias, licenças, presença e banco de horas.

Características de cada painel
Páginas dedicadas com um middleware específico para o controlo de acesso.
Isolamento de dados ao nível do modelo através de políticas Eloquent.
Componentes UI e funcionalidades condicionadas ao perfil do utilizador.
Navegação e estrutura otimizadas para cada função.

Controlo de Acesso e Segurança


Figura 5: Diagrama RBAC - Hierarquia de Papeis (Admin, HR, Employee) e Permissões por Funcionalidade.

RBAC (Role-Based Access Control):
Implementação de três perfis base: ADMIN, HR, EMPLOYEE.
Políticas Eloquent para validação de autorização ao nível do modelo.
Operações CRUD protegidas.

Isolamento de Dados:
Cada utilizador acede apenas aos dados apropriados à sua função.
Middleware customizado para proteção de rotas por canal/painel.
Queries filtered ao nível do modelo através de scopes e políticas.

Segurança Implementada:
Proteção CSRF em formulários.
Validação de permissões em cada ação.
Encriptação de dados sensíveis.
Logs de auditoria de operações sensíveis.

Gestão de Horas e Banco de Horas

Figura 6: Registo de Presença (Attendance) com Campos de Entrada/Saída e Pausas

Figura 7: Visualização do Banco de Horas (Hourbank) com Saldo Acumulado e Histórico

Registos de Trabalho:
Entrada/saída com timestamp automático.
Suporte a múltiplos registos por dia.
Cálculo automático de durações.

Pausas e Intervalos:
Campos 'break_start' / 'break_end'.
Exclusão automática de tempo de pausa nos cálculos.
Validação de lógica de intervalos.

Cálculo de Horas:
Arredondamento automático para horas e meia-horas.
Integração com banco de horas do funcionário.
Suporte a horas extras com multiplicadores diferenciados.

Banco de Horas (Hourbank):
Atualização automática após cada registo validado.
Rastreamento de horas acumuladas/deficitárias.
Criação automática ao criar novo funcionário.
Relatórios e extratos de saldo.


Experiência do Utilizador (UX)

**Figura 8: Notificações Toast ao Criar Funcionário - 4 Mensagens de Sucesso (Utilizador, Contrato, Banco de Horas)**

Quando um utilizador de RH cria um novo funcionário, o sistema executa automaticamente um fluxo encadeado que cria múltiplas entidades (Utilizador, Contrato e Banco de Horas). Para dar feedback claro de cada operação realizada, implementamos notificações individuais em toast (pequenas mensagens no canto do ecrã) com ícones e cores distintos. Esta abordagem permite que o utilizador acompanhe exatamente o que foi criado, reduzindo a incerteza e aumentando a confiança no sistema. Sem isto, o utilizador veria apenas uma mensagem genérica e não saberia se todas as operações completaram com sucesso.

**Figura 9: Diálogo de Confirmação de Ações Destrutivas com Avisos Visuais**

Operações críticas como eliminar um funcionário ou encerrar um contrato são irreversíveis e podem ter impacto significativo nos dados. O sistema implementa diálogos de confirmação antes de qualquer ação destrutiva, apresentando claramente o que será eliminado e pedindo confirmação explícita. Os avisos visuais (cores alertas, ícones) tornam óbvio que se trata de uma ação importante. Isto protege contra erros acidentais, que são particularmente críticos num contexto de RH onde dados de funcionários não podem ser perdidos acidentalmente.

**Figura 10: Badges Visuais - Indicadores de Funções (Admin/HR/Employee) e Estados de Contrato (Ativo/Encerrado/Suspenso)**

As badges (pequenos rótulos coloridos) permitem identificar rapidamente o papel de cada utilizador e o estado de cada contrato, sem necessidade de ler texto em tabelas longas. Por exemplo, um utilizador com role "Admin" é imediatamente reconhecível por uma badge azul, enquanto um contrato "Suspenso" aparece com uma cor de alerta. Isto melhora significativamente a velocidade de compreensão e reduz erros, especialmente importante quando se navega tabelas com dezenas de registos.

Tradução PT-PT:
Rótulos e campos do formulário em português.
Mensagens de validação localizadas.
Colunas de tabelas traduzidas.
Datas e números formatados para PT-PT.

Notificações Contextuais:
Trait 'NotifiesCreatedItems' para notificações por recurso.
Mensagens específicas por ação realizada.

Confirmações de Ação:
'ConfirmsCancelAction' para evitar cancelamentos não intencionais.
Diálogos de confirmação em ações destrutivas.
Avisos de mudanças não salvas.

Badges Visuais:
Indicadores de função (role) na tabela de utilizadores.
Estados visuais para contratos (ativo, encerrado, suspenso).
Indicadores de status de licenças.

Interface Responsiva:
Suporte completo a dispositivos móveis.
Layout adaptativo para tablets e desktop.
Navegação otimizada para múltiplos tamanhos de ecrã.

Melhorias e Otimizações
Padronização de Senhas:
Definição de senha padrão para novos utilizadores.
Obrigatoriedade de alteração no primeiro acesso.
Reforço de políticas de segurança.

Isolamento de Dados de Funcionário:
Proteção de privacidade e conformidade LGPD.
Dados pessoais acessíveis apenas aos autorizados.
Logs de acesso a dados sensíveis.

Actions Condicionadas:
Botões e ações visíveis apenas para perfis autorizados.
Desativação contextual de funcionalidades.
Guia visual das permissões do utilizador.

Boas Práticas de Engenharia:
Código limpo e bem estruturado.
Padrões consistentes em toda a codebase.
Naming conventions claras e significativas.
Separação de responsabilidades clara.

Validação de E-mail e Automação de Criação

**Figura 11: Formulário de Criação de Funcionário (EmployeeResource) com Validação de E-mail**

O formulário de criação de funcionário inclui um campo de e-mail que valida em tempo real se o domínio é válido (exemplo: rejeita `usuario@empresa` mas aceita `usuario@empresa.com`). A validação visual (com mensagens de erro claras) é apresentada imediatamente, evitando que o utilizador submeta o formulário com dados inválidos. Isto é particularmente importante porque e-mails inválidos comprometeriam toda a comunicação automática do sistema e a criação do utilizador associado. A implementação de uma regra customizada de validação garante que a qualidade dos dados é mantida desde o ponto de entrada.

Validação Rigorosa de E-mail:
Custom Rule ValidEmailDomain que rejeita e-mails sem extensão de domínio válida (ex: rejeita teste@teste, aceita usuario@empresa.com).
Regex: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/ garante TLD mínimo de 2 caracteres.
Aplicado em Employee e User Resources em formulários de criação/edição.
Proteção contra e-mails inválidos em toda a aplicação.

Sistema Automático de Gatilhos (Observer Pattern):
EmployeeObserver intercepta criação de Employee e automaticamente cria User, Contract e Hourbank.
Cria User com email/nome do Employee (role: employee, senha default do .env, must_change_password: true).
Cria Contract (tipo indefinido, salário da designação, status ativo).
Cria Hourbank (saldo inicial 0 horas, accrual_date = data contratação).
Armazena em cache dados para notificações Filament com tratamento de erros e logging automático.

Notificações Customizadas (Filament Toast):
4 notificações ao criar Employee:
Utilizador criado (email, role, status força troca).
Contrato criado (tipo, salário, data início).
Banco de horas criado (saldo inicial, accrual_date).
Consolidada final (checkmarks de sucesso).
Cada notificação com ícone e cor personalizada para feedback visual claro.

Proteção contra Metadata:
email_verified_at em UserResource sempre é auto-preenchido com a data atual.
created_at e updated_at nunca editáveis em formulários.
Campos metadata visíveis apenas em tabelas como toggleable (isToggledHiddenByDefault: true).
ActivityLogResource mostra created_at desabilitado (read-only).


Recursos Filament Implementados

**Figura 12: Resource EmployeeResource - Lista com Tabela, Filtros e Ações**

O Resource EmployeeResource apresenta todos os funcionários numa tabela interativa com capacidades avançadas: filtros permitem procurar por departamento, designação ou status de contrato; a pesquisa permite localizar rapidamente um funcionário pelo nome ou e-mail; as ações em cada linha (editar, ver detalhes, eliminar) estão sempre acessíveis. A isolamento de dados garante que utilizadores de RH só veem funcionários do seu departamento, enquanto Admin vê toda a organização. Esta interface reduz significativamente o tempo necessário para encontrar e atualizar informações de funcionários, comparado com sistemas que exigem navegação através de menus complexos.

**Figura 13: Resource ContractResource com Ação de Download PDF de Contrato**

Cada contrato pode ser visualizado em lista e, numa ação especializada, pode ser descarregado como PDF com toda a informação formatada profissionalmente. Esta funcionalidade é crítica para fins legais e administrativos — permite que o RH mantenha cópias arquivadas dos contratos, e aos funcionários ter acesso aos seus documentos sem necessidade de contactar o departamento. A implementação de PDF automático elimina trabalho manual repetitivo e garante consistência na documentação.

**Figura 14: Resource ActivityLogResource - Visualização de Histórico de Auditoria com Filtros**

O ActivityLogResource registra todas as alterações importantes no sistema (criação de utilizadores, edição de salários, eliminação de registos). Esta informação é crítica para conformidade regulatória e para rastrear quem fez o quê e quando. Os filtros permitem procurar por tipo de ação, utilizador que realizou a ação, ou data, facilitando investigações e auditorias. Por exemplo, se surgir uma discrepância num banco de horas, é possível rapidamente ver todo o histórico de alterações nesse registo. Isto é particularmente importante em contexto empresarial onde são necessárias evidências documentadas de todas as operações para fins legais.

O projeto implementa 16 Resources Filament para gestão:

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
VacationResource
Vacation
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
Formulários com validação apropriada.
Tabelas com ordenação e pesquisa.
Ações customizadas por função.
Isolamento de dados por RBAC.

## Separação de Férias e Licenças

Uma das principais melhorias implementadas no projeto foi a separação clara entre **Férias (Vacations)** e **Licenças (Timeoffs)** ao nível arquitectónico.

### Vacuum (Férias)
O modelo Vacation foi criado especificamente para gestão de férias remuneradas anuais. As suas características incluem:

- **Saldo Anual Renovável**: 22 dias úteis por funcionário, renovados automaticamente a 1º de janeiro.
- **Cálculo Proporcional**: Para funcionários contratados durante o ano, o saldo é calculado proporcionalmente aos meses restantes (dias × meses_restantes / 12).
- **Sem Acumulação**: Dias não usados no ano expira no final do ano civil (sem carry-over).
- **Saldo Negativo Controlado**: Apenas utilizadores HR ou Admin podem criar férias com saldo negativo; funcionários recebem ValidationException.
- **Renovação Automática**: Console command RenewVacationBalances executa yearly no 1º de janeiro.
- **Histórico de Saldo**: Campo balance_at_creation registra o saldo disponível no momento da criação, permitindo auditoria futura.

### Timeoff (Licenças)
O modelo Timeoff mantém a funcionalidade para licenças específicas:

- **Tipos Detalhados**: Parental (inicial, mãe, pai, alargada), Adopção, Saúde (doença, acidente laboral, profissional), Família (assistência a filho/neto/agregado), Formação, Obrigações legais.
- **Sem Restrições de Saldo**: Ao contrário de férias, licenças não consomem um saldo centralizado.
- **Categorização**: Cada licença deve ser categorizada através do campo category_id para rastreamento e reporting.

### Workflows de Aprovação
Ambos (Vacation e Timeoff) suportam workflows de aprovação com three states:
- **Pending**: Aguardando revisão de RH/Admin.
- **Approved**: Aprovado e efectivo.
- **Rejected**: Rejeitado com motivo.

O sistema implementa políticas de autorização que garantem:
- HR/Admin podem visualizar, criar, editar e aprovar (excepto os seus próprios pedidos).
- Employees podem apenas visualizar e criar os seus próprios pedidos.
- O auto-aprovação é bloqueada em ambos os recursos.

### Dashboard do Funcionário - Widgets Otimizados
O painel de funcionário foi refatorizado para exibir cinco widgets compilados em formato StatsOverviewWidget:

1. **EmployeeInfoWidget**: Mostra informações pessoais (nome completo, cargo, departamento, data de admissão) com cálculo automático e formatação PT-PT.

2. **MyVacationBalanceWidget**: Exibe o saldo de férias do ano actual, dias aprovados, dias pendentes e data da última renovação, com cores dinamicamente ajustadas ao saldo.

3. **MyTimeoffHistoryWidget**: Estatísticas de licenças com contagem de pedidos pendentes, aprovados e rejeitados.

4. **MyAttendanceWidget**: Taxa de presença mensal, número de faltas e atrasos.

5. **HourBankDetailWidget**: Saldo actual, total acumulado e última atualização do banco de horas.

Todos os widgets foram **refatorizados para eliminar dependências de Blade views**, utilizando apenas PHP puro com o padrão StatsOverviewWidget, alinhados com a arquitetura dos painéis Admin e HR.

### Otimizações de Performance e Código
Durante o desenvolvimento final, foram implementadas várias otimizações:

- **Query Scopes**: Adição de scopes reutilizáveis (pending(), approved(), rejected()) aos modelos Timeoff e Vacation para eliminar duplicação de where('status', '...').
- **Helper Methods**: Criação de métodos no Employee model (getPendingTimeoffsCount(), getLatestHourBankBalance()) para centralizar cálculos utilizados em múltiplas vistas e widgets.
- **Eliminação de N+1 Queries**: Refactor da dashboard blade para utilizar helper methods em vez de queries inline em templates.
- **Eager Loading**: Implementação de with(['employee', 'approvedBy']) em VacationResource table queries para evitar carregamento lazy unnecessário.
- **Layout Responsivo**: Dashboard com grid adaptativo (1 coluna em dispositivos móveis, 3 colunas em desktop screens).



Conclusão

## Dificuldades Encontradas e Soluções Adotadas

Ao longo do desenvolvimento do TeamCore, enfrentei desafios importantes que foram determinantes para o sucesso do projeto.

**Isolamento de dados complexo**: Um dos maiores obstáculos foi implementar o isolamento de dados por função de utilizador de forma robusta. No sistema RBAC, não era suficiente apenas bloquear o acesso às páginas — era necessário garantir que cada utilizador só consultava os seus próprios dados ao nível da base de dados. Inicialmente, tentei resolver isto apenas com middleware, mas rapidamente apercebi-me que isso deixava vulnerabilidades. A solução foi implementar scopes de Eloquent e políticas de autorização em cada modelo, garantindo que as queries já vinham filtradas da fonte. Isto aumentou significativamente a segurança, mas também me ensinou a importância de pensar na segurança desde o desenho da arquitetura.

**Observer Pattern e efeitos secundários automáticos**: Quando um utilizador de RH criava um novo funcionário, era necessário criar automaticamente um utilizador, um contrato e um banco de horas. Implementei isto com Observers, mas enfrentei um problema: precisava notificar o utilizador de cada passo isoladamente (4 notificações diferentes). A solução envolveu armazenar dados em cache durante o ciclo de eventos e recuperá-los após a conclusão, evitando múltiplas queries e permitindo notificações granulares. Isto mostrou-me como pensar em efeitos secundários e gestão de estado em aplicações que executam muitas operações encadeadas.

**Validação de e-mail com expressões regulares**: A validação de domínios de e-mail pareceu simples inicialmente, mas descobri que muitas validações padrão aceitavam e-mails inválidos como `usuario@empresa` (sem TLD). Tive de criar uma regra customizada que rejeitasse formatos inválidos. Isto ensinou-me a importância de compreender as camadas de validação e a necessidade de diálogos claros com utilizadores finais sobre o que constitui dados válidos.

## Pontos Fortes do Projeto

O project conseguiu alcançar os seus objetivos principais com qualidade:

- **Arquitetura bem estruturada**: A separação clara entre Admin, HR e Employee painéis, com isolamento de dados ao nível do modelo, criou uma base sólida e escalável. As políticas Eloquent garantem segurança sem depender apenas de frontend.

- **Experiência do utilizador intuitiva**: As notificações contextuais (toast messages), confirmações de ações destrutivas e badges visuais tornam a aplicação fácil de usar e reduzem significativamente o risco de erros. Isto foi validado com profissionais de RH durante o desenvolvimento.

- **Automação inteligente**: O sistema automático de criação de utilizadores, contratos e bancos de horas quando um funcionário é adicionado elimina tarefas repetitivas e reduz a margem de erro humano. Isto é particularmente valioso em PMEs onde os recursos são limitados.

- **Testes automatizados como parte da metodologia**: A utilização de Pest desde o início garantiu que o código permanecesse funcional durante todo o desenvolvimento. Isto deu confiança ao fazer mudanças e aprender com erros sem danificar o sistema.

## Pontos a Melhorar

Ao refletir criticamente sobre o projeto, identifico várias áreas que poderiam estar mais robustas:

- **Gestão de exceções inadequada**: Embora o projeto funcione bem nas situações normais, não tratei todas as exceções possíveis (ex: falha ao criar contrato durante o Observer Pattern). Isto poderia deixar o sistema em estado inconsistente. Uma solução seria implementar transações de base de dados mais complexas e rollbacks apropriados.

- **Falta de testes de carga**: O projeto foi testado manualmente com um pequeno volume de dados. Não sei como o sistema se comporta com milhares de funcionários ou com múltiplos utilizadores simultâneos. Isto seria importante validar para uma verdadeira aplicação de produção.

- **Documentação de código escassa**: Apesar de ter implementado funcionalidades complexas, não deixei comentários explicativos suficientes. Um colega que recebesse este código teria dificuldade em compreender o padrão Observer ou a lógica de notificações em cache.

- **Funcionalidades essenciais em falta**: O sistema não permite ainda a gestão de férias de forma completa (cálculo automático de dias restantes vs. dias usados), nem tem um motor de processamento salarial. Isto limita o seu uso em empresas reais.

## O que Aprendi com o Projeto

Este projeto consolidou e expandiu significativamente os meus conhecimentos:

- **Pensamento em arquitetura**: Aprendi que não basta escrever código que funciona — é preciso desenhar a arquitetura pensando em segurança, escalabilidade e manutenção. As decisões tomadas no início (como as políticas Eloquent) afetaram tudo o resto do projeto.

- **Profundidade em segurança**: Compreendi que a segurança não é um módulo isolado. Implementar RBAC corretamente exige pensar em cada camada — validação no frontend, autorização em rotas, e isolamento ao nível da base de dados.

- **Metodologia iterativa está realmente correto**: Embora tivesse aprendido isto na teoria, experiências este semestre confirmaram-me que trabalhar em ciclos pequenos, com testes contínuos e validação junto dos utilizadores finais, evita corrigir erros dispendiosos mais tarde.

- **A importância do contexto empresarial**: Falei com profissionais de RH real durante o desenvolvimento. As suas preocupações — como a facilidade em alterar salários ou o modo de confirmar ausências — mudaram as minhas prioridades de desenvolvimento. Isto ensinou-me que a melhor solução técnica não é necessariamente a que resolve o problema do negócio.

- **Usar frameworks especializados acelera e melhora a qualidade**: Filament eliminou muito do trabalho repetitivo de CRUD, permitindo focar no que é realmente complexo (lógica de negócio, segurança, automação). O Laravel e o Pest também ofereceram convenções que tornaram o código mais previsível para outros.

## Importância para o Meu Futuro Profissional

Este projeto representou uma ponte importante entre o conhecimento académico e as exigências do mercado profissional.

Em primeiro lugar, demonstra que sou capaz de desenhar e implementar uma aplicação completa — desde a modelação de dados até ao frontend — com qualidade técnica. Muitos estagiários conseguem seguir especificações, mas o que distingue profissionais é conseguir identificar problemas não óbvios (como o isolamento de dados) e implementar soluções robustas.

Em segundo lugar, o projeto reforçou a minha confiança em lidar com ambiguidade. Inicialmente, o escopo era vago ("um sistema de gestão de RH"). Através de pesquisa, entrevistas com utilizadores e validação iterativa, consegui transformar isso numa arquitetura clara e numa aplicação funcional. Esta capacidade é crítica em projetos reais, onde os requisitos raramente são completos.

Em terceiro lugar, consolidei conhecimentos em ferramentas que são realmente utilizadas no mercado (Laravel, Filament, Git, testes automatizados). Não são apenas conceitos académicos — são competências que posso aplicar imediatamente em contexto profissional.

Finalmente, este projeto mostrou-me que sou mais capaz de aprender independentemente do que imaginava. Muitas funcionalidades tiveram de ser investigadas por mim (implementação de Observers, caches, políticas de autorização no Eloquent), porque não havia disciplinas específicas no curso sobre isto. A capacidade de aprender através de documentação, experimentação e reflexão é talvez a competência mais importante que desenvolvi.

## Reflexão Final

O TeamCore não é uma aplicação perfeita — tem limitações e áreas que precisam de melhoria. No entanto, é uma solução que resolve um problema real, é robusta o suficiente para ser utilizada numa empresa, e foi desenvolvida com boas práticas. Mais importante ainda, o processo de desenvolvimento ensinou-me como pensar e agir como um engenheiro de software e não apenas como um programador. 

Para as próximas oportunidades profissionais, levarei consigo a compreensão de que qualidade técnica, segurança e experiência do utilizador não são extras — são componentes essenciais de qualquer solução. E que a reflexão crítica sobre o meu próprio trabalho — identificando limitações e oportunidades de melhoria — é tão importante quanto a capacidade de implementar.
Bibliografia
Laravel LLC. (2024). Laravel – The PHP framework for web artisans. Acedido em 21 de setembro de 2025, de https://laravel.com 
Filament. (2024). Filament – Accelerated Laravel development.Acedido em 21 de setembro de 2025, de https://filamentphp.com 
The PHP Group. (2024). PHP: Hypertext preprocessor. Acedido em 21 de setembro de 2025, de https://www.php.net 
Composer Authors. (2024). Composer – Dependency manager for PHP. Acedido em 21 de setembro de 2025, de https://getcomposer.org 
Oracle Corporation. (2024). MySQL 8.0 reference manual. Acedido em 1 de outubro de 2025, de https://dev.mysql.com/doc 
GitHub, Inc. (2024). GitHub – Where the world builds software. Acedido em 1 de outubro de 2025, de https://github.com 





Anexos


