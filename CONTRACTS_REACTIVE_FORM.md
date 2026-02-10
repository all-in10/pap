# ✅ Contratos com Datas Reativas Implementado

## 🎯 O Que Foi Realizado

Implementei o sistema reativo para mostrar/esconder campo de **Data de Fim** baseado no **Tipo de Contrato** selecionado.

---

## 📊 Tipos de Contrato com Data de Fim

Os seguintes tipos de contrato REQUEREM **Data de Fim** obrigatória:

| Tipo | Nome BD | Descrição |
|------|---------|-----------|
| 🔵 **Temporário** | `temporario` | Contrato de trabalho temporário |
| 🟠 **Tempo Parcial** | `tempo_parcial` | Contrato de trabalho a tempo parcial |
| 🟡 **Estágio** | `estagio` | Contrato de estágio |
| 🟢 **Termo Certo** | `termo_certo` | Contrato a termo certo |
| 🟣 **Termo Incerto** | `termo_incerto` | Contrato a termo incerto |

Os seguintes tipos NÃO requerem Data de Fim:

| Tipo | Nome BD | Descrição |
|------|---------|-----------|
| ⚪ **Sem Termo** | `sem_termo` | Contrato sem termo |
| 🟤 **Prestação de Serviços** | `prestacao_servicos` | Recibos verdes |

---

## 🔧 Implementação Técnica

### 1️⃣ **ContractType Model** - Métodos Helpers

Adicionei dois métodos static no modelo `ContractType`:

```php
/**
 * Verifica se este tipo de contrato requer data de fim
 */
public function requiresEndDate(): bool
{
    return in_array($this->name, [
        'temporario', 
        'tempo_parcial', 
        'estagio', 
        'termo_certo', 
        'termo_incerto'
    ]);
}

/**
 * Obtém IDs dos tipos que requerem data de fim
 */
public static function requiresEndDateIds(): array
{
    return static::whereIn('name', [
        'temporario', 'tempo_parcial', 'estagio', 
        'termo_certo', 'termo_incerto'
    ])->pluck('id')->toArray();
}
```

### 2️⃣ **Contract Model** - Métodos de Validação

Adicionei métodos com validações no modelo `Contract`:

```php
/**
 * Verifica se este contrato requer data de fim
 */
public function requiresEndDate(): bool
{
    return $this->contractType?->requiresEndDate() ?? false;
}

/**
 * Verifica se a data de fim é válida (se necessária)
 */
public function hasValidEndDate(): bool
{
    if (!$this->requiresEndDate()) {
        return true;
    }
    return $this->end_date !== null && $this->end_date > $this->start_date;
}
```

### 3️⃣ **ContractResource Form** - Reatividade

#### Before (Antigo)
```php
->visible(fn(callable $get) =>
    in_array($get('contract_type_id'), [
        \App\Models\ContractType::firstWhere('name', 'temporary')->id ?? -1,
        \App\Models\ContractType::firstWhere('name', 'internship')->id ?? -1,
    ])
)
```

#### After (Novo - Robusto)
```php
Forms\Components\DatePicker::make('contract_type_id')
    ->required()
    ->reactive()
    ->live()  // Atualização em tempo real
    ->searchable()
    ->preload(),

Forms\Components\DatePicker::make('end_date')
    ->label('Data de Fim')
    ->visible(
        fn(callable $get) => 
        in_array(
            $get('contract_type_id'),
            \App\Models\ContractType::requiresEndDateIds()  // ← Usa helper
        )
    )
    ->required(
        fn(callable $get) => 
        in_array(
            $get('contract_type_id'),
            \App\Models\ContractType::requiresEndDateIds()  // ← Usa helper
        )
    )
    ->rules([
        'nullable',
        'date_format:Y-m-d',
        function (callable $get) {
            return function ($attribute, $value, $fail) use ($get) {
                $startDate = $get('start_date');
                if ($startDate && $value && $value <= $startDate) {
                    $fail('A data de fim deve ser posterior à data de início.');
                }
            };
        },
    ])
    ->helperText('Obrigatório para contratos temporários, tempo parcial e estágios.')
    ->nullable(),
```

---

## 🎮 Comportamento da UI

### Ao Selecionar Um Tipo de Contrato:

```
┌─────────────────────────────────────────┐
│ Tipo de Contrato: [SEM TERMO      ▼]   │
├─────────────────────────────────────────┤
│ Data de Início:    [____/____/______]   │
│ Data de Fim:       [OCULTO - não precisa]│
│ Status:            [ATIVO      ▼]       │
└─────────────────────────────────────────┘

        ↓ (seleciona "temporario")

┌─────────────────────────────────────────┐
│ Tipo de Contrato: [TEMPORÁRIO     ▼]    │
├─────────────────────────────────────────┤
│ Data de Início:    [____/____/______]   │ required
│ Data de Fim:       [____/____/______] * │ required (visível + obrigatório)
│ Status:            [ATIVO      ▼]       │
└─────────────────────────────────────────┘

        Se Data Fim <= Data Início:
        ⚠️ "A data de fim deve ser posterior à data de início."
```

---

## 📋 Campos do Formulário

| Campo | Tipo | Sempre Visível? | Obrigatório? | Notas |
|-------|------|-----------------|--------------|-------|
| **Funcionário** | Select | ✅ Sim | ❌ Não | Pode ser nulo |
| **Tipo Contrato** | Select | ✅ Sim | ✅ Sim | Ativa reatividade |
| **Salário** | TextInput | ✅ Sim | ✅ Sim | Num\|erico |
| **Data Início** | DatePicker | ✅ Sim | ✅ Sim | Sempre obrigatório |
| **Data Fim** | DatePicker | 🔄 **Reativa** | 🔄 **Reativa** | Apenas temporários/tempo parcial/estágios |
| **Status** | Select | ✅ Sim | ✅ Sim | Ativo/Encerrado/Suspenso |
| **Data de Contratação** | DatePicker | ✅ Sim | ✅ Sim | Data de contarição |

---

## ✅ Tipos que Ativam "Data de Fim" Obrigatória

```php
[
    'temporario'          → visible: ✅ required: ✅
    'tempo_parcial'       → visible: ✅ required: ✅
    'estagio'             → visible: ✅ required: ✅
    'termo_certo'         → visible: ✅ required: ✅
    'termo_incerto'       → visible: ✅ required: ✅
    'sem_termo'           → visible: ❌ required: ❌
    'prestacao_servicos'  → visible: ❌ required: ❌
]
```

---

## 🔍 Validações Implementadas

### Lado Servidor (Filament Rules):

1. ✅ **Formato de Data** - `date_format:Y-m-d`
2. ✅ **Data de Fim > Data de Início** - Validação customizada
3. ✅ **Campo Obrigatório** - Para tipos específicos
4. ✅ **Nullable** - Quando não visível

### Lado Cliente (Reatividade):

1. ✅ **Visibilidade Dinâmica** - Campo aparece/desaparece conforme tipo
2. ✅ **Obrigatoriedade Dinâmica** - Campo required/not required conforme tipo
3. ✅ **Live Updates** - `.live()` atualiza em tempo real

---

## 📊 Exemplo de Fluxo

### Criar Contrato Temporário ✅

```
1. Selecionar Funcionário: João Silva
2. Salário: 1500.00
3. Tipo Contrato: TEMPORÁRIO ← Campo Data Fim aparece
4. Data Início: 01/02/2026
5. Data Fim: 30/04/2026 ← Obrigatório (agora visível)
6. Status: Ativo
7. Data Conthatação: 01/02/2026

✅ SUBMITIR → Contrato criado com datas
```

### Criar Contrato Sem Termo ✅

```
1. Selecionar Funcionário: Maria Silva
2. Salário: 2000.00
3. Tipo Contrato: SEM TERMO ← Campo Data Fim NÃO aparece
4. Data Início: 10/02/2026
5. [Data Fim - OCULTA]
6. Status: Ativo
7. Data Contratação: 10/02/2026

✅ SUBMIT → Contrato criado sem data de fim
```

---

## 🛠️ Melhorias Futuras

- [ ] Adicionar duração automática (dias/meses entre datas)
- [ ] Renovação automática para contratos
- [ ] Alert quando contrato está próximo do vencimento
- [ ] Cálculo de tempo restante no dashboard
- [ ] Template de contrato por tipo
- [ ] Integração com assinatura digital

---

## 📝 Arquivos Modificados

```
✅ app/Models/ContractType.php
   - Adicionado requiresEndDate()
   - Adicionado requiresEndDateIds()

✅ app/Models/Contract.php
   - Adicionado requiresEndDate()
   - Adicionado getContractTypeLabel()
   - Adicionado hasValidEndDate()

✅ app/Filament/Resources/ContractResource.php
   - Melhorado select de contract_type_id com reactive() e live()
   - Melhorado DatePicker de end_date com visible/required reato
   - Adicionadas validações customizadas
   - Texto de helper atualizado
```

---

## 🎓 Como Usar

### No Controller/Model:

```php
$contract = Contract::find($id);

// Verificar se precisa de end_date
if ($contract->requiresEndDate()) {
    echo "Este contrato requer data de fim";
}

// Verificar se as datas são válidas
if ($contract->hasValidEndDate()) {
    echo "Datas estão OK";
}
```

### No Template/Blade:

```blade
@if($contract->requiresEndDate())
    <p>Data de Fim: {{ $contract->end_date->format('d/m/Y') }}</p>
@endif
```

---

## 🚀 Status

✅ **IMPLEMENTADO E PRONTO PARA USAR!**

Campo "Data de Fim" agora aparece reativa baseado no tipo de contrato selecionado.
