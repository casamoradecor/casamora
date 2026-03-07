@extends('layouts.app')

@section('title', 'CASA MORÁ — EDITOR VISUAL')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/edit-mode.css') }}">
   
@section('content')
<main class="admin-visual-editor">

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

    <section class="categorias-secao">
        <h2 class="categorias-titulo">compre por categoria</h2>
        <div class="categorias-container">
            <div class="categorias-track">
                @for ($i = 1; $i <= 3; $i++)
                <div class="categoria-card edit-container">
                    <img src="{{ asset('assets/categoria_'.$i.'.png') }}" alt="categoria">
                    <div class="edit-overlay">
                        <form action="{{ route('admin.updateCategoria', $i) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="btn-edit-label">
                                <i class="fa-solid fa-camera"></i> trocar foto
                                <input type="file" name="cat_img" onchange="this.form.submit()" style="display: none;">
                            </label>
                        </form>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <section class="shoppable-secao">
        <div class="shoppable-container edit-container">
            <img src="{{ asset('assets/shoppable_main.png') }}" alt="Ambiente Decorado" class="shoppable-main-img">
            <div class="edit-overlay">
                <form action="{{ route('admin.updateShoppable') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="btn-edit-label">
                        <i class="fa-solid fa-house-chimney"></i> trocar foto do ambiente
                        <input type="file" name="shoppable_img" onchange="this.form.submit()" style="display: none;">
                    </label>
                </form>
            </div>
        </div>
    </section>

</main>
@endsection