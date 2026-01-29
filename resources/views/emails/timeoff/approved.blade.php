<x-mail::message>
# ✅ Seu Pedido de Folga foi Aprovado!

Olá {{ $employee->name }},

Bom dia! Sua solicitação de {{ $type }} foi **aprovada com sucesso**.

<x-mail::panel>
**Detalhes do Período:**
- **Data de Início:** {{ $startDate }}
- **Data de Término:** {{ $endDate }}
- **Total de Dias:** {{ $daysCount }} dia(s)
- **Tipo:** {{ $type }}
</x-mail::panel>

@if($approver)
**Aprovado por:** {{ $approver->name }}
@endif

Aproveite bem o descanso! Se você tiver dúvidas ou precisa resgatar informações sobre seus dias restantes, acesse seu painel no [TeamCore]({{ config('app.url') }}).

Qualquer dúvida, não hesite em contatar o Departamento de RH.

Atenciosamente,  
**Sistema TeamCore**

<x-mail::footer>
© {{ date('Y') }} TeamCore HR Management. Todos os direitos reservados.
</x-mail::footer>
</x-mail::message>
