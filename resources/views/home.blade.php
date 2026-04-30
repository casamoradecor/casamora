@extends('layouts.app')

@section('title', 'Casa MORÁ — Home')
@section('header_class', 'header-transparent')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
    <main>
        <section class="hero">
            <img src="{{ asset('assets/hero_banner.png') }}" alt="banner" class="hero-img">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <p class="hero-sub">onde objetos transformam casas em moradas</p>
            </div>
        </section>

        <section class="destaque-produtos">
            <div class="destaque-imagem">
                <img src="{{ asset('assets/destaque_home.png') }}" alt="Destaque">
            </div>

            <div class="destaque-carrossel">
                <button class="carrossel-btn btn-prev" id="btnPrev"><i class="fa-solid fa-chevron-left"></i></button>
                <button class="carrossel-btn btn-next" id="btnNext"><i class="fa-solid fa-chevron-right"></i></button>

                <div class="carrossel-track" id="carrosselTrack">
                    @foreach($produtos as $produto)
                        @php
                            $caminho = $produto->imagem;
                            if ($caminho && str_contains($caminho, 'assets')) {
                                $urlFinal = asset(ltrim($caminho, '/'));
                            } else {
                                $urlFinal = $caminho ? Storage::url($caminho) : asset('assets/vasomora.png');
                            }
                        @endphp

                        <div class="produto-card">
                            <img src="{{ $urlFinal }}" alt="{{ $produto->nome }}">
                            <div class="produto-info">
                                <h3 class="produto-titulo">{{ $produto->nome }}</h3>
                                <p class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                            </div>

                            <button class="btn-comprar"
                                    data-id="{{ $produto->id }}"
                                    data-nome="{{ $produto->nome }}"
                                    data-preco="{{ $produto->preco }}"
                                    data-imagem="{{ $urlFinal }}"> adicionar ao carrinho
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="categorias-secao">
            <h2 class="categorias-titulo">compre por categoria</h2>

            <div class="categorias-container">
                <div class="categorias-track">
                    @for ($i = 1; $i <= 3; $i++)
                        @php
                            $slot = $slots->get($i);
                            $link = $slot ? route('produtos.index', ['categoria' => $slot->categoria_id]) : '#';
                            $nome = $slot->categoria->nome ?? 'ver mais';
                        @endphp

                        <a href="{{ $link }}" class="categoria-card">
                            <img src="{{ asset('assets/categoria_'.$i.'.png') }}?v={{ time() }}" alt="{{ $nome }}">
                        </a>
                    @endfor
                </div>
            </div>
        </section>

        <section class="shoppable-secao">
            <div class="shoppable-container" id="shoppable-area"
                 style="position: relative; line-height: 0; display: inline-block; width: 100%;">

                <img src="{{ asset('assets/shoppable_main.png') }}" alt="Ambiente"
                     class="shoppable-main-img" id="shoppable-img" style="width: 100%; height: auto;">

                @foreach($shoppablePoints ?? [] as $point)
                    <div class="hotspot-dot" style="top: {{ $point->y_pos }}%; left: {{ $point->x_pos }}%;">

                        <div class="hotspot-circle"></div>

                        <span class="hotspot-label">{{ $point->produto->nome }}</span>

                        <a href="{{ route('produto.show', $point->produto->id) }}"
                           class="hotspot-product-preview">

                            <img src="{{ Storage::url($point->produto->imagem) }}" alt="{{ $point->produto->nome }}"
                                 class="preview-img">

                            <div class="preview-info">
                                <h4>{{ $point->produto->nome }}</h4>
                                <p>
                                    R$ {{ number_format($point->produto->preco, 2, ',', '.') }}
                                </p>
                                <span>CLIQUE PARA VER DETALHES</span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>

        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const hotspots = document.querySelectorAll('.hotspot-dot');

                    hotspots.forEach(dot => {
                        dot.addEventListener('click', function (e) {
                            if (e.target.closest('.hotspot-circle') || e.target.closest('.hotspot-label')) {
                                if (!this.classList.contains('active')) {
                                    e.preventDefault();
                                    hotspots.forEach(d => d.classList.remove('active'));
                                    this.classList.add('active');
                                }
                            }
                        });
                    });
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.hotspot-dot')) {
                            hotspots.forEach(dot => dot.classList.remove('active'));
                        }
                    });
                });
            </script>
    @endpush
