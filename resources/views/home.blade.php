  @extends('layouts.app')

  @section('title', 'Casa MORÁ — Home')

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
          // Lógica para decidir se usa a URL do Storage ou do Asset direto
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
      </section>
      
      <section class="categorias-secao">
          <h2 class="categorias-titulo">compre por categoria</h2>

          <div class="categorias-container">
              <div class="categorias-track">
                  <a href="#" class="categoria-card">
                     <img src="{{ asset('assets/categoria_1.png') }}" alt="vasos">
                      <div class="categoria-overlay"></div>
                  </a>

                  <a href="#" class="categoria-card">
                      <img src="{{ asset('assets/categoria_2.png') }}" alt="utensilios">
                      <div class="categoria-overlay"></div>
                  </a>

                  <a href="#" class="categoria-card">
                      <img src="{{ asset('assets/categoria_1.png') }}" alt="decorações">
                      <div class="categoria-overlay"></div>
                  </a>
              </div>
          </div>
      </section>

      <section class="shoppable-secao">
        <div class="shoppable-container">
          <img src="{{ asset('assets/EMABALAGEM 2 LOGO RGB.png') }}" alt="Ambiente Decorado" class="shoppable-main-img">

          <div class="shoppable-info">
            <h2>In Situ Living</h2>
            <p>Nossa seleção de peças especificamente escolhidas para transformar seu ambiente.</p>
            <a href="#" class="shop-room-btn">COMPRE ESTE AMBIENTE</a>
          </div>

          <div class="hotspot" style="top: 45%; left: 35%;">
            <button class="hotspot-dot"></button>
            <span class="hotspot-label">Vasos e Decoração</span>
            <div class="hotspot-popover">
              <img src="{{ asset('assets/vasomora.png') }}" alt="Vaso Cerâmica">
              <div class="popover-info">
                <h4>Vaso Cerâmica</h4>
                <a href="#">VER MAIS</a>
              </div>
            </div>
          </div>

          <div class="hotspot" style="top: 35%; left: 75%;">
            <button class="hotspot-dot"></button>
            <span class="hotspot-label">O Sofá Bromley</span>
            <div class="hotspot-popover">
              <img src="{{ asset('assets/EMABALAGEM 2 LOGO RGB.png') }}" alt="Sofá Bromley">
              <div class="popover-info">
                <h4>O Sofá Bromley</h4>
                <a href="#">VER MAIS</a>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  @endsection