@extends('layouts.app')

@section('title', 'Minha Conta — Casa MORÁ')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
<main class="dashboard-container">
    <aside class="dashboard-nav">
        <a href="{{ route('dashboard') }}" style="text-decoration: underline;">RESUMO</a>
        
        <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
        
        <a href="#">ENDEREÇOS</a> <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">SAIR DA CONTA</button>
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
                <a href="{{ route('perfil.edit') }}" style="color: #4B3621; font-size: 0.75rem; margin-top: 15px; display: block; font-weight: 700; text-transform: uppercase;">Editar Dados</a>
            </div>

            <div class="info-card">
                <h3>PEDIDO RECENTE</h3>
                <p>Você ainda não realizou nenhum pedido.</p>
                <a href="{{ route('home') }}" style="color: #4B3621; font-size: 0.75rem; margin-top: 15px; display: block; font-weight: 700; text-transform: uppercase;">VER PRODUTOS</a>
            </div>
        </div>
    </section>
</main>
@endsection