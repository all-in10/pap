# Ajustes de Permissões - TeamCore

## Resumo das Mudanças

O sistema de permissões foi refatorizado para implementar controlo de acesso granular baseado em funções de utilizador.

---

## Alterações Realizadas

### 1. **HRPanelProvider** (`app/Providers/Filament/HRPanelProvider.php`)
- Substituído `discoverResources()` genérico por lista explícita de resources permitidos
- **HR User** agora tem acesso apenas a:
  - EmployeeResource
  - ContractResource
  - DepartmentResource
  - DesignationResource
  - TimeoffResource
  - TimeoffCategoryResource
  - BenefitResource
  - WorklogResource
  - HourbankResource
  - AttendanceResource

- **HR User** NÃO tem acesso a:
  - ❌ UserResource
  - ❌ CountryResource
  - ❌ StateResource
  - ❌ CityResource
  - ❌ ContractTypeResource

### 2. **Policies Criadas** (`app/Policies/`)
Criadas 5 Policies restritivas que garantem acesso exclusivo ao ADMIN:

- `UserPolicy.php` - Protege acesso a utilizadores
- `CountryPolicy.php` - Protege acesso a países
- `StatePolicy.php` - Protege acesso a estados
- `CityPolicy.php` - Protege acesso a cidades
- `ContractTypePolicy.php` - Protege acesso a tipos de contrato

Cada Policy implementa todos os métodos CRUD padrão:
- `viewAny()` - Apenas ADMIN
- `view()` - Apenas ADMIN
- `create()` - Apenas ADMIN
- `update()` - Apenas ADMIN
- `delete()` - Apenas ADMIN
- `restore()` - Apenas ADMIN
- `forceDelete()` - Apenas ADMIN

### 3. **AppServiceProvider** (`app/Providers/AppServiceProvider.php`)
- Registadas todas as Policies no método `boot()`
- Ligação entre Models e respetivas Policies via `Gate::policy()`

### 4. **Testes** (`tests/Feature/PermissionsTest.php`)
Suite de 10 testes que validam:
- ✅ Admin pode visualizar Users, Countries, States, Cities, ContractTypes
- ✅ HR não pode visualizar nenhum desses recursos

**Resultado:** Todos os testes passam ✓

---

## Estratégia de Segurança (Defesa em Profundidade)

1. **Nível 1 - Panel Configuration**: HRPanelProvider não registra resources bloqueados
2. **Nível 2 - Authorization Policies**: Mesmo que alguém tente aceder, as Policies bloqueiam
3. **Nível 3 - Middleware**: EnsurePanelRole valida correspondência entre role e painel

---

## Admin Panel

**Admin User** mantém acesso total a TODOS os resources:
- ✅ Acesso irrestrito via `discoverResources()` no AdminPanelProvider
- ✅ Todas as Policies permitem ações (role === 'ADMIN')

---

## Próximas Ações (Recomendadas)

1. Testar manualmente no painel HR para confirmar que resources bloqueados não aparecem
2. Adicionar testes de integração para validar comportamento do Filament
3. Documentar as regras de permissões para referência futura
4. Considerar criar uma tabela de permissões mais granular se forem necessários permissões específicas por recurso

---

## Ficheiros Modificados

```
 M app/Providers/AppServiceProvider.php
 M app/Providers/Filament/HRPanelProvider.php
 A app/Policies/UserPolicy.php
 A app/Policies/CountryPolicy.php
 A app/Policies/StatePolicy.php
 A app/Policies/CityPolicy.php
 A app/Policies/ContractTypePolicy.php
 A tests/Feature/PermissionsTest.php
```

---

*Data de Conclusão: 05 de Fevereiro de 2026*
