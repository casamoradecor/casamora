@extends('layouts.app')

@section('title', 'Minha Conta — Casa MORÁ')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-container">
        <aside class="dashboard-nav">
            <a href="{{ route('dashboard') }}" style="text-decoration: underline;">RESUMO</a>
            <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
            <a href="{{ route('enderecos.index') }}">ENDEREÇOS</a>
            <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout" style="font-family: var(--font-base),sans-serif">SAIR DA CONTA</button>
            </form>
        </aside>

        <section class="dashboard-content">
            <h2 style="font-family: var(--font-title),serif" ">OLÁ, {{ explode(' ', Auth::user()->name)[0] }}</h2>

            <p style="margin-bottom: 40px; color: #555; font-size: 0.95rem; font-family: var(--font-base), sans-serif">
                A partir do painel de controle de sua conta, você pode ver seus pedidos recentes, gerenciar seus
                endereços de entrega e faturamento e editar sua senha e detalhes da conta.
            </p>

            <div class="info-grid">
                <div class="info-card">
                    <h3>DADOS PESSOAIS</h3>
                    <p>{{ Auth::user()->name }}</p>
                    <p>{{ Auth::user()->email }}</p>
                    <p>CPF: {{ Auth::user()->cpf ?? 'Não informado' }}</p>
                    <a href="{{ route('perfil.edit') }}"
                       style="color: #4B3621; font-size: 0.75rem; margin-top: 15px; display: block; font-weight: 700; text-transform: uppercase;">Editar
                        Dados</a>
                </div>

                <div class="info-card">
                    <h3>PEDIDO RECENTE</h3>

                    @if($ultimoPedido)
                        <div class="card-content">
                            <p><strong>#{{ $ultimoPedido->codigo_externo }}</strong></p>

                            {{-- Data formatada corretamente: 18/03/2026 --}}
                            <p class="card-date">DATA: {{ $ultimoPedido->created_at->format('d/m/Y') }}</p>

                            <p class="card-total">TOTAL:
                                <strong>R$ {{ number_format($ultimoPedido->valor_total, 2, ',', '.') }}</strong></p>

                            {{-- Status estilizado como TAG --}}

                            <span class="status-tag {{ $ultimoPedido->status == 'pago' ? 'status-pago' : 'status-pendente' }}">
                                {{ strtoupper($ultimoPedido->status) }}
                            </span>
                        </div>

                        <a href="{{ route('pedidos.show', $ultimoPedido->id) }}" class="btn-card-action">
                            VER DETALHES
                        </a>
                    @else
                        <p>VOCÊ AINDA NÃO REALIZOU NENHUM PEDIDO.</p>
                        <a href="{{ route('home') }}" class="btn-card-action">
                            VER PRODUTOS
                        </a>
                    @endif
                </div>
            </div>
        </section>
    </main>
@endsection
