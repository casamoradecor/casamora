@extends('layouts.app')

@section('title', 'Pedido #' . $pedido->id . ' — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .order-details-header { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .order-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #f9f9f9; }
        .order-item img { width: 60px; height: 80px; object-fit: cover; }
        .status-badge { background: #000; color: #fff; padding: 5px 10px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
    </style>
@endpush

@section('content')
<main class="dashboard-container">
    <aside class="dashboard-nav">
        <a href="{{ route('dashboard') }}">RESUMO</a>
        <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
        <a href="/">VOLTAR PARA LOJA</a>
    </aside>

    <section class="dashboard-content">
        <div class="order-details-header">
            <h2>DETALHES DO PEDIDO #{{ $pedido->id }}</h2>
            <p>Realizado em {{ $pedido->created_at->format('d/m/Y \à\s H:i') }}</p>
            <span class="status-badge">{{ $pedido->status }}</span>
        </div>

        <div class="info-grid">
            <div class="info-card">
                <h3>ENTREGAR EM</h3>
                <p>{{ $pedido->nome_entrega }}</p>
                <p>{{ $pedido->endereco }}</p>
                <p>CEP: {{ $pedido->cep }}</p>
            </div>
        </div>

        <h3 style="margin: 40px 0 20px; font-size: 0.8rem; letter-spacing: 1px;">ITENS DO PEDIDO</h3>
        
        @foreach($pedido->itens as $item)
            <div class="order-item">
                <div style="display: flex; gap: 20px; align-items: center;">
                    @php 
                        $caminho = $item->produto->imagem;
                        $img = str_contains($caminho, 'assets') ? asset(ltrim($caminho, '/')) : asset('storage/' . $caminho);
                    @endphp
                    <img src="{{ $img }}" alt="{{ $item->produto->nome }}">
                    
                    <div>
                        <p style="font-weight: 700;">{{ $item->produto->nome }}</p>
                        <p style="font-size: 0.8rem; color: #666;">Quantidade: {{ $item->quantidade }}</p>
                    </div>
                </div>
                <p style="font-weight: 700;">R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</p>
            </div>
        @endforeach

        <div style="margin-top: 30px; text-align: right;">
            <p style="font-size: 1.2rem; font-weight: 700;">TOTAL: R$ {{ number_format($pedido->total, 2, ',', '.') }}</p>
        </div>
    </section>
</main>
@endsection