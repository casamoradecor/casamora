@extends('layouts.app')

@section('title', 'Gestão de Vendas — Casa MORÁ Admin')

@section('header_class', 'admin-hidden')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/vendas.css') }}">

@endpush

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')
        <main class="admin-main">
            <header class="admin-header" style="margin-bottom: 30px;">
                <button style="padding: 10px" class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 2.2rem; color: var(--color-brand); margin: 0;">Vendas & Pedidos</h1>
                <p style="color: #666; font-size: 0.9rem;">Gerencie as vendas confirmadas e prepare os despachos.</p>
            </header>

            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>Pedido</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pedidos as $p)
                        <tr>
                            <td class="order-number">#{{ $p->id }}</td>
                            <td>
                                {{ $p->cliente?->name ?? 'Usuário não encontrado' }}
                            </td>
                            <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($p->status == 'pago')
                                    <span class="status-badge status-pago">Pago</span>
                                @elseif($p->status == 'enviado')
                                    <span class="status-badge" style="background: #4B3621; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.7rem;">Enviado</span>
                                @elseif($p->status == 'pendente')
                                    <span class="status-badge" style="background: #D4AF37; color: white; padding: 5px 10px; border-radius: 4px; font-size: 0.7rem;">Pendente</span>
                                @else
                                    <span class="status-badge">{{ strtoupper($p->status) }}</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; text-transform: uppercase">R$ {{ number_format($p->valor_total, 2, ',', '.') }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.pedidos.show', $p->id) }}" class="btn btn-branco">
                                    Ver Detalhes
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
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
@endsection
