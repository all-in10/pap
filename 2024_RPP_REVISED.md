# Curso Profissional de Técnico de Informática-Sistemas 
**Código de Referência do CNQ**: 481039  
**Ciclo de Formação**: 2023/2026  
**Ano letivo**: 2025/2026

## Prova de Aptidão Profissional - Relatório
### TeamCore - Uma nova gestão de Recursos Humanos

**Autor**: Victor Gabriel Cristino Gomes (N.º 21)  
**Orientador**: Zélia Capitão  
**Data de Relatório**: 14/02/2026 (em desenvolvimento)

**Agradecimentos especiais a**:
- Zélia Capitão
- Ana Paula Azevedo
- Jorge Miguel Pereira de Sousa Sequeiros
- Willian Washington

---

## Índice

1. Acrónimos e Abreviaturas
2. Resumo
3. Introdução
4. Desenvolvimento do Projeto
5. Conclusão
6. Bibliografia
7. Anexos

---

## Acrónimos/Abreviaturas/Siglas

| Sigla | Significado |
|-------|-------------|
| MVP | Minimal Viable Product |
| PAP | Prova de Aptidão Profissional |
| RH | Recursos Humanos |
| HR | Human Resources |
| CRUD | Create, Read, Update, Delete |
| ORM | Object-Relational Mapping |
| PT-PT | Português de Portugal |
| API | Application Programming Interface |
| UI | User Interface |
| UX | User Experience |
| PDF | Portable Document Format |
| CSRF | Cross-Site Request Forgery |
| PME | Pequenas e Médias Empresas |
| i18n | Internacionalização |
| RBAC | Role-Based Access Control |
| LGPD | Lei Geral de Proteção de Dados |

---

## Resumo

O presente relatório de PAP descreve o desenvolvimento do **TeamCore**, uma aplicação de gestão de Recursos Humanos concebida para simplificar os processos críticos deste setor. O projeto foi motivado pela necessidade de uma ferramenta de RH intuitiva e eficiente, contrastando com a complexidade de muitos sistemas existentes.

O TeamCore foi desenvolvido com os seguintes objetivos:
- Gestão abrangente de dados de funcionários, cargos, departamentos e contratos
- Automação de processos para minimizar margem de erro
- Suporte à decisão através de relatórios e gráficos estratégicos
- Qualidade técnica com foco em usabilidade, segurança e performance

A metodologia incluiu levantamento detalhado de requisitos, modelação de base de dados relacional, e desenvolvimento técnico usando Laravel 12 com Filament 3.3 para backend e frontend. O processo foi complementado por validação com profissionais de RH para garantir funcionalidades essenciais (MVP) e testes rigorosos.

**Nota sobre o Estado do Projeto**: No momento da redação, o TeamCore encontra-se em fase de desenvolvimento e testes finais, com conclusão prevista para 31 de Março de 2026. Os resultados preliminares indicam potencial significativo para aumentar eficiência operacional e suportar decisões estratégicas no ambiente empresarial.

---

## Introdução

### Enquadramento Teórico

A gestão eficaz de Recursos Humanos é uma das funções críticas em qualquer organização moderna. Com a transformação digital em aceleração, as aplicações de software para sistemas de informação de RH tornaram-se ferramentas essenciais para:

- Otimizar processos administrativos
- Reduzir erros operacionais
- Facilitar tomada de decisões estratégicas baseada em dados

O presente projeto insere-se na área de **Desenvolvimento de Sistemas de Informação**, especificamente na concepção e implementação de aplicações web para gestão empresarial. Este domínio abrange:

- **Desenvolvimento Backend**: Implementação de lógica de negócio, processamento de dados e integração de bases de dados relacionais
- **Desenvolvimento Frontend**: Criação de interfaces intuitivas e responsivas
- **Arquitetura de Software**: Desenho de sistemas escaláveis, seguros e fáceis de manter
- **Controlo de Acesso**: Implementação de autenticação e autorização para proteger dados sensíveis

### Fundamentação do Tema

Este projeto foi selecionado pelas seguintes razões estratégicas:

**Relevância Profissional**: O desenvolvimento de sistemas de RH representa uma aplicação prática dos conhecimentos adquiridos no Curso Profissional de Informática-Sistemas, incluindo:
- Programação em linguagens modernas
- Design de bases de dados relacionais
- Implementação de segurança e autenticação
- Design de interfaces de utilizador

**Problema Real**: Muitas organizações, especialmente PMEs, enfrentam dificuldades em gerenciar dados de funcionários de forma centralizada e eficiente. Sistemas legados frequentemente apresentam:
- Problemas de usabilidade
- Integração limitada
- Custos elevados de manutenção
- Falta de escalabilidade

**Oportunidade de Aprendizagem**: O escopo permite aplicar múltiplas tecnologias atuais do mercado (Laravel, Filament, MySQL, Pest) e consolidar conhecimentos em:
- Autenticação e autorização
- Validação de dados
- Testes automatizados
- Boas práticas de engenharia de software

**Viabilidade**: O projeto apresenta amplitude apropriada para ser completado num ciclo de desenvolvimento estruturado, permitindo implementar uma solução funcional com qualidade técnica.

### Motivação e Contexto

O TeamCore nasce da necessidade de uma ferramenta de Gestão de Recursos Humanos intuitiva e eficiente, capaz de apoiar tanto equipas de gestão como colaboradores nas operações diárias.

A solução foi concebida para:
- Centralizar a gestão de funcionários, cargos e contratos
- Automatizar tarefas de registo de tempo e horas extras
- Fornecer suporte à decisão através de relatórios e visualizações estruturadas

### Objetivos Principais

Este projeto PAP visa desenvolver um sistema de gestão que:

1. **Centraliza dados de RH**: Consolidar informação de funcionários, departamentos, designações, contratos e períodos de férias numa base de dados estruturada e facilmente consultável

2. **Automatiza registos de trabalho**: Permitir o registo eficiente de horas trabalhadas, com suporte a pausas/almoços, cálculo automático de horas extras e manutenção atualizada do banco de horas individual

3. **Implementa controlo de acesso granular**: Segregar permissões por função de utilizador (ADMIN, HR, EMPLOYEE), garantindo que cada utilizador acede apenas aos dados e funcionalidades apropriados

4. **Oferece experiência de utilizador otimizada**: Proporcionar uma interface intuitiva, traduzida para Português (pt-PT), com notificações contextuais e confirmações para prevenir ações não intencionais

5. **Garante qualidade técnica**: Assegurar a aplicação através de testes automatizados, validação de regras de autorização e boas práticas de segurança

### Abordagem e Metodologia

O desenvolvimento foi realizado em ciclos iterativos, adotando:

- **Tecnologias modernas**: Laravel 12, Filament 3.3, PHP 8.2+, MySQL 8.0
- **Testes automatizados**: Desenvolvimento orientado a testes usando Pest para validação contínua
- **Validação com profissionais**: Consulta com especialistas de RH para validar requisitos essenciais
- **Boas práticas de engenharia**: Isolamento de dados, arquitetura em camadas clara, padrões consistentes

---

## Desenvolvimento do Projeto

### Metodologia e Ferramentas

O projeto TeamCore foi desenvolvido com as seguintes tecnologias:

- **Framework Backend**: Laravel 12
- **Painel Administrativo**: Filament 3.3
- **Linguagem**: PHP 8.2+
- **Base de Dados**: MySQL 8.0
- **Framework de Testes**: Pest 3.8
- **Bundler Frontend**: Vite 7.0
- **CSS Framework**: Tailwind CSS 3.x
- **Geração PDF**: DomPDF 3.1

#### Arquitetura de Dados Relacional

O projeto suporta as seguintes entidades principais:

- **Funcionários (Employee)**: Dados pessoais, contactos e informações profissionais
- **Utilizadores (User)**: Credenciais de acesso, papéis e permissões
- **Contratos (Contract)**: Informações de vínculo laboral, tipo e remuneração
- **Departamentos (Department)**: Estrutura organizacional da empresa
- **Cargos (Designation)**: Definição de funções profissionais e salários base
- **Benefícios (Benefit)**: Subsídios e benefícios associados a funcionários
- **Banco de Horas (Hourbank)**: Controlo de horas acumuladas/deficitárias por funcionário
- **Registos de Presença (Attendance)**: Registos diários de entrada/saída e pausas
- **Registos de Trabalho (Worklog)**: Logs detalhados de atividades ejecutadas
- **Licenças e Faltas (Timeoff)**: Gestão de períodos de férias, licenças e justificações
- **Categorias de Licença (TimeoffCategory)**: Tipos de ausências permitidas
- **Tipos de Contrato (ContractType)**: Definições de tipos contratuais
- **Localização**: País, Estado e Cidade para preenchimento de dados de funcionários

#### Processo de Desenvolvimento

O processo de desenvolvimento adotou uma metodologia iterativa com:

- Ciclos bissemanais de análise, implementação, testes e validação
- Utilização de Git para controlo de versão
- Suite de testes automatizados executando a cada novo commit
- Garantia de que regressões não ocorrem durante desenvolvimento

### Stack Tecnológico

| Tecnologia | Versão | Propósito |
|-----------|--------|----------|
| Laravel | 12.0 | Framework backend, routing, ORM Eloquent |
| Filament | 3.3 | Painel administrativo, gestão de recursos |
| PHP | 8.2+ | Linguagem de desenvolvimento |
| MySQL | 8.0 | Base de dados relacional |
| Pest | 3.8 | Framework de testes automatizados |
| Vite | 7.0 | Bundler para assets frontend |
| DomPDF | 3.1 | Geração de relatórios em PDF |
| Tailwind CSS | 3.x | Framework CSS para estilização |

### Principais Implementações

#### 1. Arquitetura Multi-Painel

O projeto implementa uma arquitetura multi-painel com isolamento de dados por função:

- **Painel Admin (/admin)**: Gestão completa do sistema
  - Gestão de utilizadores e papéis
  - Gerenciamento de departamentos e cargos
  - Administração de benefícios
  - Controlo geral da aplicação

- **Painel RH (/hr)**: Gestão de Recursos Humanos
  - Gestão de funcionários
  - Gestão de contratos
  - Processamento de licenças e faltas
  - Relatórios estratégicos e análise

- **Painel Funcionário (/employee)**: Acesso pessoal
  - Visualização de dados pessoais
  - Registos de trabalho próprio
  - Solicitações de licença/ausência
  - Visualização de banco de horas

**Características de cada painel**:
- Páginas dedicadas com middleware específico para controlo de acesso
- Isolamento de dados ao nível do modelo através de políticas Eloquent
- Componentes UI e funcionalidades condicionadas ao perfil do utilizador
- Navegação e estrutura otimizadas para cada função

#### 2. Controlo de Acesso e Segurança

**RBAC (Role-Based Access Control)**:
- Implementação de três perfis base: ADMIN, HR, EMPLOYEE
- Políticas Eloquent para validação de autorização ao nível do modelo
- Operações CRUD protegidas por regras de negócio

**Isolamento de Dados**:
- Cada utilizador acede apenas aos dados apropriados à sua função
- Middleware customizado para proteção de rotas por canal/painel
- Queries filtered ao nível do modelo através de scopes e políticas

**Segurança Implementada**:
- Protecção CSRF em formulários
- Validação de permissões em cada ação
- Encriptação de dados sensíveis
- Logs de auditoria de operações sensíveis
- Política de senhas com força de troca obrigatória no primeiro acesso
- Campo toggle `must_change_password` integrado no formulário de utilizadores para controlo centralizado
- Middleware `EnforcePasswordChange` que redireciona utilizadores com força de troca ativa para fluxo de alteração
- Senha padrão configurável via variável de ambiente (`DEFAULT_USER_PASSWORD`)

#### 3. Gestão de Horas e Banco de Horas

**Registos de Trabalho**:
- Entrada/saída com timestamp automático
- Suporte a múltiplos registos por dia
- Cálculo automático de durações

**Pausas e Intervalos**:
- Campos 'break_start' / 'break_end'
- Exclusão automática de tempo de pausa nos cálculos
- Validação de lógica de intervalos

**Cálculo de Horas**:
- Arredondamento automático para horas e meia-horas
- Integração com banco de horas do funcionário
- Suporte a horas extras com multiplicadores diferenciados

**Banco de Horas (Hourbank)**:
- Atualização automática após cada registo validado
- Rastreamento de horas acumuladas/deficitárias
- Criação automática ao criar novo funcionário
- Relatórios e extratos de saldo

#### 4. Experiência do Utilizador (UX)

**Tradução PT-PT**:
- Rótulos e campos do formulário em português
- Mensagens de validação localizadas
- Colunas de tabelas traduzidas
- Datas e números formatados para PT-PT

**Notificações Contextuais**:
- Trait 'NotifiesCreatedItems' para notificações por recurso
- Supressão de notificações padrão do Filament
- Mensagens específicas por ação realizada

**Confirmações de Ação**:
- 'ConfirmsCancelAction' para evitar cancelamentos não intencionais
- Diálogos de confirmação em ações destrutivas
- Avisos de mudanças não salvas

**Badges Visuais**:
- Indicadores de função (role) na tabela de utilizadores
- Estados visuais para contratos (ativo, encerrado, suspenso)
- Indicadores de status de licenças

**Interface Responsiva**:
- Suporte completo a dispositivos móveis
- Layout adaptativo para tablets e desktop
- Navegação otimizada para múltiplos tamanhos de ecrã

#### 5. Testes e Validação

**Suite de Testes Automatizados**:
- Utilização de framework Pest para testes de funcionalidade
- Testes de negócio para validação de regras
- Conformidade com requisitos especificados

**Testes de Autorização**:
- Validação de políticas de acesso em cenários reais
- Verificação de isolamento de dados
- Testes de boundary conditions

**Testes de Integração**:
- Verificação de fluxos entre modelos relacionados
- Testes de eventos e listeners
- Validação de cascatas de atualização

**Execução Contínua**:
- Testes executam automaticamente em cada novo commit
- Garantia de satisfação de requisitos mesmo após mudanças
- Prevenção de regressões

#### 6. Melhorias e Otimizações

**Padronização de Senhas e Gestão de Política**:
- Definição de senha padrão para novos utilizadores (configurável via `DEFAULT_USER_PASSWORD`)
- Obrigatoriedade de alteração no primeiro acesso através de middleware dedicado
- Campo toggle `must_change_password` integrado no formulário de utilizadores para controlo centralizado e intuitivo
- Coluna IconColumn na tabela de utilizadores exibindo visualmente o status de força de troca (✓/✗)
- Reforço de políticas de segurança com interface de utilizador clara e acessível
- Middleware `EnforcePasswordChange` que redireciona utilizadores com força de troca ativa para fluxo seguro de alteração

**Actions Condicionadas**:
- Botões e ações visíveis apenas para perfis autorizados
- Desabilitamento contextual de funcionalidades
- Guia visual das permissões do utilizador

**Boas Práticas de Engenharia**:
- Código limpo e bem estruturado
- Documentação inline dos componentes críticos
- Padrões consistentes em toda a codebase
- Naming conventions claras e significativas
- Separação de responsabilidades clara

#### 7. Validação de E-mail e Automação de Criação (Novo - 16/02/2026)

**Validação Rigorosa de E-mail**:
- Custom Rule `ValidEmailDomain` que rejeita e-mails sem extensão de domínio válida (ex: rejeita `teste@teste`, aceita `usuario@empresa.com`)
- Regex validação: `/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/` garante TLD mínimo de 2 caracteres
- Aplicado em Employee e User Resources em formulários de criação/edição
- Protecção contra e-mails inválidos em toda a aplicação

**Sistema Automático de Gatilhos (Observer Pattern)**:
- `EmployeeObserver` que intercepta criação de Employee e automaticamente:
  - **Cria User** com email/nome do Employee (role: `employee`, senha default do .env, must_change_password: true)
  - **Cria Contract** (tipo indefinido, salário da designação, status ativo)
  - **Cria Hourbank** (saldo inicial 0 horas, accrual_date = data contratação)
  - **Armazena em cache** dados para notificações Filament
  - **Tratamento de erros** com logging automático

**Notificações Customizadas (Filament Toast)**:
- 4 notificações ao criar Employee:
  1. Utilizador criado (email, role, status força troca)
  2. Contrato criado (tipo, salário, data início)
  3. Banco de horas criado (saldo inicial, accrual_date)
  4. Consolidada final (checkmarks de sucesso)
- Cada notificação com ícone e cor personalizada para feedback visual claro

**Proteção contra Metadata Manual**:
- Remoção de `email_verified_at` do formulário UserResource para ser auto-preenchido
- `created_at` e `updated_at` nunca editáveis em formulários
- Campos metadata visíveis apenas em tabelas como `.toggleable(isToggledHiddenByDefault: true)`
- ActivityLogResource mostra `created_at` desabilitado (read-only)

#### 8. Recursos Filament Implementados

O projeto implementa 15 Resources Filament para gestão:

| Resource | Modelo | Funcionalidades |
|----------|--------|-----------------|
| EmployeeResource | Employee | CRUD completo com validações PT-PT |
| ContractResource | Contract | Gestão de contratos, tipos e datas |
| DepartmentResource | Department | Estrutura organizacional |
| DesignationResource | Designation | Cargos com níveis e salários |
| UserResource | User | Gestão de utilizadores |
| BenefitResource | Benefit | Benefícios e subsídios |
| AttendanceResource | Attendance | Registos de entrada/saída |
| WorklogResource | Worklog | Logs de atividades |
| TimeoffResource | Timeoff | Pedidos de licença/ausência |
| TimeoffCategoryResource | TimeoffCategory | Tipos de licenças |
| HourbankResource | Hourbank | Gestão de banco de horas |
| ContractTypeResource | ContractType | Definições de tipos contratuais |
| StateResource | State | Estados/províncias |
| CityResource | City | Cidades |
| CountryResource | Country | Países |

Cada Resource inclui:
- Formulários com validações apropriadas
- Tabelas com ordenação e pesquisa
- Ações customizadas por função
- Isolamento de dados por RBAC

---

## Conclusão

O desenvolvimento do TeamCore representou uma aplicação prática e abrangente dos conhecimentos adquiridos no Curso Profissional de Técnico de Informática-Sistemas. Este projeto encapsula competências fundamentais na área de Desenvolvimento de Sistemas de Informação, incluindo análise de requisitos, design de bases de dados relacionais, implementação de lógica de negócio complexa, segurança em sistemas de informação e interfacing com utilizadores finais.

**Realizações Principais:**
- Solução funcional de gestão de RH moderno, escalável e seguro com arquitetura multi-painel
- Isolamento completo de dados por função de utilizador (RBAC) com políticas de autorização granulares
- Interface intuitiva e responsiva com tradução completa para PT-PT, alinhada com padrões UX modernos
- Suite abrangente de testes automatizados (Pest/PHPUnit) garantindo qualidade técnica e validação contínua
- 17 widgets de visualização de dados (estatísticas e gráficos avançados) suportando decisões estratégicas
- Sistema robusto de gestão de senhas com políticas de segurança e força de troca obrigatória
- Exportação de documentos em PDF para contratos com suporte a localização
- Sistema automático de criação de User, Contract e Hourbank ao criar Employee com notificações contextuais
- Validação rigorosa de e-mail com Custom Rule (rejeita domínios inválidos)
- Proteção de campos metadata contra edição manual (auto-preenchidos pelo sistema)
- Documentação técnica detalhada e código bem estruturado facilitando manutenção futura

**Estado Atual (16 de Fevereiro de 2026):**
O projeto encontra-se em fase avançada com todas as funcionalidades principais implementadas, integradas e testadas. A arquitetura foi validada em cenários reais de utilização, e a aplicação demonstra estabilidade operacional. O desenvolvimento continua em direção à conclusão prevista para 31 de Março de 2026, preparando-se para testes finais, homologação com stakeholders e implementação.

O TeamCore demonstra viabilidade comercial significativa e potencial comprovado para apoiar organizações, especialmente PMEs, na modernização de seus processos de gestão de Recursos Humanos, reduzindo complexidade administrativa e enhancing decisões estratégicas através de relatórios e visualizações baseadas em dados.

---

## Anexos

### A. Imagens e Diagramas

[Inserir figuras e diagramas da arquitetura do sistema]

### B. Configuração do Ambiente

[Instruções de setup, dependências, variáveis de ambiente]

### C. Documentação de API

[Se aplicável, documentação de endpoints e schemas]

### D. Considerações Futuras

- Integração com sistemas de folha de pagamento
- Mobilidade aprimorada com aplicação móvel nativa
- Relatórios avançados com BI
- Notificações em tempo real (WebSockets)
- Internacionalização para múltiplos idiomas

---

**Fim do Relatório**
