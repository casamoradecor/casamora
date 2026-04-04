@extends('layouts.app')

@section('title', 'CASA MORÁ — EDITOR VISUAL')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/edit-mode.css') }}">
@endpush

@section('content')
    <main class="admin-visual-editor">

        {{-- SEÇÃO HERO --}}
        <section class="hero edit-container">
            <img src="{{ asset('assets/hero_banner.png') }}" alt="banner" class="hero-img">
            <div class="edit-overlay">
                <form action="{{ route('admin.uploadBanner') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="btn-edit-label">
                        <i class="fa-solid fa-camera"></i> trocar banner principal
                        <input type="file" name="hero_img" onchange="this.form.submit()" style="display: none;">
                    </label>
                </form>
            </div>
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <img src="{{ asset('assets/LogoMora.png') }}" alt="Casa MORÁ" class="hero-logo">
                <p class="hero-sub">onde objetos transformam casas em moradas</p>
            </div>
        </section>

        {{-- SEÇÃO DESTAQUE --}}
        <section class="destaque-produtos">
            <div class="destaque-imagem edit-container">
                <img src="{{ asset('assets/destaque_home.png') }}" alt="Destaque">
                <div class="edit-overlay">
                    <form action="{{ route('admin.updateDestaque') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="btn-edit-label">
                            <i class="fa-solid fa-image"></i> trocar destaque
                            <input type="file" name="destaque_img" onchange="this.form.submit()" style="display: none;">
                        </label>
                    </form>
                </div>
            </div>

            <div class="destaque-carrossel">
                <div class="carrossel-track">
                    @foreach($produtos as $produto)
                        <div class="produto-card">
                            <img src="{{ Storage::url($produto->imagem) }}" alt="{{ $produto->nome }}">
                            <div class="produto-info">
                                <h3 class="produto-titulo">{{ $produto->nome }}</h3>
                                <p class="produto-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- SEÇÃO CATEGORIAS --}}
        <section class="categorias-secao">
            <h2 class="categorias-titulo">compre por categoria</h2>
            <div class="categorias-container">
                <div class="categorias-track">
                    @for ($i = 1; $i <= 3; $i++)
                        <div class="categoria-card edit-container" style="height: 300px; position: relative;">
                            <img src="{{ asset('assets/categoria_'.$i.'.png') }}?v={{ time() }}" style="width: 100%; height: 100%; object-fit: cover;">

                            <div class="edit-overlay" style="opacity: 1; background: rgba(0,0,0,0.6); display: flex; flex-direction: column; justify-content: center; padding: 15px; gap: 10px;">

                                <form action="{{ route('admin.updateCategoria', $i) }}" method="POST" enctype="multipart/form-data" style="width: 100%; display: flex; flex-direction: column; gap: 8px;">
                                    @csrf

                                    {{-- BOTÃO 1: TROCAR APENAS A FOTO (Auto-submit) --}}
                                    <label class="btn-edit-label" style="background: #fff; color: #000; padding: 8px; border-radius: 50px; font-size: 0.6rem; cursor: pointer; text-align: center; margin: 0;">
                                        <i class="fa-solid fa-camera"></i> trocar foto {{ $i }}
                                        <input type="file" name="cat_img" onchange="this.form.submit()" style="display: none;">
                                    </label>

                                    <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.2); margin: 5px 0;">

                                    <div style="position: relative; z-index: 1001; width: 100%;">
                                        <select name="categoria_id" required style="width: 100%; height: 35px; background: #fff; color: #000; border: 1px solid #333; cursor: pointer; display: block !important; pointer-events: all !important;">
                                            <option value="">vincular categoria...</option>
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- BOTÃO 2: SALVAR O VÍNCULO --}}
                                    <button type="submit" class="btn-confirm"
                                            style="background: #3d2b1f; color: #fff; border: 1px solid #fff; padding: 8px; border-radius: 50px; font-size: 0.6rem; font-weight: 700; cursor: pointer; text-transform: uppercase; position: relative; z-index: 10000; pointer-events: auto !important;">
                                        salvar link {{ $i }}
                                    </button>
                                </form>

                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        {{-- SEÇÃO SHOPPABLE (AMBIENTE COMPRÁVEL ESTILO ROWEAM) --}}
        <section class="shoppable-secao">
            <div class="shoppable-wrapper" style="max-width: 1200px; margin: 0 auto;">

                {{-- 1. PAINEL DE CONTROLE (UPLOAD) --}}
                <div class="shoppable-admin-header" style="background: #fff; padding: 20px; border: 1px solid #ddd; border-bottom: none; display: flex; justify-content: space-between; align-items: center; border-radius: 8px 8px 0 0;">
                    <div>
                        <h3 style="font-size: 0.9rem; color: #3d2b1f; margin: 0; text-transform: uppercase; font-weight: 800;">CONFIGURAR AMBIENTE COMPRÁVEL</h3>
                        <p style="font-size: 0.7rem; color: #888; margin: 5px 0 0;">Clique na imagem abaixo para marcar onde estão os produtos.</p>
                    </div>

                    <form action="{{ route('admin.updateShoppable') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="btn-edit-label" style="font-size: 0.6rem; padding: 10px 20px;">
                            <i class="fa-solid fa-camera"></i> trocar imagem do ambiente
                            <input type="file" name="shoppable_img" onchange="this.form.submit()" style="display: none;">
                        </label>
                    </form>
                </div>

                {{-- 2. ÁREA DE MARCAÇÃO (HOTSPOTS) --}}
                <div class="shoppable-container-v2" id="shoppable-area" style="position: relative; line-height: 0; border: 1px solid #ddd; background: #fff; overflow: hidden; border-radius: 0 0 8px 8px;">
                    <img src="{{ asset('assets/shoppable_main.png') }}" alt="Ambiente Decorado" class="shoppable-main-img" id="shoppable-img" style="width: 100%; height: auto; cursor: crosshair;">

                    @foreach($shoppablePoints ?? [] as $point)
                        <div class="hotspot-dot" style="top: {{ $point->y_pos }}%; left: {{ $point->x_pos }}%;">

                            {{-- Botão de excluir --}}
                            <form action="{{ route('admin.deleteHotspot', $point->id) }}" method="POST" class="hotspot-delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete-point">×</button>
                            </form>

                            {{-- Estrutura visual --}}
                            <div class="hotspot-circle"></div>
                            <span class="hotspot-label">{{ $point->produto->nome }}</span>

                            {{-- Popover de visualização --}}
                            <a href="{{ route('produto.show', $point->produto->id) }}" class="hotspot-product-preview">
                                <img src="{{ Storage::url($point->produto->imagem) }}" alt="{{ $point->produto->nome }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 2px;">

                                <div class="preview-info">
                                    <h4>{{ $point->produto->nome }}</h4>
                                    <p>R$ {{ number_format($point->produto->preco, 2, ',', '.') }}</p>
                                    <span class="ver-mais-link">CLIQUE PARA VER DETALHES</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Modal de Seleção de Produto (Invisível por padrão) — MELHORADO E PROFISSIONAL --}}
            <div id="modal-selecionar-produto" class="modal-mora" style="display: none; background: rgba(0,0,0,0.85);">
                <div class="modal-content">
                    <h3>vincular produto</h3>
                    <p>Selecione o produto que está nesta posição da foto:</p>

                    <form id="form-novo-ponto" action="{{ route('admin.saveHotspot') }}" method="POST">
                        @csrf
                        <input type="hidden" name="x_pos" id="input-x">
                        <input type="hidden" name="y_pos" id="input-y">

                        <select name="produto_id" class="input-mora" required style="margin-bottom: 25px;">
                            <option value="">Selecione um produto...</option>
                            @foreach($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                            @endforeach
                        </select>

                        <div class="modal-buttons">
                            <button type="button" class="btn-cancel" onclick="fecharModalHotspot()">cancelar</button>
                            <button type="submit" class="btn-confirm">salvar ponto</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </main>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const shoppableImg = document.getElementById('shoppable-img');
            const modalAdicionar = document.getElementById('modal-selecionar-produto');
            const hotspotsSaved = document.querySelectorAll('.hotspot-dot');

            // 1. GESTÃO DOS PONTOS SALVOS (Clique para ver popover)
            hotspotsSaved.forEach(dot => {
                dot.addEventListener('click', function(e) {
                    // Impede que o clique no ponto abra o modal de "novo ponto" na imagem de fundo
                    e.stopPropagation();

                    // Fecha outros popovers abertos
                    hotspotsSaved.forEach(d => { if(d !== dot) d.classList.remove('active'); });

                    // Ativa o popover deste ponto
                    this.classList.toggle('active');
                });
            });

            // Fecha qualquer popover se clicar fora
            document.addEventListener('click', function() {
                hotspotsSaved.forEach(dot => dot.classList.remove('active'));
            });

            // 2. GESTÃO DE ADICIONAR NOVO PONTO (Clique na imagem de fundo)
            if (shoppableImg) {
                shoppableImg.addEventListener('click', function(e) {
                    // Calcula a posição do clique em % relativa à imagem
                    const rect = shoppableImg.getBoundingClientRect();
                    const x = ((e.clientX - rect.left) / rect.width) * 100;
                    const y = ((e.clientY - rect.top) / rect.height) * 100;

                    // Preenche os inputs ocultos
                    document.getElementById('input-x').value = x.toFixed(2);
                    document.getElementById('input-y').value = y.toFixed(2);

                    // Abre o modal
                    modalAdicionar.style.display = 'flex';
                });
            }
        });

        function fecharModalHotspot() {
            document.getElementById('modal-selecionar-produto').style.display = 'none';
        }
    </script>
@endpush
