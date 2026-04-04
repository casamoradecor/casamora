@extends('layouts.app')

@section('title', 'Casa MORÁ — Home')
@section('header_class', 'header-transparent')

@section('content')
    <main>
        <section class="hero">
            <img src="{{ asset('assets/hero_banner.png') }}" alt="banner" class="hero-img">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <img src="{{ asset('assets/LogoMora.png') }}" alt="Casa MORÁ" class="hero-logo">
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
            <div class="shoppable-container" id="shoppable-area" style="position: relative; line-height: 0;">

                <img src="{{ asset('assets/shoppable_main.png') }}" alt="Ambiente Decorado"
                     class="shoppable-main-img" id="shoppable-img">

                @foreach($shoppablePoints ?? [] as $point)
                    <div class="hotspot-dot"
                         style="position: absolute; top: {{ $point->y_pos }}%; left: {{ $point->x_pos }}%; display: flex; align-items: center; gap: 12px; transform: translate(-50%, -50%); cursor: pointer; z-index: 10;">

                        <div class="hotspot-circle"
                             style="width: 18px; height: 18px; border: 2px #fff; background: transparent; border-radius: 50%; box-shadow: 0 0 8px rgba(0,0,0,0.5); flex-shrink: 0;">
                        </div>

                        <span class="hotspot-label"
                              style="color: #fff; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; text-shadow: 1px 1px 3px rgba(0,0,0,0.8); white-space: nowrap; letter-spacing: 1px;">
            {{ $point->produto->nome }}
        </span>

                        <a href="{{ route('produto.show', $point->produto->id) }}"
                           class="hotspot-product-preview"
                           style="position: absolute; bottom: 40px; left: 50%; transform: translateX(-50%); background: #fff; width: 280px; display: flex; padding: 15px; gap: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border-radius: 4px; text-decoration: none; color: #333; opacity: 0; visibility: hidden; transition: all 0.3s ease; z-index: 20;">

                            <img src="{{ Storage::url($point->produto->imagem) }}" alt="{{ $point->produto->nome }}"
                                 style="width: 70px; height: 70px; object-fit: cover; border-radius: 2px; flex-shrink: 0;">

                            <div class="preview-info"
                                 style="display: flex; flex-direction: column; justify-content: center; gap: 15px; text-align: left;">
                                <h4 style="font-size: 0.8rem; margin: 0; color: #3d2b1f; line-height: 1.2; font-weight: 800;">{{ $point->produto->nome }}</h4>
                                <p style="font-size: 0.95rem; font-weight: 700; margin: 0; color: #000;">
                                    R$ {{ number_format($point->produto->preco, 2, ',', '.') }}
                                </p>
                                <span
                                    style="font-size: 0.6rem; color: #888; text-decoration: underline; margin-top: 5px;">CLIQUE PARA VER DETALHES</span>
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
                            const preview = this.querySelector('.hotspot-product-preview');

                            if (e.target.closest('.hotspot-circle') || e.target.closest('.hotspot-label')) {
                                if (preview.style.visibility === 'hidden' || preview.style.opacity === '0') {
                                    e.preventDefault();
                                    hotspots.forEach(d => {
                                        d.classList.remove('active');
                                        const p = d.querySelector('.hotspot-product-preview');
                                        p.style.opacity = '0';
                                        p.style.visibility = 'hidden';
                                    });
                                    this.classList.add('active');
                                    preview.style.opacity = '1';
                                    preview.style.visibility = 'visible';
                                }
                            }
                        });
                    });
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.hotspot-dot')) {
                            hotspots.forEach(dot => {
                                dot.classList.remove('active');
                                const p = dot.querySelector('.hotspot-product-preview');
                                p.style.opacity = '0';
                                p.style.visibility = 'hidden';
                            });
                        }
                    });
                });
            </script>
    @endpush
