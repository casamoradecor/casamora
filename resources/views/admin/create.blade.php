@extends('layouts.app')

@section('title', 'CASA MORÁ — PRODUTOS')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/create-admin.css') }}">
    
@endpush

@section('content')
<div class="admin-body-full">
    <main class="admin-main-create">
        <header class="admin-header-list">
            <div>
                <h1>produtos</h1>
            </div>
            <a href="{{ route('admin.produtos.novo') }}" class="btn-add-mora">
                <i class="fa-solid fa-plus"></i> adicionar produto
            </a>
        </header>

        <div class="table-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox"></th>
                        <th>produto</th>
                        <th>estoque</th>
                        <th>preço</th>
                        <th>lançamento</th>
                        <th width="100">ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produtos as $produto)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="{{ asset('storage/' . $produto->imagem) }}" class="product-img">
                                <strong>{{ $produto->nome }}</strong>
                            </div>
                        </td>
                        <td>{{ $produto->estoque }} un</td>
                        <td>r$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('admin.produto.toggle-lancamento', $produto->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-launch {{ $produto->lancamento ? 'active' : '' }}">
                                    {{ $produto->lancamento ? 'no carrossel' : 'ativar' }}
                                </button>
                            </form>
                        </td>
                        <td>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <a href="{{ route('admin.produtos.edit', $produto->id) }}" style="color: #3b1f15;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                {{-- BOTÃO QUE ABRE O MODAL --}}
                                <button type="button" onclick="abrirModalExclusao('{{ $produto->id }}', '{{ $produto->nome }}')" 
                                        style="background:none; border:none; color:#ff4d4d; cursor:pointer;">
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

{{-- ESTRUTURA DO MODAL --}}
<div id="modalDelete" class="modal-overlay">
    <div class="modal-box">
        <h2 style="font-family: 'Playfair Display'; color: #3b1f15;">confirmar exclusão</h2>
        <p style="margin: 20px 0; color: #666; font-size: 0.9rem;">
            tem certeza que deseja excluir o produto:<br>
            <strong id="nomeProdutoModal" style="color: #3b1f15; font-size: 1.1rem; display: block; margin-top: 10px;"></strong>
        </p>
        <div style="display: flex; gap: 10px; justify-content: center;">
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
        // Coloca o nome do produto no modal
        document.getElementById('nomeProdutoModal').innerText = nome.toUpperCase();
        
        // Define a URL correta de exclusão
        document.getElementById('formDelete').action = "/admin/produto/" + id;
        
        // Mostra o modal
        document.getElementById('modalDelete').style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modalDelete').style.display = 'none';
    }

    // Fecha se clicar fora da caixa
    window.onclick = function(event) {
        if (event.target == document.getElementById('modalDelete')) {
            fecharModal();
        }
    }
</script>
@endsection