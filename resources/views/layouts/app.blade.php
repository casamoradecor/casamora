<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="carrinho-url" content="{{ route('carrinho.adicionar') }}">
    <meta name="carrinho-listar-url" content="{{ route('carrinho.listar') }}">
    <meta name="carrinho-diminuir-url" content="{{ route('carrinho.diminuir') }}">
    <title>@yield('title', 'Casa MORÁ')</title>
    <link rel="icon" href="{{ asset('assets/ICONE RGB.png') }}" type="image/png">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Poppins:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/carrinho.css') }}">
    @stack('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
<header class="site-header @yield('header_class')">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="{{ asset('assets/ICONE RGB.png') }}" alt="Logo Casa MORÁ">
        </a>

        <button class="mobile-nav-toggle" id="mobileNavToggle" type="button" aria-label="Abrir menu"
                aria-controls="siteNav" aria-expanded="false">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <nav class="site-nav" id="siteNav">
            <a href="/">INÍCIO</a>
            @auth
                @if(Auth::check() && Auth::id() === 1)
                    {{-- Volta para a tela de Banners/Categorias --}}
                    <a href="{{ route('admin.index') }}">MODO EDIÇÃO</a>
                @endif
            @endauth
            <a href="{{ route('produtos.index') }}">PRODUTOS</a>
            <a href="{{ route('sobre.nos') }}">SOBRE NÓS</a>
        </nav>

        <div class="header-actions" style="position: relative;">
            <form action="{{ route('produtos.index') }}" method="GET" class="busca-inline" id="buscaInline">

                <input type="text" name="busca" id="inputBusca" placeholder="o que você procura?" autocomplete="off">

                <button type="submit" class="icon" id="btnBusca">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>

                <button type="button" class="icon" id="btnFecharBusca" style="display: none;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </form>

            <a href="{{ Auth::check() ? route('dashboard') : route('login') }}" class="icon" aria-label="conta">
                <i class="fa-regular fa-user"></i>
            </a>
            <button class="icon" id="btnCarrinho"><i class="fa-solid fa-cart-shopping"></i></button>

            <div id="resultadosBusca" class="resultados-busca-wrapper"></div>
        </div>
    </div>
</header>

@yield('content')

@if(!Request::is('admin*'))
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-col newsletter">
                <h3 style="font-family: 'Poppins', sans-serif">Assine nossa Newsletter</h3>
                <p>Receba novidades e ofertas exclusivas da Casa MORÁ.</p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="news-form">
                    @csrf
                    <input type="email" name="email" placeholder="Seu melhor e-mail" required>
                    <button type="submit" aria-label="Enviar"><i class="fa-solid fa-arrow-right"></i></button>
                </form>

                @if(session('sucesso_newsletter'))
                    <p style="color: #5B8C5A; font-size: 0.85rem; margin-top: 10px; font-family: 'Poppins', sans-serif;">
                        <i class="fa-solid fa-check"></i> {{ session('sucesso_newsletter') }}
                    </p>
                @endif
            </div>

            <div class="footer-col links">
                <h3 style="font-family: 'Poppins', sans-serif">Navegação</h3>
                <a href="/">Início</a>
                <a href="{{ route('produtos.index') }}">Produtos</a>
                <a href="{{ route('sobre.nos') }}">Sobre nós</a>
                <a href="#">Contato</a>
            </div>

            <div class="footer-col links">
                <h3 style="font-family: 'Poppins', sans-serif">Políticas</h3>
                <a href="#">Trocas e Devoluções</a>
                <a href="#">Política de Privacidade</a>
                <a href="#">Termos de Uso</a>
            </div>

            <div class="footer-col social">
                <h3 style="font-family: 'Poppins', sans-serif">Siga a Casa MORÁ</h3>
                <div class="social-icons">
                    <a href="https://www.instagram.com/casamora.decora/" aria-label="Instagram"><i
                            class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@casamoradecora" aria-label="TikTok"><i
                            class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2026 Casa MORÁ. Todos os direitos reservados.</p>
        </div>
    </footer>
@endif
<aside class="carrinho-sidebar" id="carrinhoSidebar">
    <div class="carrinho-header">
        <h2>Seu Carrinho</h2>
        <button class="fechar-carrinho" id="btnFecharCarrinho">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="carrinho-itens" id="listaCarrinho">
        <p class="carrinho-vazio">Seu carrinho está vazio.</p>
    </div>

    <div class="carrinho-footer">
        <div class="carrinho-total">
            <span>Total:</span>
            <span id="valorTotal">R$ 0,00</span>
        </div>
        <a href="{{ route('checkout') }}" class="btn-finalizar">FINALIZAR COMPRA</a>
    </div>
</aside>

<div class="carrinho-overlay" id="carrinhoOverlay"></div>

<script src="{{ asset('js/main.js') }}"></script>
<script src="{{ asset('js/carrinho.js') }}"></script>
<script src="{{ asset('js/frete.js') }}"></script>
@stack('js')
</body>
</html>
