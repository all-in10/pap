# 📖 Guia do Utilizador - TeamCore HR Management

**Versão:** 1.0  
**Data:** 30 de Janeiro de 2026  
**Idioma:** Português (pt-PT)

---

## 📑 Índice

1. [Bem-vindo ao TeamCore](#bem-vindo-ao-teamcore)
2. [Acesso e Autenticação](#acesso-e-autenticação)
3. [Guias por Função](#guias-por-função)
4. [Funcionalidades Comuns](#funcionalidades-comuns)
5. [Tarefas Frequentes](#tarefas-frequentes)
6. [FAQ](#faq)
7. [Troubleshooting](#troubleshooting)
8. [Contactos e Suporte](#contactos-e-suporte)

---

## Bem-vindo ao TeamCore

**TeamCore** é um sistema de gestão de Recursos Humanos que simplifica os processos de gestão de funcionários, rastreamento de horas, controlo de férias e muito mais.

### O que pode fazer no TeamCore?

✅ **Gerir dados de funcionários** - Adicionar, editar e visualizar informações de colaboradores  
✅ **Rastrear horas de trabalho** - Registar entrada/saída com cálculo automático  
✅ **Solicitar férias e licenças** - Submeter pedidos e acompanhar status  
✅ **Visualizar banco de horas** - Consultar saldo de horas acumuladas  
✅ **Gerar relatórios** - Análises e gráficos de desempenho  
✅ **Gerir benefícios** - Atribuir e consultar benefícios organizacionais

### Segurança e Privacidade

🔒 **Dados Protegidos** - Seu acesso é restrito conforme seu role (função)  
🔐 **Encriptação** - Todas as comunicações são seguras  
📋 **Auditoria** - Todas as alterações são registadas

---

## Acesso e Autenticação

### 1. Fazer Login

**Login Centralizado - Um Único Ponto de Entrada**

1. Abra o navegador e aceda a **qualquer uma destas rotas:**
   - `https://seudominio.com/app/login` (Login direto)
   - `https://seudominio.com/admin` (Redireciona para login)
   - `https://seudominio.com/hr` (Redireciona para login)
   - `https://seudominio.com/employee` (Redireciona para login)

2. Será redirecionado automaticamente para: `https://seudominio.com/app/login`

3. Introduza seu **email** e **password**

4. Clique em **"Entrar"**

5. O sistema redirecionará automaticamente para seu painel:
   - **ROOT/ADMIN** → `/admin`
   - **HR** → `/hr`
   - **EMPLOYEE** → `/employee`
   - **Outro** → `/app` (dashboard padrão)

```
Email: user@example.com
Senha: (aquela que lhe foi fornecida)

Seu painel específico aparecerá automaticamente após login
```

ℹ️ **Nota:** Existe apenas **um login** para toda a aplicação. Não há múltiplos logins.

### 2. Alterar Password na Primeira Sessão

⚠️ **Obrigatório** - Na primeira vez que acede, deve alterar sua password:

1. Após login, clique no **menu de perfil** (ícone no canto superior direito)
2. Selecione **"Alterar Password"**
3. Introduza sua **nova password** (mínimo 8 caracteres)
4. Confirme a password
5. Clique em **"Guardar"**

### 3. Recuperar Password

Se esqueceu sua password:

1. Na página de login (qualquer uma delas redireciona para `/app/login`)
2. Clique em **"Esqueceu a password?"**
3. Introduza seu **email**
4. Clique em **"Enviar Link"**
5. Abra o email e clique no link fornecido
6. Introduza uma **nova password**
7. Clique em **"Restaurar Password"**

ℹ️ **Nota:** O login é centralizado, então recuperação de password funciona em um único lugar.

### 4. Logout (Sair)

1. Clique no **menu de perfil** (canto superior direito)
2. Selecione **"Sair"**

---

## Guias por Função

### 👑 ROOT (Superadministrador)

**Acesso:** Controlo total da aplicação  
**Painéis:** /admin, /app, /hr, /employee

#### Funcionalidades Disponíveis

| Tarefa | Como Fazer |
|--------|-----------|
| **Gerir Utilizadores** | Admin → Utilizadores → Ações (Criar, Editar, Deletar) |
| **Gerir Funcionários** | Admin → Funcionários → CRUD completo |
| **Gerir Contratos** | Admin → Contratos → Editar salários, datas, tipos |
| **Ver Todas as Horas** | Admin → Registos de Trabalho → Filtrar por período |
| **Aprovar/Rejeitar Férias** | HR → Solicitações → Aprovar/Rejeitar |
| **Gerir Departamentos** | Admin → Departamentos → Criar/Editar |
| **Ver Relatórios** | Dashboard → Gráficos e estatísticas |
| **Aceder Auditoria** | Admin → Logs de Auditoria → Visualizar alterações |

#### Dashboard ROOT

Seu dashboard mostra:
- 📊 Estatísticas globais (Total Utilizadores, Funcionários, Contratos)
- 📈 Gráficos de contratos por tipo
- 🍰 Distribuição de funcionários por departamento
- 📅 Status de férias pendentes

---

### 🔑 ADMIN (Administrador)

**Acesso:** Gestão quase total (sem acesso a Utilizadores)  
**Painéis:** /admin, /app

#### Funcionalidades Disponíveis

| Tarefa | Como Fazer |
|--------|-----------|
| **Gerir Funcionários** | Admin → Funcionários → Criar/Editar |
| **Registar Horas** | Admin → Registos de Trabalho → Novo registo |
| **Editar Horas** | Admin → Registos de Trabalho → Clique no registo |
| **Ver Contratos** | Admin → Contratos → Visualizar e editar |
| **Aprovar Férias** | HR → Solicitações → Revisar e aprovar |
| **Gerir Designações** | Admin → Designações → Criar/Editar |
| **Ver Dashboard** | Dashboard → Widgets com métricas |

#### Tarefas Comuns do ADMIN

**Criar novo funcionário:**
1. Vá para Admin → Funcionários
2. Clique em **"+ Novo Funcionário"**
3. Preencha os dados (Nome, Email, Data de Admissão, etc.)
4. Selecione Departamento e Cargo
5. Clique em **"Criar"**
6. Uma password temporária será enviada por email

**Registar hora para um funcionário:**
1. Vá para Admin → Registos de Trabalho
2. Clique em **"+ Novo Registo"**
3. Selecione o funcionário
4. Introduza hora de entrada e saída
5. (Opcional) Adicione pausa
6. Clique em **"Guardar"**

---

### 👤 HR (Recursos Humanos)

**Acesso:** Gestão de dados de RH (sem capacidade de deletar)  
**Painéis:** /app, /hr

#### Funcionalidades Disponíveis

| Tarefa | Como Fazer |
|--------|-----------|
| **Ver Funcionários** | RH → Funcionários → Lista com filtros |
| **Criar Funcionário** | RH → Funcionários → Novo (sem poder deletar) |
| **Editar Funcionário** | RH → Funcionários → Clique no funcionário |
| **Ver Solicitações** | RH → Solicitações → Revisar lista |
| **Aprovar Férias** | RH → Solicitações → Selecione e clique Aprovar |
| **Rejeitar Férias** | RH → Solicitações → Selecione e clique Rejeitar |
| **Gerir Contratos** | RH → Contratos → Visualizar e editar |
| **Ver Relatórios** | RH → Dashboard → Gráficos de departamento |

#### Tarefas Comuns do HR

**Revisar solicitação de férias:**
1. Vá para RH → Solicitações
2. Clique na solicitação para visualizar detalhes
3. Leia o motivo e datas
4. Clique em **"Aprovar"** ou **"Rejeitar"**
5. Adicione comentário (opcional)
6. Clique em **"Confirmar"**
7. O funcionário será notificado automaticamente

**Visualizar histórico de horas de um funcionário:**
1. Vá para RH → Funcionários
2. Selecione o funcionário
3. Clique em **"Registos de Trabalho"**
4. Filtre por período (data início/fim)
5. Visualize tabela com horas registadas

---

### 💼 EMPLOYEE (Funcionário)

**Acesso:** Apenas seus dados pessoais  
**Painéis:** /employee

#### Funcionalidades Disponíveis

| Tarefa | Como Fazer |
|--------|-----------|
| **Ver Meu Perfil** | Dashboard → EmployeeInfoWidget |
| **Ver Minhas Horas** | Dashboard → Resumo de Ponto |
| **Ver Banco de Horas** | Dashboard → Histórico de Banco de Horas |
| **Solicitar Férias** | Dashboard → "Nova Solicitação" → Férias |
| **Solicitar Licença** | Dashboard → "Informações sobre Licenças" |
| **Solicitar Justificativa** | Dashboard → "Nova Solicitação" → Justificativa |
| **Acompanhar Solicitações** | Dashboard → Histórico de Solicitações |

#### Seu Dashboard Employee

Seu painel pessoal mostra:

**Header (Topo):**
- 👤 **Informações Pessoais:** Seu nome, email, departamento, cargo
- 📊 **Estatísticas:** Total de horas, horas extras, saldo de banco

**Conteúdo Principal:**
- 🟢 **Solicitar Férias** - Clique para submeter pedido de férias
- 🔵 **Informações sobre Licenças** - Saiba mais sobre suas licenças

**Histórico:**
- 📅 **Férias/Ausências** - Tabela com pedidos submetidos
- 📜 **Licenças** - Tabela com licenças registadas

**Footer (Rodapé):**
- 📋 **Resumo de Ponto** - Últimos 10 registos com horas
- 📈 **Histórico de Banco de Horas** - Gráfico dos últimos 30 dias
- 📜 **Informações de Licenças** - Estatísticas de suas licenças

#### Tarefas Comuns do EMPLOYEE

**Solicitar férias:**
1. No seu dashboard, clique em **"Solicitar Férias"** (botão verde)
2. Ou vá para Admin → Solicitações → **"+ Novo"**
3. Preencha os campos:
   - **Tipo:** Selecione "Férias"
   - **Data de Início:** Primeira data de férias
   - **Data de Fim:** Última data de férias
   - **Motivo:** Descrição breve (ex: "Descanso pessoal")
4. Clique em **"Submeter"**
5. Sua solicitação irá para aprovação do RH
6. Receberá notificação quando for aprovada ou rejeitada

**Solicitar justificativa de ausência:**
1. No seu dashboard, clique em **"Solicitar Justificativa"**
2. Preencha:
   - **Data:** Data em que faltou
   - **Motivo:** Por que faltou (ex: "Consulta médica")
   - **Documentação:** (Opcional) Adicione documento comprovativo
3. Clique em **"Submeter"**

**Consultar seu banco de horas:**
1. No seu dashboard, localize **"Histórico de Banco de Horas"**
2. Visualize o gráfico com dados dos últimos 30 dias
3. Cores:
   - 🟦 **Azul** = Horas normais
   - 🟧 **Laranja** = Horas extras
4. Passe o mouse no gráfico para ver valores exatos

**Ver seus últimos registos de ponto:**
1. No seu dashboard, localize **"Resumo de Ponto"**
2. Visualize tabela com:
   - 📅 Data do registo
   - 🕐 Hora de entrada
   - 🕑 Hora de saída
   - ⏱️ Total de horas trabalhas
3. Clique no registo para ver detalhes (pausa, etc.)

---

## Funcionalidades Comuns

### 📊 Dashboard

**O que é o Dashboard?**
É sua página inicial que mostra informações e widgets relevantes conforme seu role.

**Como acessar:**
- Após fazer login, você chega automaticamente ao dashboard
- Ou clique no ícone de "Casa" (Home) no menu

**Elementos do Dashboard:**

| Elemento | Descrição |
|----------|-----------|
| **Header** | Widgets com informações principais (stats, gráficos) |
| **Conteúdo Principal** | Seção de ações e formulários |
| **Footer** | Widgets com dados detalhados |
| **Dark Mode** | Alternar entre tema claro/escuro (ícone no topo) |

---

### 🔔 Notificações

**Onde ver notificações:**
1. Clique no **ícone de sino** (🔔) no canto superior direito
2. Verá um dropdown com notificações recentes
3. A cor **vermelha** indica notificações não lidas

**Tipos de notificações:**

| Tipo | Exemplo |
|------|---------|
| **Criação** | "Funcionário João Silva criado" |
| **Atualização** | "Seu perfil foi atualizado" |
| **Aprovação** | "Suas férias foram aprovadas" |
| **Rejeição** | "Sua solicitação foi rejeitada" |
| **Lembrete** | "Seu contrato vence em 30 dias" |

---

### 🔍 Filtros e Busca

Muitas listas (Funcionários, Registos, Solicitações) têm filtros disponíveis.

**Como usar filtros:**
1. Na página de lista, clique em **"Filtros"** (ícone de funil)
2. Selecione os critérios desejados
3. Clique em **"Aplicar"**
4. Os resultados serão atualizados automaticamente

**Exemplos de filtros:**
- 📅 **Por Data** - Filtrar por período
- 🏢 **Por Departamento** - Visualizar apenas um depto
- 👤 **Por Funcionário** - Dados de um colaborador
- 📊 **Por Status** - Aprovado, Pendente, Recusado

---

### 📥 Importar/Exportar Dados

**Exportar para CSV:**
1. Na página de lista, clique em **"Exportar"** (ícone de download)
2. O arquivo CSV será baixado automaticamente
3. Abra em Excel ou similar

**Importar dados:**
1. Na página de lista, clique em **"Importar"** (ícone de upload)
2. Selecione seu arquivo CSV
3. Mapeie as colunas com os campos do sistema
4. Clique em **"Importar"**

---

### 🖨️ Imprimir

**Como imprimir uma página:**
1. Pressione **Ctrl+P** (Windows/Linux) ou **Cmd+P** (Mac)
2. Configure as opções de impressão
3. Clique em **"Imprimir"**

**Alternativa:**
1. Clique no ícone de **"Impressora"** (se disponível na página)
2. Escolha impressora
3. Clique em **"Imprimir"**

---

## Tarefas Frequentes

### ✏️ Criar um novo item

**Genérico para qualquer recurso:**
1. Aceda à página do recurso (ex: Funcionários)
2. Clique no botão **"+ Novo"** ou **"Criar"**
3. Preencha o formulário com dados obrigatórios (marcados com *)
4. Clique em **"Criar"** ou **"Guardar"**
5. Uma notificação de sucesso aparecerá

---

### ✏️ Editar um item

**Passos:**
1. Aceda à lista do recurso
2. Localize o item na tabela
3. Clique no item ou no ícone de **"Editar"** (✏️)
4. Modifique os dados desejados
5. Clique em **"Guardar"**
6. Uma notificação de confirmação aparecerá

⚠️ **Confirmação ao Cancelar:** Se clicar em "Cancelar" com alterações não salvas, será pedida confirmação.

---

### 🗑️ Deletar um item

**Passos:**
1. Aceda à lista do recurso
2. Localize o item
3. Clique no ícone de **"Deletar"** (🗑️) ou menu de ações
4. Confirme a deleção na caixa de diálogo
5. O item será marcado como "deletado" (soft delete)

ℹ️ **Nota:** Itens deletados podem ser recuperados pelo administrador se necessário.

---

### 🔒 Alterar Permissões

**Apenas ROOT/ADMIN podem alterar roles:**
1. Vá para Admin → Utilizadores
2. Selecione o utilizador
3. Clique em **"Editar"**
4. Altere o campo **"Função"** (Role)
5. Clique em **"Guardar"**

---

### 📱 Usar em Dispositivo Móvel

**O TeamCore é responsivo:**
1. Abra em seu telemóvel ou tablet
2. Interface adapta-se automaticamente
3. Todos os recursos funcionam normalmente
4. Gestos de toque funcionam como em desktop

**Dicas para móvel:**
- Toque no menu (☰) para abrir navegação
- Deslize para esquerda/direita para mais opções
- Aumente o zoom se necessário (Ctrl + + ou Cmd + +)

---

## FAQ

### ❓ Perguntas Frequentes

**P: Posso alterar o email de outro utilizador?**
R: Não, apenas ROOT/ADMIN podem editar utilizadores. Contacte seu administrador.

**P: Como exporto meus registos de trabalho?**
R: Na página de Registos de Trabalho, clique em "Exportar" → "CSV". O arquivo será baixado.

**P: Qual é o horário obrigatório de trabalho?**
R: Depende do seu contrato. Consulte o seu gestor ou departamento de RH.

**P: Posso solicitar férias retroativas?**
R: Não, as datas de férias devem ser futuras. Solicite uma justificativa para datas passadas.

**P: Como vejo quantas férias tenho direito?**
R: No seu dashboard, procure "Informações de Licenças" que mostra o saldo anual.

**P: Minha solicitação foi rejeitada. Porque?**
R: Clique na solicitação para ver comentários do revisor. Contacte seu gestor para esclarecimentos.

**P: Posso editar um registo de trabalho depois de criado?**
R: Sim, se tiver permissão. Vá para Admin/HR → Registos de Trabalho → Clique no registo → Editar.

**P: Como mudo de password?**
R: Clique no seu perfil (canto superior direito) → Alterar Password → Introduza nova password.

**P: Onde vejo o histórico de alterações?**
R: ROOT/ADMIN podem ver em Admin → Logs de Auditoria. Mostra quem alterou o quê e quando.

**P: Posso ver dados de outros funcionários?**
R: Depende do seu role. EMPLOYEE vê apenas seus dados. HR/ADMIN veem dados de RH conforme necessário.

**P: Como funciona o dark mode?**
R: Clique no ícone de lua/sol (topo direito) para alternar entre temas claro e escuro.

---

## Troubleshooting

### 🔴 Problemas Comuns

#### ❌ "Acesso Recusado" ou "Não autorizado"

**Causa:** Você não tem permissão para acessar esse recurso.

**Solução:**
1. Verifique seu role (função)
2. Contacte seu administrador se achar que deveria ter acesso
3. Faça logout e login novamente

---

#### ❌ "Erro 404 - Página não encontrada"

**Causa:** A página não existe ou o URL está incorreto.

**Solução:**
1. Verifique se o URL está correto
2. Clique em "Voltar" e navegue através do menu
3. Limpe o cache do navegador (Ctrl+Shift+Delete)
4. Contacte suporte se persistir

---

#### ❌ "Erro ao guardar" ou "Validação falhou"

**Causa:** Dados inválidos ou campos obrigatórios vazios.

**Solução:**
1. Procure pela mensagem de erro (geralmente em vermelho)
2. Verifique campos obrigatórios (marcados com *)
3. Corrija os dados conforme indicado
4. Tente guardar novamente

---

#### ❌ "Sessão expirada"

**Causa:** Sua sessão expirou por inatividade.

**Solução:**
1. Faça login novamente
2. Seus dados não foram perdidos (salve primeiro)
3. Se estava editando, volte e continue

---

#### ❌ Página carrega lentamente

**Causa:** Muitos dados ou conexão lenta.

**Solução:**
1. Filtre dados para período menor
2. Limpe cache do navegador
3. Feche abas desnecessárias
4. Verifique sua conexão de internet

---

#### ❌ Notificações não aparecem

**Causa:** Notificações podem estar desativadas.

**Solução:**
1. Verifique se há notificações (ícone de sino)
2. Procure nas "Notificações Antigas"
3. Refresque a página (F5)
4. Contacte suporte se problema persistir

---

#### ❌ Dark mode não funciona

**Causa:** Configuração do navegador ou cache.

**Solução:**
1. Limpe cache do navegador
2. Clique em ícone de lua/sol para alternar
3. Experimente outro navegador
4. Verifique se JavaScript está ativado

---

### 🆘 Contactar Suporte

Se o problema persistir:

**Informações a fornecer:**
- Seu nome e email
- Função (role) no sistema
- O que estava tentando fazer
- Mensagem de erro exata
- Screenshot se possível

**Como contactar:**
- 📧 Email: suporte@teamcore.pt
- 📞 Telefone: +351 XXX XXX XXX
- 💬 Chat: Em desenvolvimento

---

## Contactos e Suporte

### 📞 Equipa de Suporte

| Assunto | Contacto |
|---------|----------|
| **Problemas Técnicos** | suporte@teamcore.pt |
| **Dados de Funcionários** | rh@teamcore.pt |
| **Integração com Nómina** | integracao@teamcore.pt |
| **Treinamento** | treinamento@teamcore.pt |

### 🕐 Horários de Atendimento

- **Segunda a Sexta:** 9:00 - 18:00
- **Sábado/Domingo:** Encerrado
- **Feriados:** Contacte por email

### 📚 Documentação Adicional

- **Documentação Técnica:** [pap_development_summary.md](pap_development_summary.md)
- **Guia de Segurança:** [ROLES_DOCUMENTATION.md](ROLES_DOCUMENTATION.md)
- **API Documentation:** [API_COMPLETE_DOCUMENTATION.md](API_COMPLETE_DOCUMENTATION.md)

### 💡 Dicas Gerais

✅ **Faça backup regular** de seus dados importantes  
✅ **Use password forte** (8+ caracteres, misture tipos)  
✅ **Logout em computadores públicos**  
✅ **Reporte problemas rapidamente** para resolução mais rápida  
✅ **Esteja atento a notificações** para informações importantes

---

## Conclusão

O **TeamCore** foi desenvolvido para ser intuitivo e fácil de usar. Se tiver dúvidas, consulte este guia ou contacte o suporte.

**Bem-vindo ao TeamCore!** 🎉

---

**Versão:** 1.0  
**Última Atualização:** 30 de Janeiro de 2026  
**Próxima Revisão:** Maio de 2026
