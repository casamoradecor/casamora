@extends('layouts.app')

@section('title', 'Status do Pedido — Casa MORÁ')

@push('css')
    {{-- Importando Poppins se não estiver no seu layout global --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        .status-container {
            padding: 120px 10%;
            text-align: center;
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }

        .status-icon {
            font-size: 5rem;
            margin-bottom: 25px;
            animation: scaleIn 0.5s ease-out;
        }

        /* Cores e Sombras baseadas no Status */
        .icon-pago { color: #5B8C5A; }      /* Verde mais fechado/elegante */
        .icon-cancelado { color: #A63D40; } /* Vermelho sofisticado */
        .icon-pendente { color: #D4AF37; }  /* Dourado/Amarelo estético */

        .status-title {
            font-weight: 700;
            font-size: 2.5rem;
            letter-spacing: -1px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .status-message {
            color: #444;
            font-size: 1.1rem;
            max-width: 600px;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .status-order-id {
            color: var(--color-brand, #4B3621);
            font-weight: 700;
        }

        .status-subtext {
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 40px;
        }

        .status-actions {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .btn-status {
            padding: 16px 35px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 4px; /* Estilo MORÁ: cantos menos arredondados são mais premium */
            transition: all 0.3s ease;
        }

        .btn-primary-mora {
            background-color: var(--color-brand, #4B3621);
            color: #fff;
            border: 1px solid var(--color-brand, #4B3621);
        }

        .btn-primary-mora:hover {
            background-color: transparent;
            color: var(--color-brand, #4B3621);
        }

        .btn-outline-mora {
            color: var(--color-brand, #4B3621);
            border: 1px solid #ddd;
        }

        .btn-outline-mora:hover {
            border-color: var(--color-brand, #4B3621);
        }

        @keyframes scaleIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .status-container { padding: 100px 5%; }
            .status-title { font-size: 1.8rem; }
            .status-actions { flex-direction: column; width: 100%; }
        }
    </style>
@endpush

@section('content')
    <main class="status-container">

        @if($pedido->status == 'pago')
            <div class="status-icon icon-pago">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="status-title" style="color: #5B8C5A;">Pagamento Confirmado</h1>
            <p class="status-message">
                Tudo pronto, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong>!
                Seu pedido <span class="status-order-id">#{{ $pedido->id }}</span> já foi confirmado e nossa equipe começou a prepará-lo com todo carinho.
            </p>
        @elseif($pedido->status == 'cancelado')
            <div class="status-icon icon-cancelado">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <h1 class="status-title" style="color: #A63D40;">Ops! Algo deu errado</h1>
            <p class="status-message">
                O pagamento do pedido <span class="status-order-id">#{{ $pedido->id }}</span> não pôde ser processado.
                Por favor, verifique os dados do cartão ou tente outro método de pagamento.
            </p>
        @else
            <div class="status-icon icon-pendente">
                <i class="fa-solid fa-clock"></i>
            </div>
            <h1 class="status-title" style="color: #D4AF37;">Pedido em Análise</h1>
            <p class="status-message">
                Recebemos seu pedido <span class="status-order-id">#{{ $pedido->id }}</span>!
                Estamos aguardando a confirmação do pagamento pelo Mercado Pago para dar continuidade.
            </p>
        @endif

        <p class="status-subtext">Você receberá atualizações detalhadas no e-mail <strong>{{ Auth::user()->email }}</strong>.</p>

        <div class="status-actions">
            <a href="{{ route('dashboard') }}" class="btn-status btn-primary-mora">Acompanhar Pedido</a>
            <a href="{{ route('home') }}" class="btn-status btn-outline-mora">Voltar para a Loja</a>
        </div>
    </main>
@endsection
