@extends('layouts.app')

@section('title', 'Gestão de Envio - Pedido #' . $pedido->id . ' — Casa MORÁ')
@section('header_class', 'admin-hidden')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendas-confirmadas.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

@endpush

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')
        <main class="admin-main">
            <header class="page-header">
                <div>
                    <a href="{{ route('admin.vendas.index') }}" class="btn-back">
                        <i class="fa-solid fa-arrow-left"></i> Voltar para Vendas
                    </a>
                    <h1 style="font-weight: 700; color: #000; font-size: 2rem;">Pedido #{{ $pedido->id }}</h1>
                    <p style="color: #888;">Gerencie os detalhes e realize o despacho da mercadoria.</p>
                </div>
                <div style="text-align: right;">
                    <span class="status-badge status-pago" style="padding: 10px 20px; font-size: 0.8rem;">PAGAMENTO CONFIRMADO</span>
                </div>
            </header>

            <div class="info-grid">
                <div class="details-card">
                    <h3>Dados de Entrega</h3>
                    <p><strong>Destinatário:</strong> {{ $pedido->nome_entrega }}</p>
                    <p><strong>Endereço:</strong> {{ $pedido->endereco }}</p>
                    <p><strong>CEP:</strong> {{ $pedido->cep }}</p>
                    <p><strong>CPF:</strong> {{ $pedido->cpf_entrega ?? 'Não informado' }}</p>
                    <p><strong>E-mail Cliente:</strong> {{ $pedido->cliente?->email }}</p>
                </div>

                <div class="details-card">
                    <h3>Resumo Financeiro</h3>
                    <p style="text-transform: uppercase"><strong>Subtotal:</strong> R$ {{ number_format($pedido->valor_produtos, 2, ',', '.') }}</p>
                    <p style="text-transform: uppercase"><strong>Frete:</strong> R$ {{ number_format($pedido->valor_frete, 2, ',', '.') }}</p>
                    @if($pedido->valor_desconto > 0)
                        <p style="color: #A63D40;"><strong>Desconto:</strong> - R$ {{ number_format($pedido->valor_desconto, 2, ',', '.') }}</p>
                    @endif
                    <p style="font-size: 1rem; margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; text-transform: uppercase">
                        <strong>Total Pago:</strong> R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="items-section">
                <h3 style="font-size: 0.75rem; letter-spacing: 2px; color: #888; text-transform: uppercase; margin-bottom: 20px;">Itens do Pedido</h3>
                <table class="items-table">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th style="text-align: center;">Qtd</th>
                        <th>Preço Unitário</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedido->itens as $item)
                        <tr>
                            <td>
                                <div class="product-info">
                                    @php
                                        $img = $item->produto->imagem ? (str_contains($item->produto->imagem, 'assets') ? asset($item->produto->imagem) : asset('storage/' . $item->produto->imagem)) : asset('assets/placeholder.png');
                                    @endphp
                                    <img src="{{ $img }}" class="product-img" alt="{{ $item->produto->nome }}">
                                    <div>
                                        <span style="font-weight: 600; color: #000; display: block;">{{ $item->produto->nome }}</span>
                                        <span style="font-size: 0.75rem; color: #888;">REF: {{ str_pad($item->produto->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $item->quantidade }}</td>
                            <td style="text-transform: uppercase">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700; color: #000; text-transform: uppercase">R$ {{ number_format($item->preco_unitario * $item->quantidade, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="dispatch-panel">
                <div class="dispatch-info">
                    <h4>Pronto para despachar?</h4>
                    <p>Insira o código de rastreamento para notificar o cliente.</p>
                </div>
                <form action="{{ route('admin.pedidos.enviar', $pedido->id) }}" method="POST" class="dispatch-form">
                    @csrf
                    <input type="text" name="codigo_rastreio" class="dispatch-input" placeholder="Ex: BR123456789AA" required>
                    <button type="submit" class="btn-confirm-envio">Confirmar Envio</button>
                </form>
            </div>
        </main>
    </div>
@endsection
