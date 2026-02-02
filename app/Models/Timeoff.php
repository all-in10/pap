<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeoff extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'type',
        'category_id',
        'status',
        'reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'hours' => 'float',
    ];

    // Tipos de licença
    public function category()
    {
        return $this->belongsTo(TimeoffCategory::class, 'category_id');
    }

    public const TYPES = [
        // Parentalidade
        'parental_inicial' => ['label' => 'Licença parental inicial', 'category' => 'parentalidade'],
        'parental_mae' => ['label' => 'Licença parental exclusiva da mãe', 'category' => 'parentalidade'],
        'parental_pai' => ['label' => 'Licença parental exclusiva do pai', 'category' => 'parentalidade'],
        'parental_alargada' => ['label' => 'Licença parental alargada', 'category' => 'parentalidade'],
        'adocao' => ['label' => 'Licença por adoção', 'category' => 'parentalidade'],
        'risco_clinico' => ['label' => 'Licença por risco clínico na gravidez', 'category' => 'parentalidade'],
        'interrupcao_gravidez' => ['label' => 'Licença por interrupção da gravidez', 'category' => 'parentalidade'],
        // Saúde
        'doenca' => ['label' => 'Licença por doença (baixa médica)', 'category' => 'saude'],
        'acidente_trabalho' => ['label' => 'Licença por acidente de trabalho', 'category' => 'saude'],
        'doenca_profissional' => ['label' => 'Licença por doença profissional', 'category' => 'saude'],
        // Família
        'assistencia_filho' => ['label' => 'Assistência a filho', 'category' => 'familia'],
        'assistencia_filho_deficiencia' => ['label' => 'Assistência a filho com deficiência/doença crónica', 'category' => 'familia'],
        'assistencia_neto' => ['label' => 'Assistência a neto', 'category' => 'familia'],
        'assistencia_agregado' => ['label' => 'Assistência a membro do agregado familiar', 'category' => 'familia'],
        // Formação e Vida Pessoal
        'frequencia_ensino' => ['label' => 'Licença para frequência de ensino', 'category' => 'formacao'],
        'sem_retribuicao' => ['label' => 'Licença sem retribuição', 'category' => 'formacao'],
        'funcoes_sindicais' => ['label' => 'Licença para funções sindicais', 'category' => 'formacao'],
        // Outras
        'obrigacoes_legais' => ['label' => 'Cumprimento de obrigações legais', 'category' => 'outras'],
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
