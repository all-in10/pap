<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Contrato de Trabalho</title>
<style>
    @font-face {
        font-family: 'DejaVu Sans';
        src: url("{{ storage_path('fonts/DejaVuSans.ttf') }}") format("truetype");
    }
    body { font-family: 'DejaVu Sans', sans-serif; line-height: 1.4; }
    h1 { text-align: center; margin-bottom: 30px; }
    .section { margin-bottom: 20px; }
    .section h2 { font-size: 16px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid #000; }
    th, td { padding: 8px; text-align: left; }
</style>
</head>
<body>
<h1>Contrato de Trabalho</h1>

<div class="section">
    <h2>Informações do Funcionário</h2>
    <p><strong>Nome:</strong> {{ $contract->employee->first_name }} {{ $contract->employee->last_name }}</p>
    <p><strong>Cargo / Designação:</strong> {{ $contract->designation?->name ?? '-' }}</p>
    <p><strong>Data de Contratação:</strong> {{ $contract->date_hired->format('d/m/Y') }}</p>
</div>

<div class="section">
    <h2>Detalhes do Contrato</h2>
    <table>
        <tr>
            <th>Tipo de Contrato</th>
            <td>{{ $contract->contractType?->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Data de Início</th>
            <td>{{ $contract->start_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Data de Fim</th>
            <td>{{ $contract->end_date?->format('d/m/Y') ?? '-' }}</td>
        </tr>
        <tr>
            <th>Salário</th>
            <td>€{{ number_format($contract->salary, 2) }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>{{ ucfirst($contract->status) }}</td>
        </tr>
    </table>
</div>

<div class="section">
    <h2>Cláusulas do Contrato</h2>
    <p>O funcionário deverá desempenhar suas funções de acordo com a designação atribuída e em conformidade com as normas da empresa...</p>
    <p>Este contrato é válido enquanto o funcionário estiver ativo ou até sua data de término, se aplicável.</p>
</div>

<div class="section">
    <p>Assinaturas:</p>
    <p>__________________________________</p>
    <p>{{ $contract->employee->first_name }} {{ $contract->employee->last_name }}</p>

    <p>__________________________________</p>
    <p>Representante da Empresa</p>
</div>
</body>
</html>
