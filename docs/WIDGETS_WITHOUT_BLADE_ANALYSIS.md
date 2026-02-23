# Análise: Widgets sem Blade usando Filament Nativamente

## 🎯 Estratégia

Existem 3 formas de criar widgets Filament SEM arquivo Blade:

### 1. **StatsOverviewWidget** ✅ (Já implementado)
- Melhor para: Métricas simples, números, informações resumidas
- Exemplo: `GeneralStats.php`, `DepartmentStatsWidget.php`
- Retorna array de `Stat::make()`

### 2. **ChartWidget** (Recomendado para gráficos)
- Melhor para: Gráficos (barras, pizza, linha)
- Requer: `doctrine/dbal` (já deve ter)
- Layout puro PHP

### 3. **Custom Widget com renderização HTML** (Fallback)
- Melhor para: Tabelas, layouts customizados
- Usa: `render()` método que retorna HTML string ou Blade inline
- Ou implementar interface `Renderable`

### 4. **Filament Tables** (Melhor opção para dados tabulares)
- Melhor para: Listagens, tabelas, dados estruturados
- Nativo do Filament, sem precisar de arquivo Blade
- Integra Actions, Bulk Actions, Search, Filter

---

## 📊 Comparação de Widgets Atuais

| Widget | Tipo Atual | Tipo Melhor | Razão |
|--------|-----------|-----------|-------|
| `EmployeeDirectoryWidget` | Widget + Blade | TableWidget | Melhor para listagens |
| `PendingTimeoffsWidget` | Widget + Blade | TableWidget | Melhor para listagens |
| `ContractExpirationAlertWidget` | Widget + Blade | StatsOverviewWidget + TableWidget | Híbrido |
| `MyTimeoffHistoryWidget` | Widget + Blade | TableWidget | Melhor para histórico |
| `HourBankDetailWidget` | Widget + Blade | StatsOverviewWidget | Síntese é simples |

---

## 🔧 Implementação: Widgets sem Blade

### Opção A: Usar StatsOverviewWidget (Mais Simples)

```php
// Widget totalmente em PHP
class HourBankDetailWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $employee = $user->employee;
        $balance = $employee->hourbanks()->latest()->first();
        
        return [
            Stat::make('Saldo de Horas', number_format($balance->balance_hours, 2) . 'h')
                ->icon('heroicon-o-clock')
                ->color($balance->balance_hours > 0 ? 'success' : 'danger'),
        ];
    }
}
```

### Opção B: Usar Filament TableWidget (Recomendado para dados)

Exemplo com Filament 3.x - TableWidget nativo:

```php
use Filament\Widgets\TableWidget as BaseTableWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class PendingTimeoffsWidget extends BaseTableWidget
{
    protected static ?int $sort = 2;
    
    protected function getTableQuery()
    {
        return Timeoff::where('status', 'pending')
            ->with(['employee', 'category'])
            ->orderBy('created_at', 'desc');
    }
    
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('employee.first_name')
                ->label('Colaborador')
                ->searchable(),
            TextColumn::make('category.name')
                ->label('Categoria'),
            TextColumn::make('start_date')
                ->label('Início')
                ->date(),
            BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'approved',
                    'danger' => 'rejected',
                ]),
        ];
    }
    
    protected function getTableHeaderActions(): array
    {
        return [];
    }
}
```

### Opção C: Render HTML inline (Sem arquivo Blade)

```php
use Filament\Widgets\Widget;
use Illuminate\Contracts\Support\Htmlable;

class MyCustomWidget extends Widget
{
    public function render(): Htmlable|string
    {
        $data = $this->getData();
        
        return <<<HTML
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="text-lg font-bold">Título</h3>
            <p>{$data['info']}</p>
        </div>
        HTML;
    }
    
    private function getData()
    {
        return [
            'info' => 'Dados aqui'
        ];
    }
}
```

---

## 🚀 Migração Recomendada

### Fase 1: Widgets Simples (StatsOverviewWidget)
- ✅ DepartmentStatsWidget
- ✅ ContractOverviewWidget
- ✅ AttendanceOverviewWidget
- ✅ MyAttendanceWidget
- ✅ HourBankDetailWidget (converter)

### Fase 2: Widgets com Dados (TableWidget)
- ❌ EmployeeDirectoryWidget → TableWidget
- ❌ PendingTimeoffsWidget → TableWidget
- ❌ MyTimeoffHistoryWidget → TableWidget
- ❌ ContractExpirationAlertWidget → StatsOverviewWidget + TableWidget

### Fase 3: Widgets Avançados (ChartWidget)
- Gráficos de departamentos
- Gráficos de presença
- Análises de benefícios

---

## 📦 Dependências Necessárias

Para TableWidget (tabelas nativas):
```bash
composer show | grep filament
# Já deve ter filament/tables instalado
```

Para ChartWidget (gráficos):
```bash
composer require filament/charts
# Ou já pode estar instalado
```

---

## ✨ Benefícios da Conversão

| Aspecto | Blade | Sem Blade |
|--------|-------|----------|
| Arquivo(.php) | 1 + 1 Blade | Apenas 1 .php |
| Manutenção | Maior | Menor |
| Reatividade | Limitada | Melhor com Livewire |
| Type Safety | Menos | Mais |
| Performance | Renderização | Direto em PHP |
| Testes | Difícil (Blade) | Fácil (PHP) |

---

## 📝 Próximos Passos

1. Analisar o Filament version do projeto
2. Implementar TableWidget para EmployeeDirectoryWidget
3. Implementar TableWidget para PendingTimeoffsWidget
4. Converter HourBankDetailWidget para StatsOverviewWidget puro
5. Remover arquivos Blade desnecessários

