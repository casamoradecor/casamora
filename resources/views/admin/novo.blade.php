@extends('layouts.app')

@section('title', 'CASA MORÁ — ADICIONAR')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/novo-produto.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body-novo">

        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <header class="admin-header-list" style="margin-bottom: 20px;">
                <button class="mobile-menu-toggle" onclick="toggleAdminMenu()">
                    <i class="fa-solid fa-bars"></i>
                </button>
            <div class="novo-container">
                <form action="{{ route('admin.produto.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-form">
                        <label class="label-mora">fotos do produto</label>
                        <div class="upload-placeholder">
                            <input type="file" name="imagem" required>
                        </div>
                    </div>
                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">código do produto (sku)</label>
                            <input type="text" name="codigo" placeholder="ex: mora-001" class="input-mora" required>
                        </div>
                        <div class="card-form">
                            <label class="label-mora">nome do item</label>
                            <input type="text" name="nome" placeholder="ex: vaso de cerâmica morá" class="input-mora" required>
                        </div>
                    </div>
                    <div class="card-form">
                        <label class="label-mora">descrição detalhada</label>
                        <textarea name="descricao" rows="5" class="input-mora" placeholder="detalhes técnicos e estilo..."></textarea>
                    </div>

                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">valor (r$)</label>
                            <input type="number" step="0.01" name="preco" class="input-mora" required>
                        </div>
                        <div class="card-form">
                            <label class="label-mora">estoque inicial</label>
                            <input type="number" name="estoque" class="input-mora" required>
                        </div>
                    </div>

                    <div style="margin: 20px 0 10px 10px;">
                        <span class="label-mora" style="opacity: 0.6; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px;">
                            Dimensões para cálculo de Frete
                        </span>
                    </div>

                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">peso (kg) — ex: 0.800</label>
                            <input type="number" step="0.001" name="peso" placeholder="0.000" class="input-mora" required>
                        </div>
                        <div class="card-form">
                            <label class="label-mora">largura (cm)</label>
                            <input type="number" name="largura" placeholder="0" class="input-mora" required>
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="card-form">
                            <label class="label-mora">altura (cm)</label>
                            <input type="number" name="altura" placeholder="0" class="input-mora" required>
                        </div>
                        <div class="card-form">
                            <label class="label-mora">comprimento (cm)</label>
                            <input type="number" name="comprimento" placeholder="0" class="input-mora" required>
                        </div>
                    </div>

                    <div class="card-form">
                        <div class="form-row-between">
                            <div class="form-col-45">
                                <label class="label-mora">categoria</label>
                                <select name="categoria_id" class="input-mora" required>
                                    <option value="">selecione uma categoria</option>
                                    @foreach($categorias as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-col-45">
                                <label class="label-mora">status de destaque</label>
                                <label class="switch-wrapper" for="check_lancamento">
                                    <input type="checkbox" name="lancamento" value="1" id="check_lancamento" class="switch-input">
                                    <div class="switch-button"></div>
                                    <span class="label-mora label-switch">definir como lançamento</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-save-mora">
                        salvar produto
                    </button>
                </form>
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
