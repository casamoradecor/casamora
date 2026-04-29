@extends('layouts.app')

@section('title', 'Todos os Produtos — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/shop.css') }}">
@endpush

@section('content')
    <header class="shop-header">
        <div class="shop-filters-bar">
            <form action="{{ route('produtos.index') }}" method="GET" class="filter-form">
                <div class="search-group">

                    <input type="text" name="busca" id="search-input" value="{{ request('busca') }}"
                           placeholder="pesquisar na coleção..." class="search-input"
                           >

                    @if(request('busca'))
                        <a href="{{ route('produtos.index') }}" class="btn-clear-search" title="limpar busca">
                            &times;
                        </a>
                    @endif
                    <button type="submit" class="btn-search-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                             class="bi bi-search" viewBox="0 0 16 16" style="color: var(--color-brand);">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="select-groups">
                    <select name="categoria" onchange="this.form.submit()">
                        <option value="">todas as coleções</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nome }}
                            </option>
                        @endforeach
                    </select>

                    <select name="ordem" onchange="this.form.submit()">
                        <option value="">ordenar por</option>
                        <option value="preco_min" {{ request('ordem') == 'preco_min' ? 'selected' : '' }}>menor preço
                        </option>
                        <option value="preco_max" {{ request('ordem') == 'preco_max' ? 'selected' : '' }}>maior preço
                        </option>
                    </select>
                </div>

            </form>
        </div>
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
                <a href="{{ route('produto.show', $produto->id) }}" class="shop-product-link">
                    <img src="{{ $urlFinal }}" alt="{{ $produto->nome }}" class="shop-card-image">
                </a>

                <div class="shop-card-info">
                    <a href="{{ route('produto.show', $produto->id) }}" class="shop-product-link">
                        <h3 class="shop-card-title">{{ $produto->nome }}</h3>
                    </a>
                    <span class="shop-card-price">R$ {{ number_format($produto->preco, 2, ',', '.') }}</span>
                </div>
                <span class="shop-card-sub">{{ $produto->categoria->nome ?? 'coleção morada' }}</span>

                <div class="shop-card-actions">
                    <button class="btn-comprar"
                            data-id="{{ $produto->id }}"
                            data-nome="{{ $produto->nome }}"
                            data-preco="{{ $produto->preco }}"
                            data-imagem="{{ $urlFinal }}"
                            >
                        adicionar ao carrinho
                    </button>

                    <a href="{{ route('produto.show', $produto->id) }}" class="btn-comprar ver-mais-btn"
                       >
                        ver mais
                    </a>
                </div>
            </div>
        @endforeach
    </main>
@endsection
