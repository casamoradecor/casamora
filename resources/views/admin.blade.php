@extends('layouts.app')

@section('title', 'Casa MORÁ — Admin')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
<div class="admin-wrapper admin-body">
    <aside class="admin-sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('assets/ICONE RGB.png') }}" alt="Casa MORÁ">
            <span style="font-size: 0.8rem; font-weight: 700; color: #333; letter-spacing: 1px;">casa morá</span>
        </div>

        <nav class="sidebar-nav">
            <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i> início</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-chart-line"></i> estatísticas</a>

            <div class="nav-group-title">Gestão</div>
            <a href="{{ route('admin.produtos.create') }}" class="nav-item"><i class="fa-solid fa-box"></i> produtos</a>
            <a href="#" class="nav-item"><i class="fa-solid fa-receipt"></i> vendas</a>
            
            <div class="nav-group-title">Personalização</div>
            <a href="#" class="nav-item"><i class="fa-solid fa-palette"></i> visual da loja</a>
        </nav>

        <div style="padding: 20px; border-top: 1px solid #f5f5f5;">
            <a href="/" class="nav-item" style="color: #888;"><i class="fa-solid fa-arrow-left"></i> voltar ao site</a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Início</h1>
            <div class="user-info">
                {{-- Busca o nome do usuário logado no banco --}}
                <span>olá, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong></span>
            </div>
        </header>

        <div class="status-banner">
            <div>
                <i class="fa-solid fa-check-circle" style="margin-right: 10px;"></i>
                <strong>Painel Ativo.</strong> Gerencie sua loja com as cores da Casa MORÁ.
            </div>
        </div>

        <div class="card-dashboard">
            <h3 style="font-family: 'Playfair Display'; margin-bottom: 20px;">O que vamos atualizar agora?</h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 30px;">
                Escolha uma das opções rápidas abaixo ou navegue pelo menu lateral para edições detalhadas.
            </p>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div style="border: 1px solid #eee; padding: 25px; border-radius: 12px;">
                    <i class="fa-solid fa-image" style="font-size: 1.5rem; color: var(--color-brand); margin-bottom: 15px;"></i>
                    <h4 style="margin-bottom: 10px;">Visual da Home</h4>
                    <p style="font-size: 0.8rem; color: #888; margin-bottom: 20px;">Troque o banner principal, a imagem do ambiente ou as categorias.</p>
                    <button class="btn-admin-primary">editar visual</button>
                </div>

                <div style="border: 1px solid #eee; padding: 25px; border-radius: 12px;">
                    <i class="fa-solid fa-box-open" style="font-size: 1.5rem; color: var(--color-brand); margin-bottom: 15px;"></i>
                    <h4 style="margin-bottom: 10px;">Estoque de Produtos</h4>
                    <p style="font-size: 0.8rem; color: #888; margin-bottom: 20px;">Adicione novos itens ao carrossel ou ajuste preços e quantidades.</p>
                    <button class="btn-admin-primary">gerenciar itens</button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection