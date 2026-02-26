@extends('layouts.app')

@section('content')
<main style="padding: 150px 10%; text-align: center;">
    <h1 style="font-family: var(--font-title); font-size: 3rem; color: var(--color-brand);">OBRIGADO!</h1>
    <p style="margin: 20px 0; font-size: 1.1rem;">Seu pedido <strong>#{{ $pedidoId }}</strong> foi recebido com sucesso.</p>
    <p>Você receberá um e-mail com os detalhes da sua compra em breve.</p>
    
    <div style="margin-top: 50px;">
        <a href="{{ route('dashboard') }}" class="btn-primary" style="padding: 15px 30px; text-decoration: none;">ACOMPANHAR MEUS PEDIDOS</a>
    </div>
</main>
@endsection