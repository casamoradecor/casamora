@extends('layouts.app')

@section('title', 'Gestão de Categorias — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-novo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/categorias.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <button style="padding: 10px" class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                <i class="fa-solid fa-bars"></i>
            </button>
            <header class="admin-header">
                <h1 style="font-family: 'Poppins', serif; font-weight: bold">Categorias</h1>
                <div class="user-info"><span>Painel de Controle</span></div>
            </header>

            <form action="{{ route('admin.categorias.store') }}" method="POST" id="formFinal">
                @csrf
                <div class="categorias-grid">
                    <section class="categorias-form-card">
                        <h3 class="categorias-title" style="font-family: 'Poppins', serif">incluir na lista</h3>
                        <div class="form-group">
                            <label class="label-mora">nome da categoria</label>
                            <input type="text" id="temp_nome" class="input-mora" placeholder="Ex: vasos de cerâmica">
                        </div>
                        <div class="form-group">
                            <label class="label-mora">descrição (opcional)</label>
                            <textarea id="temp_descricao" class="input-mora" rows="3"
                                      placeholder="Breve resumo..."></textarea>
                        </div>
                        <button type="button" class="btn btn-marrom" onclick="adicionarNaLista()">
                            incluir na lista
                        </button>
                    </section>

                    <section class="categorias-table-card">
                        <h3 class="categorias-title" style="font-family: 'Poppins', serif">categorias ativas</h3>
                        <table class="admin-table">
                            <thead>
                            <tr>
                                <th>nome</th>
                                <th style="text-align: center;">status</th>
                                <th style="text-align: right;">ações</th>
                            </tr>
                            </thead>
                            <tbody id="listaCategorias">
                            @foreach($categorias as $categoria)
                                <tr id="row-{{ $categoria->id }}">
                                    <td>
                                        <input type="hidden" name="categorias_existentes[{{ $categoria->id }}][nome]"
                                               value="{{ $categoria->nome }}">
                                        <strong class="categoria-nome"
                                                id="text-nome-existente-{{ $categoria->id }}">{{ $categoria->nome }}</strong>
                                    </td>
                                    <td style="text-align: center;">
                                        <span
                                            class="status-badge">{{ $categoria->status == 'ativa' ? 'ativo' : 'inativo' }}</span>
                                    </td>
                                    <td class="td-actions">
                                        <button type="button" class="btn-action-minimal"
                                                onclick="editarExistente({{ $categoria->id }}, '{{ $categoria->nome }}')">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" class="btn-action-minimal btn-trash"
                                                onclick="confirmarExclusao('{{ route('admin.categorias.destroy', $categoria->id) }}', '{{ $categoria->nome }}')">
                                            <i class="fa-solid fa-trash" style="color: red"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </section>
                </div>

                <div class="categorias-footer-actions">
                    <button type="submit" class="btn btn-marrom">
                        salvar alterações
                    </button>
                </div>
            </form>
        </main>
    </div>

    {{-- MODAL EXCLUIR --}}
    <div id="modalExcluir" class="modal-mora-overlay">
        <div class="modal-mora-content">
            <h2 class="modal-mora-title">Confirmar Exclusão</h2>
            <p class="modal-mora-text">Tem certeza que deseja excluir a categoria:</p>
            <div id="excluirNomeItem" class="modal-mora-item-name"></div>

            <div class="modal-mora-actions">
                <button type="button" class="btn btn-branco" onclick="fecharModal('modalExcluir')">cancelar</button>
                <button type="button" class="btn btn-marrom" id="btnConfirmarExclusao">excluir</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDITAR --}}
    <div id="modalEditar" class="modal-mora-overlay">
        <div class="modal-mora-content">
            <h2 class="modal-mora-title">Editar Categoria</h2>
            <div class="form-group" style="text-align: left;">
                <label class="label-mora">novo nome</label>
                <input type="text" id="edit_nome" class="input-mora">
            </div>
            <input type="hidden" id="edit_id">

            <div class="modal-mora-actions">
                <button type="button" class="btn btn-branco" onclick="fecharModal('modalEditar')">cancelar</button>
                <button type="button" class="btn btn-marrom" onclick="salvarEdicaoModal()">salvar</button>
            </div>
        </div>
    </div>

    {{-- Form oculto para o DELETE real --}}
    <form id="formExcluir" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('js')
    <script src="{{ asset('js/categorias.js') }}"></script>
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
