@extends('layouts.app')

@section('title', 'PEDIDO #' . $pedido->id . ' — CASA MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
    <main class="dashboard-container">
        <aside class="dashboard-nav">
            <a href="{{ route('dashboard') }}">RESUMO</a>
            <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
            <a href="{{ route('home') }}">VOLTAR PARA LOJA</a>
        </aside>

        <section class="dashboard-content">
            <div class="order-details-header">
                <h2>DETALHES DO PEDIDO #{{ $pedido->id }}</h2>
                <p>REALIZADO EM {{ $pedido->created_at->format('d/m/Y \À\S H:i') }}</p>

                <span class="status-tag status-{{ $pedido->status }}">
                {{ $pedido->status == 'approved' ? 'PAGO' : 'PENDENTE' }}
            </span>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>DADOS DE ENTREGA</h3>
                    <p>DESTINATÁRIO: {{ $pedido->nome_entrega }}</p>
                    <p>RUA: {{ $pedido->endereco }}</p>
                    <p>CEP: {{ $pedido->cep }}</p>
                </div>
            </div>

            <h3 style="margin: 40px 0 20px; font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase;">ITENS
                DO PEDIDO</h3>

            @foreach($pedido->itens as $item)
                <div class="order-item">
                    <div class="order-item-info">
                        @php
                            $caminho = $item->produto->imagem;
                            $img = str_contains($caminho, 'assets') ? asset(ltrim($caminho, '/')) : asset('storage/' . $caminho);
                        @endphp
                        <img src="{{ $img }}" alt="{{ $item->produto->nome }}">

                        <div class="order-item-text">
                            <p>{{ $item->produto->nome }}</p>
                            <p>QUANTIDADE: {{ $item->quantidade }}</p>
                        </div>
                    </div>
                    <p class="resultado"><strong>R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</strong>
                    </p>
                </div>
            @endforeach

            <div class="order-summary">
                <div class="order-summary-row">
                    <span>SUBTOTAL:</span>
                    <span>R$ {{ number_format($pedido->valor_produtos, 2, ',', '.') }}</span>
                </div>
                <div class="order-summary-row">
                    <span>FRETE:</span>
                    <span>R$ {{ number_format($pedido->valor_frete, 2, ',', '.') }}</span>
                </div>
                <div class="order-summary-total">
                    TOTAL: R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                </div>
            </div>
        </section>
    </main>
@endsection
