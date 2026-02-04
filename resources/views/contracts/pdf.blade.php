<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrato #{{ $contract->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 10px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 6px; }
        .footer { margin-top: 30px; font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Contrato de Trabalho</h2>
        <div>Contrato #{{ $contract->id }}</div>
    </div>

    <div class="section">
        <div><span class="label">Funcionário:</span> {{ $contract->employee?->first_name ?? '-' }} {{ $contract->employee?->last_name ?? '' }}</div>
        <div><span class="label">Tipo de Contrato:</span> {{ $contract->contractType?->label ?? '-' }}</div>
        <div><span class="label">Salário:</span> €{{ number_format($contract->salary, 2, ',', '.') }}</div>
    </div>

    <div class="section">
        <div><span class="label">Início:</span> {{ optional($contract->start_date)?->format('d/m/Y') ?? '-' }}</div>
        <div><span class="label">Fim:</span> {{ optional($contract->end_date)?->format('d/m/Y') ?? '-' }}</div>
        <div><span class="label">Data de Contratação:</span> {{ optional($contract->date_hired)?->format('d/m/Y') ?? '-' }}</div>
        <div><span class="label">Status:</span> {{ ucfirst($contract->status) }}</div>
    </div>

    <div class="section">
        <h4>Cláusulas</h4>
        <p>Este contrato é gerado automaticamente e serve apenas como demonstração. Para documentação oficial, consulte o departamento de Recursos Humanos.</p>
    </div>

    <div class="footer">
        Gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
