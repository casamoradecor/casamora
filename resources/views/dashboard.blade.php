@extends('layouts.app')

@section('title', 'Minha Conta — Casa MORÁ')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<main class="dashboard-container">
    <aside class="dashboard-nav">
        <a href="{{ route('dashboard') }}">RESUMO</a>
        <a href="#">MEUS PEDIDOS</a>
        <a href="#">ENDEREÇOS</a>
        <a href="#">EDITAR PERFIL</a>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sair da Conta</button>
        </form>
    </aside>

    <section class="dashboard-content">
        <h2>OLÁ, {{ explode(' ', Auth::user()->name)[0] }}</h2>
        
        <p style="margin-bottom: 40px; color: #555; font-size: 0.95rem;">
            A partir do painel de controle de sua conta, você pode ver seus pedidos recentes, gerenciar seus endereços de entrega e faturamento e editar sua senha e detalhes da conta.
        </p>

        <div class="info-grid">
            <div class="info-card">
                <h3>DADOS PESSOAIS</h3>
                <p>{{ Auth::user()->name }}</p>
                <p>{{ Auth::user()->email }}</p>
                <p>CPF: {{ Auth::user()->cpf ?? 'Não informado' }}</p>
            </div>

            <div class="info-card">
                <h3>PEDIDO RECENTE</h3>
                <p>Você ainda não realizou nenhum pedido.</p>
                <a href="{{ route('home') }}" style="color: var(--color-brand); font-size: 0.75rem; margin-top: 15px; display: block; font-weight: 700;">VER PRODUTOS</a>
            </div>
        </div>
    </section>
</main>
@endsection