@component('mail::message')
    # Olá, {{ explode(' ', $pedido->cliente->name)[0] }}! ✨

    Boas notícias! O seu pedido **#{{ $pedido->id }}** da **Casa MORÁ** acaba de ser despachado e já está com a transportadora.

    **Seu Código de Rastreamento:**
    @component('mail::panel')
        ## {{ $pedido->codigo_rastreio }}
    @endcomponent

    Agora você pode acompanhar cada passo da sua nova decoração até a sua casa.

    @component('mail::button', ['url' => url('/meus-pedidos'), 'color' => 'success'])
        Acompanhar meu Pedido
    @endcomponent

    Agradecemos por escolher a Casa MORÁ para fazer parte do seu lar.

    Atenciosamente,
    **Equipe Casa MORÁ**
@endcomponent
