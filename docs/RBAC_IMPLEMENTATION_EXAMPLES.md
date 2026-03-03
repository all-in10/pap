# RBAC - Exemplos Práticos de Implementação

## 📚 Índice
1. [Verificar & Autorizar](#1-verificar--autorizar)
2. [Criar Nova Funcionalidade](#2-criar-nova-funcionalidade)
3. [Casos de Uso Reais](#3-casos-de-uso-reais)
4. [Tratamento de Erros](#4-tratamento-de-erros)

---

## 1. Verificar & Autorizar

### 1.1 Em Controllers

#### ✅ Básico - Bloquear Acesso
```php
// app/Http/Controllers/EmployeeController.php
namespace App\Http\Controllers;

use App\Models\Employee;

class EmployeeController extends Controller
{
    public function update(Employee $employee)
    {
        // Valida autorização - lança 403 se não permitido
        $this->authorize('update', $employee);
        
        // Segue com a operação
        $employee->update(request()->validated());
        
        return response()->json(['message' => 'Atualizado com sucesso']);
    }

    public function delete(Employee $employee)
    {
        // HR não consegue eliminar
        // Apenas ADMIN consegue
        $this->authorize('delete', $employee);
        
        $employee->delete();
        
        return response()->json(['message' => 'Eliminado com sucesso']);
    }
}
```

#### ✅ Condicional - Mostrar Botão
```php
// Em Filament Resource ou Blade
public static function canView(Model $record): bool
{
    return auth()->user()?->can('view', $record) ?? false;
}

public static function canEdit(Model $record): bool
{
    return auth()->user()?->can('update', $record) ?? false;
}

public static function canDelete(Model $record): bool
{
    return auth()->user()?->can('delete', $record) ?? false;
}
```

#### ✅ Com Mensagem Customizada
```php
public function deleteMultiple()
{
    $employees = Employee::whereIn('id', request()->ids)->get();
    
    foreach ($employees as $employee) {
        if (auth()->user()->cannot('delete', $employee)) {
            return response()->json(
                ['error' => "Não consegue eliminar {$employee->full_name}"],
                403
            );
        }
        $employee->delete();
    }
    
    return response()->json(['message' => 'Eliminados com sucesso']);
}
```

---

### 1.2 Em Blade Templates

#### ✅ Mostrar Conteúdo Condicionalmente
```blade
<!-- resources/views/employee/show.blade.php -->

<div class="employee-card">
    <h2>{{ $employee->full_name }}</h2>
    <p>{{ $employee->email }}</p>
    
    <!-- Mostrar dados sensíveis só para Admin/HR -->
    @can('update', $employee)
        <div class="salary-info">
            <strong>Salário:</strong> €{{ number_format($employee->salary, 2) }}
        </div>
    @endcan
    
    <!-- Botão editar só para Admin/HR -->
    @can('update', $employee)
        <a href="{{ route('employee.edit', $employee) }}" class="btn btn-primary">
            ✏️ Editar
        </a>
    @endcan
    
    <!-- Botão eliminar só para Admin -->
    @can('delete', $employee)
        <form method="POST" action="{{ route('employee.destroy', $employee) }}" style="display:inline;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('Confirma eliminação?')">
                🗑️ Eliminar
            </button>
        </form>
    @endcan
</div>

<!-- Mostrar mensagem confidencial só para Admin -->
@if(auth()->user()->can('view-audit'))
    <div class="alert alert-info">
        <strong>ℹ️ Info Confidencial:</strong> Último acesso: {{ $employee->last_login }}
    </div>
@endif
```

#### ✅ Com Alternativa (@cannot)
```blade
@cannot('update', $employee)
    <div class="alert alert-warning">
        ⚠️ Sem permissão para editar este funcionário
    </div>
@else
    <form method="POST" action="{{ route('employee.update', $employee) }}">
        @csrf
        <!-- Form fields -->
        <button type="submit">Guardar</button>
    </form>
@endcannot
```

---

### 1.3 Em Policies (PHP Puro)

#### ✅ Verificação Básica
```php
// app/Policies/EmployeePolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Employee;

class EmployeePolicy extends BasePolicy
{
    /**
     * Admin bypass automático
     */
    public function before(User $user): ?bool
    {
        // Admin consegue fazer qualquer coisa
        if ($this->isAdmin($user)) {
            return true;
        }
        return null; // Continua verificações específicas
    }

    /**
     * ADMIN e HR conseguem visualizar
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    /**
     * ADMIN e HR conseguem ver detalhes
     */
    public function view(User $user, Employee $employee): bool
    {
        return $this->isAdminOrHR($user);
    }

    /**
     * ADMIN consegue criar
     * HR consegue criar
     */
    public function create(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    /**
     * ADMIN consegue editar
     * HR consegue editar
     */
    public function update(User $user, Employee $employee): bool
    {
        return $this->isAdminOrHR($user);
    }

    /**
     * APENAS ADMIN consegue eliminar
     */
    public function delete(User $user, Employee $employee): bool
    {
        // Não usar isAdminOrHR aqui!
        return $this->isAdmin($user);
    }

    /**
     * ADMIN consegue exportar
     * HR consegue exportar
     */
    public function export(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }
}
```

---

## 2. Criar Nova Funcionalidade

### 2.1 Exemplo: Sistema de Benefícios

#### Passo 1: Criar Model & Migration
```bash
php artisan make:model Benefit -m
```

```php
// database/migrations/2026_03_03_create_benefits_table.php
Schema::create('benefits', function (Blueprint $table) {
    $table->id();
    $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
    $table->string('type'); // 'health_insurance', 'dental', etc
    $table->decimal('amount', 10, 2);
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

#### Passo 2: Criar Policy
```php
// app/Policies/BenefitPolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\Benefit;

class BenefitPolicy extends BasePolicy
{
    public function before(User $user): ?bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function view(User $user, Benefit $benefit): bool
    {
        // HR vê benefícios
        if ($this->isAdminOrHR($user)) {
            return true;
        }
        
        // Employee vê apenas seus benefícios
        if ($user->role === 'employee') {
            return $benefit->employee_id === $user->employee_id;
        }
        
        return false;
    }

    public function create(User $user): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function update(User $user, Benefit $benefit): bool
    {
        return $this->isAdminOrHR($user);
    }

    public function delete(User $user, Benefit $benefit): bool
    {
        return $this->isAdmin($user);
    }
}
```

#### Passo 3: Registar Policy
```php
// app/Providers/AppServiceProvider.php
use App\Models\Benefit;
use App\Policies\BenefitPolicy;

public function boot(): void
{
    // ... outras policies
    Gate::policy(Benefit::class, BenefitPolicy::class);
}
```

#### Passo 4: Criar Filament Resource
```php
// app/Filament/Resources/BenefitResource.php
namespace App\Filament\Resources;

use App\Models\Benefit;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class BenefitResource extends Resource
{
    protected static ?string $model = Benefit::class;
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Benefícios';

    public static function canViewAny(): bool
    {
        return Auth::user() && in_array(Auth::user()->role, ['admin', 'hr']);
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('create', Benefit::class) ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->can('update', $record) ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->can('delete', $record) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->relationship('employee', 'full_name')
                ->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'health_insurance' => 'Seguro Saúde',
                    'dental' => 'Seguro Dentário',
                    'life_insurance' => 'Seguro de Vida',
                ])
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->numeric()
                ->required(),
            Forms\Components\DatePicker::make('start_date')
                ->required(),
            Forms\Components\DatePicker::make('end_date'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('start_date'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

#### Passo 5: Adicionar a Painéis
```php
// app/Providers/Filament/AdminPanelProvider.php & HRPanelProvider.php
->resources([
    // ... outros recursos
    BenefitResource::class,  // ✅ Adicionar para Admin
])

// app/Providers/Filament/EmployeePanelProvider.php
// NOT adicionado - Employee não consegue gerir benefícios
```

---

## 3. Casos de Uso Reais

### Caso 1: Employee Submete Licença
```php
// app/Http/Controllers/TimeoffController.php
public function store(StoreTimeoffRequest $request)
{
    // Validar dados
    $validated = $request->validated();
    
    // Adicionar employee_id automaticamente
    $validated['employee_id'] = auth()->user()->employee_id;
    
    // Criar licença
    $timeoff = Timeoff::create($validated);
    
    // Notificar HR
    Mail::send(new PendingTimeoffNotification($timeoff));
    
    return back()->with('success', 'Licença submetida com sucesso');
}
```

### Caso 2: HR Aprova/Rejeita Licença
```php
// app/Http/Controllers/TimeoffController.php
public function approve(Timeoff $timeoff)
{
    // Validar que é HR
    $this->authorize('update', $timeoff);
    
    // Verificar status
    if ($timeoff->status !== 'pending') {
        return back()->withErrors('Licença já foi processada');
    }
    
    // Atualizar
    $timeoff->update([
        'status' => 'approved',
        'approved_by' => auth()->id(),
        'approved_at' => now(),
    ]);
    
    // Notificar Employee
    Mail::send(new TimeoffApprovedNotification($timeoff));
    
    return back()->with('success', 'Licença aprovada');
}
```

### Caso 3: Admin Acessa Todos os Logs
```php
// app/Http/Controllers/AuditController.php
public function index()
{
    // Apenas ADMIN consegue
    if (!auth()->user()->can('view-audit')) {
        abort(403, 'Acesso negado ao log de auditoria');
    }
    
    // Trazer todos os logs
    $logs = Activity::latest()->paginate(50);
    
    return view('admin.audit.index', compact('logs'));
}
```

---

## 4. Tratamento de Erros

### 4.1 Custom Policy Exceptions
```php
// app/Exceptions/PolicyException.php
namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class PolicyException extends Exception
{
    public function render()
    {
        return response()->json(
            ['error' => $this->message],
            Response::HTTP_FORBIDDEN
        );
    }
}
```

### 4.2 Middleware com Logging
```php
// app/Http/Middleware/LogUnauthorizedAccess.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogUnauthorizedAccess
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Log tentativa não autorizada
            Log::warning("Unauthorized access attempt", [
                'user' => auth()->id(),
                'role' => auth()->user()->role,
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->ip(),
            ]);
            
            return response()->json(
                ['error' => 'Sem permissão para aceder este recurso'],
                403
            );
        }
    }
}
```

### 4.3 Blade Helper
```php
<!-- resources/views/components/unauthorized.blade.php -->
<div class="alert alert-danger">
    <h4>🚫 Acesso Negado</h4>
    <p>{{ $message ?? 'Não tem permissão para aceder este recurso.' }}</p>
    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Voltar</a>
</div>
```

---

## 📋 Checklist de Implementação

```
Nova Funcionalidade:
- [ ] Model criado
- [ ] Migration criada e executada
- [ ] Policy criada com regras corretas
- [ ] Policy registada em AppServiceProvider
- [ ] Filament Resource criado (se UI necessária)
- [ ] Resource adicionado aos painéis apropriados
- [ ] Testes criados (unit + feature)
- [ ] Testes passam
- [ ] Código reviewado
- [ ] Documentação atualizada
- [ ] Deploy realizado
- [ ] Testado em produção
```

---

## 🧪 Testes Completos para Nova Funcionalidade

```php
// tests/Feature/BenefitPolicyTest.php
namespace Tests\Feature;

use App\Models\Benefit;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BenefitPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $hr;
    protected User $employee;
    protected Benefit $benefit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->hr = User::factory()->create(['role' => 'hr']);
        $this->employee = User::factory()->create(['role' => 'employee']);
        
        $employee_model = Employee::factory()->create();
        $this->benefit = Benefit::factory()->create(['employee_id' => $employee_model->id]);
    }

    public function test_admin_can_view_benefits()
    {
        $this->assertTrue($this->admin->can('viewAny', Benefit::class));
    }

    public function test_hr_can_view_benefits()
    {
        $this->assertTrue($this->hr->can('viewAny', Benefit::class));
    }

    public function test_employee_cannot_view_all_benefits()
    {
        $this->assertFalse($this->employee->can('viewAny', Benefit::class));
    }

    public function test_admin_can_create_benefit()
    {
        $this->assertTrue($this->admin->can('create', Benefit::class));
    }

    public function test_hr_can_create_benefit()
    {
        $this->assertTrue($this->hr->can('create', Benefit::class));
    }

    public function test_admin_can_delete_benefit()
    {
        $this->assertTrue($this->admin->can('delete', $this->benefit));
    }

    public function test_hr_cannot_delete_benefit()
    {
        $this->assertFalse($this->hr->can('delete', $this->benefit));
    }
}
```

---

**Última Atualização**: 03/03/2026  
**Versão**: 1.0  
**Status**: ✅ Ativo e Testado
