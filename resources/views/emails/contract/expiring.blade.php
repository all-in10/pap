<x-mail::message>
# ⚠️ Aviso: Seu Contrato Está Expirando

Olá {{ $employee->name }},

Este é um aviso importante sobre seu contrato de trabalho.

<x-mail::panel>
**Informações do Contrato:**
- **Tipo de Contrato:** {{ $contractType }}
- **Data de Término:** {{ $expiryDate }}
- **Dias Restantes:** {{ $daysUntilExpiration }} dia(s)
- **Status Atual:** {{ $status }}
</x-mail::panel>

## ⏰ Ação Recomendada

Por favor, entre em contato com o Departamento de RH para:
- Discussão de renovação contratual
- Ajustes de termos e condições
- Agendamento de reunião de revisão

**Contato de RH:**
- Email: hr@teamcore.local
- Telefone: +55 (XX) XXXX-XXXX

Acesse seu [painel TeamCore]({{ config('app.url') }}) para mais detalhes sobre seu contrato e histórico.

Atenciosamente,  
**Sistema TeamCore**

<x-mail::footer>
© {{ date('Y') }} TeamCore HR Management. Todos os direitos reservados.
</x-mail::footer>
</x-mail::message>
