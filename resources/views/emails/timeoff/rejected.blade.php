<x-mail::message>
# ❌ Seu Pedido de Folga foi Rejeitado

Olá {{ $employee->name }},

Informamos que sua solicitação de {{ $type }} foi **rejeitada**.

<x-mail::panel>
**Detalhes do Período Solicitado:**
- **Data de Início:** {{ $startDate }}
- **Data de Término:** {{ $endDate }}
- **Total de Dias:** {{ $daysCount }} dia(s)
- **Tipo:** {{ $type }}
</x-mail::panel>

@if($reason)
**Motivo da Rejeição:**
{{ $reason }}
@endif

**Próximos Passos:**
1. Acesse seu [painel TeamCore]({{ config('app.url') }}) para ver o feedback detalhado
2. Entre em contato com o Departamento de RH se desejar discutir a decisão
3. Considere enviar um novo pedido com datas alternativas

Se você tiver dúvidas, nossa equipe de RH está à disposição.

Atenciosamente,  
**Sistema TeamCore**

<x-mail::footer>
© {{ date('Y') }} TeamCore HR Management. Todos os direitos reservados.
</x-mail::footer>
</x-mail::message>
