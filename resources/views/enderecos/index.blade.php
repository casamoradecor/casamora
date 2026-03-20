@extends('layouts.app')

@section('title', 'MEUS ENDEREÇOS — CASA MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/endereco.css') }}">
@endpush

@section('content')
    <main class="dashboard-container">
        <aside class="dashboard-nav">
            <a href="{{ route('dashboard') }}">RESUMO</a>
            <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
            <a href="{{ route('enderecos.index') }}" class="active">ENDEREÇOS</a>
            <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">SAIR DA CONTA</button>
            </form>
        </aside>

        <section class="dashboard-content">
            <header class="dashboard-header">
                <h2>MEUS ENDEREÇOS</h2>
                <a href="{{ route('enderecos.create') }}" class="btn-add-mora">
                    <i class="fa-solid fa-plus"></i> adicionar novo endereço
                </a>
            </header>

            <p>GERENCIE SEUS ENDEREÇOS DE ENTREGA CADASTRADOS NA CASA MORÁ.</p>

            @if($enderecos->isEmpty())
                <div class="info-card">
                    <p>VOCÊ AINDA NÃO POSSUI ENDEREÇOS SALVOS.</p>
                </div>
            @else
                <table class="tabela-pedidos">
                    <thead>
                    <tr>
                        <th>ENDEREÇO</th>
                        <th>BAIRRO / CIDADE</th>
                        <th class="text-center">AÇÕES</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($enderecos as $e)
                        <tr>
                            <td>
                                <strong>RUA: {{ $e->logradouro }}, {{ $e->numero }}</strong><br>
                                <span class="cep-label">CEP: {{ $e->cep }}</span>
                            </td>
                            <td>{{ $e->bairro }} - {{ $e->cidade }}/{{ $e->estado }}</td>
                            <td class="text-center">
                                <div class="actions-flex">
                                    <a href="{{ route('enderecos.edit', $e->id) }}" class="btn-action-minimal">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button type="button" class="btn-action-minimal trash" onclick="openDeleteModal({{ $e->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    </main>

    <div id="deleteModal" class="modal-mora">
        <div class="modal-content">
            <h3>TEM CERTEZA?</h3>
            <p>ESTA AÇÃO REMOVERÁ O ENDEREÇO PERMANENTEMENTE.</p>

            <form id="formDelete" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">CANCELAR</button>
                    <button type="submit" class="btn-confirm">EXCLUIR</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal(id) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('formDelete');
            form.action = '/meus-enderecos/' + id;
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
@endsection
