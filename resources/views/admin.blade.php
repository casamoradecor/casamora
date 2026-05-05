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
                <span class="sidebar-logo-text">casa morá</span>
            </div>

            <nav class="sidebar-nav">
                <a href="#" class="nav-item active"><i class="fa-solid fa-house"></i> início</a>
                <a href="#" class="nav-item"><i class="fa-solid fa-chart-line"></i> estatísticas</a>
                <a href="{{ route('admin.newsletter.index') }}" class="nav-item {{ request()->routeIs('admin.newsletter.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-envelope"></i> newsletter
                </a>

                <div class="nav-group-title">Gestão</div>
                <a href="{{ route('admin.categorias.index') }}" class="nav-item">
                    <i class="fa-solid fa-layer-group"></i> categorias
                </a>
                <a href="{{ route('admin.produtos.create') }}" class="nav-item"><i class="fa-solid fa-box"></i> produtos</a>
                <a href="{{ route('admin.vendas.index') }}" class="nav-item">
                    <i class="fa-solid fa-receipt"></i> vendas
                </a>

                <div class="nav-group-title">Personalização</div>
                <a href="{{ route('admin.visual.edit') }}" class="nav-item {{ request()->routeIs('admin.visual.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-palette"></i> visual da loja
                </a>
                <a href="{{ route('admin.sobre.edit') }}" class="nav-item {{ request()->routeIs('admin.sobre.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-address-card"></i> sobre nós
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="/" class="nav-item nav-item-back"><i class="fa-solid fa-arrow-left"></i> voltar ao site</a>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <button class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="font-family: 'Poppins', sans-serif; font-size: 35.2px; font-weight: bold">Início</h1>
                <div class="user-info">
                    <span>olá, <strong>{{ explode(' ', Auth::user()->name)[0] }}</strong></span>
                </div>
            </header>

            <div class="status-banner">
                <div class="status-content">
                    <i class="fa-solid fa-check-circle"></i>
                    <span><strong>Painel Ativo.</strong> Gerencie sua loja com as cores da Casa MORÁ.</span>
                </div>
            </div>

            <div class="card-dashboard">
                <h3 class="card-title" style="font-family: 'Poppins', sans-serif">O que vamos atualizar agora?</h3>
                <p class="card-description">
                    Escolha uma das opções rápidas abaixo ou navegue pelo menu lateral para edições detalhadas.
                </p>

                <div class="admin-options-grid">
                    <div class="option-card">
                        <i class="fa-solid fa-image"></i>
                        <h4>Visual da Home</h4>
                        <p>Troque o banner principal, a imagem do ambiente ou as categorias.</p>
                        <a href="{{ route('admin.visual.edit') }}" class="btn btn-marrom" style="font-family: 'Poppins', sans-serif">
                            editar visual
                        </a>
                    </div>

                    <div class="option-card">
                        <i class="fa-solid fa-box-open"></i>
                        <h4>Estoque de Produtos</h4>
                        <p>Adicione novos itens ao carrossel ou ajuste preços e quantidades.</p>
                        <a href="{{ route('admin.produtos.create') }}" class="btn btn-marrom" style="font-family: 'Poppins', sans-serif">
                            gerenciar itens
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    @push('js')
        <script>
            // Função para abrir e fechar o menu no mobile
            function toggleAdminMenu() {
                const sidebar = document.querySelector('.admin-sidebar');
                sidebar.classList.toggle('active');
            }

            // Fecha o menu automaticamente se o usuário clicar fora dele
            document.addEventListener('click', function(event) {
                const sidebar = document.querySelector('.admin-sidebar');
                const toggleBtn = document.querySelector('.mobile-menu-toggle');

                // Verifica se o clique foi fora da sidebar e do botão de abrir
                if (sidebar && sidebar.classList.contains('active')) {
                    if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        </script>
    @endpush
@endsection
