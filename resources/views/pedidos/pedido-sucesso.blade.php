@extends('layouts.app')

@section('content')
    <main style="padding: 150px 10%; text-align: center; min-height: 60vh;">

        @if($pedido->status == 'pago')
            {{-- Ícone e Título para Pagamento Confirmado --}}
            <div style="color: #28a745; font-size: 4rem; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 style="font-family: var(--font-title); font-size: 3rem; color: #28a745;">PAGAMENTO CONFIRMADO!</h1>
            <p style="margin: 20px 0; font-size: 1.1rem;">Tudo certo, <strong>{{ Auth::user()->name }}</strong>! Seu pedido <strong>#{{ $pedido->id }}</strong> já está em processamento.</p>
        @elseif($pedido->status == 'cancelado')
            {{-- Ícone e Título para Erro/Cancelado --}}
            <div style="color: #dc3545; font-size: 4rem; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <h1 style="font-family: var(--font-title); font-size: 3rem; color: #dc3545;">HOUVE UM PROBLEMA</h1>
            <p style="margin: 20px 0; font-size: 1.1rem;">O pagamento do pedido <strong>#{{ $pedido->id }}</strong> não foi aprovado.</p>
        @else
            {{-- Ícone e Título para Pendente (Pix/Boleto ou Processando) --}}
            <div style="color: #ffc107; font-size: 4rem; margin-bottom: 20px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <h1 style="font-family: var(--font-title); font-size: 3rem; color: var(--color-brand);">PEDIDO RECEBIDO</h1>
            <p style="margin: 20px 0; font-size: 1.1rem;">Seu pedido <strong>#{{ $pedido->id }}</strong> foi registrado e aguarda a confirmação do pagamento.</p>
        @endif

        <p style="color: #666;">Você receberá atualizações sobre o status da entrega no seu e-mail.</p>

        <div style="margin-top: 50px; display: flex; gap: 20px; justify-content: center;">
            <a href="{{ route('dashboard') }}" class="btn-primary" style="padding: 15px 30px; text-decoration: none; border: 1px solid var(--color-brand); border-radius: 50px;">ACOMPANHAR PEDIDO</a>
            <a href="{{ route('home') }}" style="padding: 15px 30px; text-decoration: none; color: var(--color-brand); font-weight: 700;">VOLTAR PARA A LOJA</a>
        </div>
    </main>
@endsection
