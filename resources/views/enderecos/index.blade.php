@extends('layouts.app')

@section('title', 'MEUS ENDERECOS — CASA MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/endereco.css') }}">
@endpush

@section('content')
    <main class="dashboard-container">
        <aside class="dashboard-nav">
            <a href="{{ route('dashboard') }}" style="text-decoration: underline;">RESUMO</a>
            <a href="{{ route('pedidos.index') }}">MEUS PEDIDOS</a>
            <a href="{{ route('enderecos.index') }}">ENDEREÇOS</a>
            <a href="{{ route('perfil.edit') }}">EDITAR PERFIL</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">SAIR DA CONTA</button>
            </form>
        </aside>

        <section class="dashboard-content">
            {{-- CABEÇALHO COM BOTÃO ADICIONAR --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2>MEUS ENDEREÇOS</h2>
                <a href="{{ route('enderecos.create') }}" class="btn-novo-endereco">+ NOVO ENDEREÇO</a>
            </div>

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
                        <th style="text-align: center;">AÇÕES</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($enderecos as $e)
                        <tr>
                            <td>
                                <strong>RUA: {{ $e->logradouro }}, {{ $e->numero }}</strong><br>
                                <span style="font-size: 0.7rem; color: #888;">CEP: {{ $e->cep }}</span>
                            </td>
                            <td>{{ $e->bairro }} - {{ $e->cidade }}/{{ $e->estado }}</td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 15px; justify-content: center; align-items: center;">
                                    {{-- BOTÃO EDITAR --}}
                                    <a href="{{ route('enderecos.edit', $e->id) }}" class="link-tabela">EDITAR</a>

                                    {{-- BOTÃO EXCLUIR QUE ABRE O MODAL --}}
                                    <button type="button" class="link-tabela excluir"
                                            onclick="openDeleteModal({{ $e->id }})"
                                            style="background:none; border:none; cursor:pointer; color:#CC0000; font-weight: 800; font-size: 0.7rem; text-decoration: underline;">
                                        EXCLUIR
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

    {{-- MODAL DE EXCLUSÃO CUSTOMIZADO --}}
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
            // Ajusta a rota do form dinamicamente
            form.action = '/meus-enderecos/' + id;
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Fecha o modal se clicar fora da caixa branca
        window.onclick = function (event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
@endsection
