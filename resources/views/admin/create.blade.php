@extends('layouts.app')

@section('title', 'Gestão de Produtos — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/create-admin.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body">
        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <header class="admin-header-list">
                <h1>Produtos</h1>
                <span class="header-info-label">Gestão de Estoque</span>
            </header>

            <div class="admin-header-list" style="flex-direction: row; margin-bottom: 30px;">
                <a href="{{ route('admin.produtos.novo') }}" class="btn-add-mora">
                    <i class="fa-solid fa-plus"></i> adicionar produto
                </a>
            </div>

            <div class="table-card">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>código</th>
                        <th>produto</th>
                        <th>estoque</th>
                        <th>preço</th>
                        <th>lançamento</th>
                        <th>ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($produtos as $produto)
                        <tr>
                            <td>
                                <span class="sku-label">{{ $produto->codigo ?? 'S/C' }}</span>
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="{{ asset('storage/' . $produto->imagem) }}" class="product-img">
                                    <strong>{{ $produto->nome }}</strong>
                                </div>
                            </td>
                            <td>{{ $produto->estoque }} un</td>
                            <td class="price-bold">r$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('admin.produto.toggle-lancamento', $produto->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-launch {{ $produto->lancamento ? 'active' : '' }}">
                                        {{ $produto->lancamento ? 'no carrossel' : 'ativar' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div class="actions-flex">
                                    <a href="{{ route('admin.produtos.edit', $produto->id) }}" class="btn-action-minimal">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn-action-minimal trash" onclick="abrirModalExclusao('{{ $produto->id }}', '{{ $produto->nome }}')">
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

    {{-- MODAL --}}
    <div id="modalDelete" class="modal-overlay">
        <div class="modal-box">
            <h2>confirmar exclusão</h2>
            <p>tem certeza que deseja excluir o produto:<br>
                <strong id="nomeProdutoModal" class="modal-item-highlight"></strong>
            </p>
            <div class="actions-flex" style="justify-content: center; margin-top: 20px;">
                <button onclick="fecharModal()" class="btn-modal btn-cancel">cancelar</button>
                <form id="formDelete" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal btn-confirm">excluir agora</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalExclusao(id, nome) {
            document.getElementById('nomeProdutoModal').innerText = nome.toUpperCase();
            document.getElementById('formDelete').action = "/admin/produto/" + id;
            document.getElementById('modalDelete').style.display = 'flex';
        }

        function fecharModal() {
            document.getElementById('modalDelete').style.display = 'none';
        }
    </script>
@endsection
