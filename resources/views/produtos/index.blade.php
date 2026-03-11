@extends('layouts.app')

@section('title', 'Todos os Produtos — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('content')
    <header class="shop-header">

    </header>

    <main class="products-grid">
        @foreach($produtos as $produto)
            @php
                $caminho = $produto->imagem;
                if ($caminho && str_contains($caminho, 'assets')) {
                    $urlFinal = asset(ltrim($caminho, '/'));
                } else {
                    $urlFinal = $caminho ? Storage::url($caminho) : asset('assets/vasomora.png');
                }
            @endphp

            <div class="shop-card">
                @if($produto->estoque <= 0)
                    <span class="badge-sold-out">esgotado</span>
                @endif

                <img src="{{ $urlFinal }}" alt="{{ $produto->nome }}" class="shop-card-image">

                <div class="shop-card-info">
                    <h3 class="shop-card-title">{{ $produto->nome }}</h3>
                    <span class="shop-card-price">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                </div>

                <span class="shop-card-sub">coleção morada</span>

                <button class="btn-comprar"
                        data-id="{{ $produto->id }}"
                        data-nome="{{ $produto->nome }}"
                        data-preco="{{ $produto->preco }}"
                        data-imagem="{{ $urlFinal }}">
                    adicionar ao carrinho
                </button>
            </div>
        @endforeach
    </main>
@endsection
