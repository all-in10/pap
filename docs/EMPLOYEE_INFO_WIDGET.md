# Widget de Informações do Funcionário - TeamCore

## Resumo

Criado novo widget `EmployeeInfoWidget` que mostra informações pessoais e profissionais do funcionário autenticado, com acesso rápido para solicitar licenças/férias.

---

## Ficheiros Criados/Modificados

### 1. **EmployeeInfoWidget.php** (`app/Filament/Widgets/EmployeeInfoWidget.php`)
- Novo widget que carrega informações do funcionário autenticado
- Obtém dados via `Auth::user()->employee_id`
- Calcula banco de horas (última entrada de Hourbank)
- Conta pedidos de férias pendentes
- Pronto para ser incluído em painéis

### 2. **employee-info-widget.blade.php** (`resources/views/filament/widgets/employee-info-widget.blade.php`)
- View do widget com layout responsivo
- Exibe 4 seções principais:

#### Seção 1: Informações Pessoais
- Nome Completo
- Cargo (Designation)
- Departamento
- Email
- Data de Admissão
- Telefone

#### Seção 2: Métricas Importantes
- Banco de Horas (com ícone de relógio)
- Pedidos Pendentes (com ícone de calendário)

#### Seção 3: Ações Rápidas
- Botão "Solicitar Licença/Férias" → rota `filament.employee.resources.timeoffs.create`
- Botão "Ver Meus Pedidos" → rota `filament.employee.resources.timeoffs.index`

#### Seção 4: Observações
- Exibição de observações (se existirem)

### 3. **EmployeePanelProvider.php** (MODIFICADO)
- Adicionado `EmployeeInfoWidget` à lista de widgets
- Será exibido no dashboard do painel Employee

---

## Design e Funcionalidades

### Estilo Visual
- Gradientes coloridos para cada seção
- Borders temáticos (azul para info, verde para horas, orange para pendentes, roxo para ações)
- Ícones SVG integrados (relógio, calendário, mais)
- Modo escuro suportado (dark mode ready)
- Layout totalmente responsivo (mobile, tablet, desktop)

### Funcionalidades
- ✅ Carrega automaticamente dados do funcionário autenticado
- ✅ Exibe banco de horas atualizado
- ✅ Conta pedidos pendentes
- ✅ Botões de ação com links para criar/listar férias
- ✅ Mensagem de erro se não houver funcionário associado
- ✅ Suporte a observações personalizadas

---

## Como Usar

O widget é automaticamente exibido no dashboard do painel Employee. Para usá-lo noutros painéis:

```php
->widgets([
    \App\Filament\Widgets\EmployeeInfoWidget::class,
])
```

---

## Rotas Utilizadas

O widget presume a existência destas rotas (padrão Filament):
- `filament.employee.resources.timeoffs.create` - Criar novo pedido
- `filament.employee.resources.timeoffs.index` - Listar pedidos

Se estas rotas não existirem, os botões não funcionarão. Nesse caso, é preciso criar um Resource `TimeoffResource` no painel Employee.

---

## Próximas Ações (Recomendadas)

1. **Testar em produção** - Verificar se os dados carregam corretamente
2. **Criar TimeoffResource para Employee** - Se ainda não existir
3. **Adicionar validações** - Garantir que Employee só pode criar/editar seus próprios registos
4. **Expandir widget** - Adicionar mais métricas (horas trabalhadas este mês, contratos ativos, etc)
5. **Notificações** - Adicionar badge com notificações pendentes

---

## Estrutura de Dados Necessária

O widget presume que existe a relação:
- `User` → `Employee` (via `employee_id`)
- `Employee` → `Hourbank` (última entrada)
- `Employee` → `Timeoff` (com campo `status`)

---

*Data de Conclusão: 05 de Fevereiro de 2026*
