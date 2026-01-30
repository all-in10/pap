# 🔐 Documentação - Sistema de Login Consolidado

**Data:** 30 de Janeiro de 2026  
**Status:** ✅ Implementado  
**Versão:** 2.0

---

## 📋 Visão Geral

O sistema de login foi refatorado para consolidar toda a autenticação num **único ponto de entrada** (`/app/login`). O **AppPanelProvider** funciona como **distribuidor central** que redireciona cada utilizador para seu painel específico conforme seu role (função).

---

## 🏗️ Arquitetura do Sistema de Login

### Antes (Login Múltiplo - ❌ Descontinuado)

```
/app/login      → Autenticação + AppPanel
/admin/login    → Autenticação + AdminPanel (duplicado)
/hr/login       → Autenticação + HrPanel (duplicado)
/employee/login → Autenticação + EmployeePanel (duplicado)

❌ Problema: 4 logins diferentes
❌ Problema: Duplicação de código
❌ Problema: Confusão de utilizador
```

### Depois (Login Único - ✅ Atual)

```
/app/login      → Autenticação centralizada (único ponto de entrada)
   ↓ (após login bem-sucedido)
   ├─ ROOT/ADMIN → redireciona para /admin
   ├─ HR        → redireciona para /hr
   ├─ EMPLOYEE  → redireciona para /employee
   └─ Fallback  → redireciona para /app

✅ Benefício: 1 login único
✅ Benefício: Código limpo
✅ Benefício: Melhor UX
✅ Benefício: AppPanel é distribuidor inteligente
```

---

## 🔄 Fluxo de Autenticação Detalhado

### 1️⃣ Utilizador Acessa Sistema

```
Utilizador digita: https://teamcore.com/admin
        ↓
Middleware detecta: não autenticado
        ↓
Redireciona para: /app/login (login centralizado)
        ↓
Apresenta: Formulário de login único
```

### 2️⃣ Utilizador Submete Login

```
Introduz email e password
        ↓
POST → /app/login
        ↓
AppPanelProvider autentica
        ↓
Laravel valida credenciais
        ↓
Se válido: cria sessão + token
```

### 3️⃣ Middleware Detecta Role e Redireciona

```
Auth::check() → verificar se autenticado ✅
        ↓
$user->role → obter função do utilizador
        ↓
match($role):
   ├─ ROOT/ADMIN → redirect('/admin')
   ├─ HR         → redirect('/hr')
   ├─ EMPLOYEE   → redirect('/employee')
   └─ default    → redirect('/app')
```

### 4️⃣ Utilizador Acede Seu Painel

```
/admin    → AdminPanelProvider (login=false, sem tela de login)
/hr       → HrPanelProvider (login=false, sem tela de login)
/employee → EmployeePanelProvider (login=false, sem tela de login)
/app      → AppPanelProvider (dashboard padrão)

✅ Utilizador já está autenticado
✅ Ses sessão é mantida
✅ Pode navegir livremente entre painéis (conforme permissões)
```

---

## 🔧 Componentes Técnicos

### 1. AppPanelProvider.php

**Função:** Painel principal com login único

```php
->default()              // Painel padrão
->id('app')              // ID único
->path('app')            // Rota: /app
->login()                // Ativa autenticação AQUI (único lugar!)
->loginUrl('/app/login') // URL do login
```

**Responsabilidades:**
- ✅ Único ponto de entrada para autenticação
- ✅ Ativa a tela de login
- ✅ Processa credenciais
- ✅ Cria sessão/token
- ✅ Redireciona para painel apropriado

---

### 2. AdminPanelProvider.php

**Função:** Painel administrativo SEM login próprio

```php
->default(false)         // Não é padrão
->id('admin')            // ID único
->path('admin')          // Rota: /admin
->login(false)           // SEM autenticação aqui
->loginUrl('/app/login') // Redireciona para AppPanel se não autenticado
```

**Responsabilidades:**
- ✅ Fornece interface de admin
- ✅ Redireciona para /app/login se não autenticado
- ✅ Middleware valida se role = ROOT ou ADMIN

---

### 3. HrPanelProvider.php

**Função:** Painel de RH SEM login próprio

```php
->id('hr')
->path('hr')
->login(false)           // SEM autenticação aqui
->loginUrl('/app/login') // Redireciona para AppPanel se não autenticado
```

---

### 4. EmployeePanelProvider.php

**Função:** Painel do funcionário SEM login próprio

```php
->id('employee')
->path('employee')
->login(false)           // SEM autenticação aqui
->loginUrl('/app/login') // Redireciona para AppPanel se não autenticado
```

---

### 5. Middleware: RedirectAuthenticatedFromAllLogins.php

**Função:** Impedir utilizadores autenticados de acessar logins

```php
// Se utilizador JÁ está autenticado e tenta:
/app/login      → redireciona para painel específico
/admin/login    → redireciona para painel específico
/hr/login       → redireciona para painel específico
/employee/login → redireciona para painel específico
```

**Lógica:**
```php
if (Auth::check() && isLoginRoute()) {
    return redirectToUserPanel($user->role);
}
```

---

## 📊 Tabela Comparativa

| Aspecto | Antes | Depois |
|---------|-------|--------|
| **Pontos de Login** | 4 (/app, /admin, /hr, /employee) | 1 (/app) |
| **Duplicação de Código** | Sim (4x) | Não |
| **UX/Usabilidade** | Confuso | Claro e intuitivo |
| **Manutenção** | Difícil | Fácil |
| **Segurança** | Múltiplas camadas | Camada única testada |
| **AppPanelProvider** | Redundante | Distribuidor inteligente |
| **RedirectAfterLogin** | Manual (4x) | Automático (1x) |
| **Falha de Login** | Apresenta erro no painel errado | Apresenta erro num único local |

---

## 🔐 Segurança

### Validações Implementadas

#### 1. Autenticação Centralizada
```php
// Único ponto de processamento de credenciais
POST /app/login → Valida no AppPanel → Cria sessão
```

#### 2. Middleware de Autorização

Cada painel tem seu middleware de acesso:

```php
// AdminPanel
EnsureAdminPanelAccess::class
// Valida: role === ROOT || role === ADMIN

// HrPanel
EnsureHrPanelAccess::class
// Valida: role === HR || role === ADMIN || role === ROOT

// EmployeePanel
EnsureEmployeePanelAccess::class
// Valida: role === EMPLOYEE
```

#### 3. Redirecionamento Automático

Se utilizador tenta acessar painel não autorizado:
```
/employee tenta acessar /admin
   ↓
EnsureAdminPanelAccess bloqueia
   ↓
Redireciona para painel autorizado (/employee)
```

#### 4. Proteção Contra Acesso Direto

```
Utilizador tenta: /admin/login diretamente
   ↓
Detectado como login route
   ↓
Se autenticado: redireciona para painel específico
   ↓
Se não autenticado: redireciona para /app/login
```

---

## 🚀 Fluxos de Utilizador

### Cenário 1: LOGIN NOVO (Utilizador não autenticado)

```
1. Utilizador digita: /admin
2. Middleware: não autenticado → redireciona para /app/login
3. Apresenta: Formulário de login
4. Utilizador: introduz email + password
5. AppPanel: autentica + cria sessão
6. Middleware: detecta role → redireciona para /admin
7. AdminPanel: acessa + apresenta painel admin
✅ Utilizador vê painel apropriado
```

---

### Cenário 2: UTILIZADOR JÁ AUTENTICADO

```
1. Utilizador tem sessão ativa
2. Tenta acessar: /admin/login (por engano)
3. Middleware: já autenticado → redireciona para /admin
✅ Bypassa login, vai direto para painel
```

---

### Cenário 3: MÚLTIPLOS PAINÉIS (Mesmo Utilizador)

```
1. Utilizador (HR) faz login → vai para /hr
2. Clica em link que vai para /admin
3. EnsureAdminPanelAccess: bloqueia (não é ADMIN)
4. Redireciona para /app ou /hr
✅ Protegido contra acesso não autorizado
```

---

### Cenário 4: LOGOUT

```
1. Utilizador em /admin clica "Sair"
2. Sessão terminada
3. Redireciona para /app/login
✅ Volta ao ponto de entrada único
```

---

## 📝 Configuração de Middleware

Para usar o novo middleware `RedirectAuthenticatedFromAllLogins`, adicione a `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ... outros middleware ...
    'redirect-authenticated-all-logins' => \App\Http\Middleware\RedirectAuthenticatedFromAllLogins::class,
];
```

Depois, aplique em `routes/web.php`:

```php
// Login redirect para qualquer tentativa de acesso a login
Route::middleware('redirect-authenticated-all-logins')->group(function () {
    // Filament handles these routes automatically
});
```

---

## 🧪 Testes de Validação

### Teste 1: Login Centralizado
```
✓ Aceder /app/login → formulário aparece
✓ Aceder /admin/login → redireciona para /app/login
✓ Aceder /hr/login → redireciona para /app/login
✓ Aceder /employee/login → redireciona para /app/login
```

### Teste 2: Redirecionamento por Role
```
✓ Utilizador ROOT faz login → vai para /admin
✓ Utilizador ADMIN faz login → vai para /admin
✓ Utilizador HR faz login → vai para /hr
✓ Utilizador EMPLOYEE faz login → vai para /employee
```

### Teste 3: Acesso não Autorizado
```
✓ EMPLOYEE tenta acessar /admin → redireciona para /employee
✓ HR tenta acessar /admin (sem permissão) → redireciona para /hr
✓ EMPLOYEE tenta acessar /hr → redireciona para /employee
```

### Teste 4: Autenticados Tentando Login
```
✓ Utilizador autenticado acessa /app/login → vai para seu painel
✓ Utilizador autenticado acessa /admin/login → vai para seu painel
✓ Utilizador autenticado acessa /hr/login → vai para seu painel
```

---

## 🎯 Benefícios

### Para Utilizadores
- ✅ **Único ponto de entrada** - Mais fácil de lembrar
- ✅ **Redirecionamento automático** - Vai direto para seu painel
- ✅ **Melhor UX** - Fluxo intuitivo

### Para Desenvolvedores
- ✅ **Código limpo** - Sem duplicação
- ✅ **Fácil manutenção** - Login em um único lugar
- ✅ **Escalável** - Adicionar novo role não requer novo login

### Para Administradores
- ✅ **Segurança centralizada** - Melhor controlo
- ✅ **Auditar em um lugar** - Logs de login num único ponto
- ✅ **Melhor gestão** - Configurações de sessão num único lugar

---

## 📋 Checklist de Implementação

- ✅ AppPanelProvider: login=true (único)
- ✅ AdminPanelProvider: login=false, loginUrl='/app/login'
- ✅ HrPanelProvider: login=false, loginUrl='/app/login'
- ✅ EmployeePanelProvider: login=false, loginUrl='/app/login'
- ✅ Middleware RedirectAuthenticatedFromAllLogins criado
- ✅ Middleware RedirectToPanelAfterLogin criado (opcional, para logging)
- ✅ Testes de redirecionamento validados
- ✅ Documentação completa

---

## 🔄 Próximas Melhorias (Futuro)

- [ ] Remember me (manter conectado)
- [ ] Two-factor authentication (2FA)
- [ ] Social login (Google, Microsoft)
- [ ] Audit log de logins
- [ ] Rate limiting em tentativas de login
- [ ] Custom login page com branding

---

## 📞 Suporte

Para dúvidas sobre o sistema de login, contacte:
- **Email:** desenvolvimento@teamcore.pt
- **Documentação:** [GUIA_DO_UTILIZADOR.md](GUIA_DO_UTILIZADOR.md)

---

**Versão:** 2.0  
**Última Atualização:** 30 de Janeiro de 2026  
**Status:** ✅ Implementado e Testado
