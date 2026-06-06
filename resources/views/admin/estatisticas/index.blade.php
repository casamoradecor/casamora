@extends('layouts.app')

@section('title', 'Estatísticas — Casa MORÁ Admin')
@section('header_class', 'admin-hidden')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-novo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estatisticas.css') }}">
@endpush

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <button style="padding: 10px" class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>

            <header class="admin-header">
                <h1 style="font-family: 'Poppins', serif; font-weight: bold">Estatísticas</h1>
                <div class="user-info"><span>Painel de Controle</span></div>
            </header>

            <p style="color: #666; margin-bottom: 30px;">Visão geral do desempenho da Casa MORÁ.</p>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Acessos ao Site</h3>
                    <p>{{ number_format($totalVisitas, 0, ',', '.') }}</p>
                </div>
                <div class="stat-card">
                    <h3>Usuários Cadastrados</h3>
                    <p>{{ $totalUsuarios }}</p>
                </div>
                <div class="stat-card">
                    <h3>Assinantes Newsletter</h3>
                    <p>{{ $totalNewsletter }}</p>
                </div>
            </div>

            <div class="charts-grid">
                <div class="chart-box">
                    <h3 class="chart-title">Top 5 Produtos Mais Vendidos</h3>
                    <div class="canvas-container">
                        <canvas id="graficoProdutos"></canvas>
                    </div>
                </div>

                <div class="chart-box">
                    <h3 class="chart-title">Usuários vs Newsletter</h3>
                    <div class="canvas-container">
                        <canvas id="graficoNewsletter"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Função para abrir e fechar o menu no mobile
        function toggleAdminMenu() {
            const sidebar = document.querySelector('.admin-sidebar');
            if (sidebar) sidebar.classList.toggle('active');
        }

        // Fecha o menu automaticamente se o usuário clicar fora dele
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.admin-sidebar');
            const toggleBtn = document.querySelector('.mobile-menu-toggle');

            if (sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        const labelsProdutos = @json($labelsProdutos);
        const dadosProdutos = @json($dadosProdutos);
        const totalUsuarios = {{ $totalUsuarios }};
        const totalNewsletter = {{ $totalNewsletter }};

        const ctxProdutos = document.getElementById('graficoProdutos');
        if (ctxProdutos) {
            new Chart(ctxProdutos.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labelsProdutos,
                    datasets: [{
                        label: 'Unidades Vendidas',
                        data: dadosProdutos,
                        backgroundColor: '#4B3621',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        const ctxNewsletter = document.getElementById('graficoNewsletter');
        if (ctxNewsletter) {
            new Chart(ctxNewsletter.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Inscritos na Newsletter', 'Apenas Cadastrados'],
                    datasets: [{
                        data: [totalNewsletter, (totalUsuarios - totalNewsletter)],
                        backgroundColor: ['#5B8C5A', '#E0D8C3'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { position: 'bottom' }
                    }
                }
            });
        }
    </script>
@endpush
