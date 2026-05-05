@extends('layouts.app')

@section('title', 'Newsletter — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/newsletter-admin.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <header class="admin-header-list">
                <button class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 style="font-family: 'Poppins', serif">Newsletter</h1>
                <span class="header-info-label">Gestão de Inscritos</span>
            </header>

            <div class="admin-header-list" style="flex-direction: row; margin-bottom: 30px;">
                <button type="button" class="btn btn-marrom" onclick="abrirModalEmail()">
                    enviar e-mail para todos
                </button>
            </div>

            @if(session('sucesso'))
                <div class="alert-success-mora" style="margin-bottom: 20px;">
                    {{ session('sucesso') }}
                </div>
            @endif

            <div class="table-card">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>e-mail</th>
                        <th>data de inscrição</th>
                        <th>status</th>
                        <th>ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($inscritos as $inscrito)
                        <tr>
                            <td>
                                <span class="sku-label">#{{ $inscrito->id }}</span>
                            </td>
                            <td>
                                <div class="product-info">
                                    <strong style="text-transform: lowercase;">{{ $inscrito->email }}</strong>
                                </div>
                            </td>
                            <td>{{ $inscrito->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="btn-launch {{ $inscrito->active ? 'active' : '' }}">
                                    {{ $inscrito->active ? 'ativo' : 'inativo' }}
                                </span>
                            </td>
                            <td>
                                <div class="actions-flex">
                                    <button type="button" class="btn-action-minimal trash" onclick="abrirModalExclusao('{{ $inscrito->id }}', '{{ $inscrito->email }}')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <div id="modalEmail" class="modal-overlay">
        <div class="modal-box" style="max-width: 600px;">
            <h2 style="font-family: 'Poppins', serif">criar newsletter</h2>
            <form action="{{ route('admin.newsletter.enviar') }}" method="POST">
                @csrf
                <div style="text-align: left; margin-top: 20px;">
                    <label style="font-size: 0.7rem; font-weight: 800; color: #888;">ASSUNTO DO E-MAIL</label>
                    <input type="text" name="assunto" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 8px;">

                    <label style="font-size: 0.7rem; font-weight: 800; color: #888;">CONTEÚDO (HTML PERMITIDO)</label>
                    <textarea name="conteudo" rows="10" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: sans-serif;"></textarea>
                </div>
                <div class="actions-flex" style="justify-content: center; margin-top: 30px;">
                    <button type="button" onclick="fecharModalEmail()" class="btn btn-branco">cancelar</button>
                    <button type="submit" class="btn btn-marrom">disparar agora</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DELETAR (IGUAL AO SEU) --}}
    <div id="modalDelete" class="modal-overlay">
        <div class="modal-box">
            <h2>confirmar remoção</h2>
            <p>remover o e-mail da lista:<br>
                <strong id="nomeItemModal" class="modal-item-highlight"></strong>
            </p>
            <div class="actions-flex" style="justify-content: center; margin-top: 20px;">
                <button onclick="fecharModal()" class="btn btn-branco">cancelar</button>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-marrom">remover agora</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalEmail() { document.getElementById('modalEmail').style.display = 'flex'; }
        function fecharModalEmail() { document.getElementById('modalEmail').style.display = 'none'; }

        function abrirModalExclusao(id, email) {
            document.getElementById('nomeItemModal').innerText = email;
            document.getElementById('formDelete').action = "/admin/newsletter/" + id;
            document.getElementById('modalDelete').style.display = 'flex';
        }
        function fecharModal() { document.getElementById('modalDelete').style.display = 'none'; }
    </script>
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
