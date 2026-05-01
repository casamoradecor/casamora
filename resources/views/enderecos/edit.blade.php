@extends('layouts.app')

@section('title', 'EDITAR ENDEREÇO — CASA MORÁ')

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
                <button type="submit" class="btn-logout" style="font-family: var(--font-base),sans-serif">SAIR DA CONTA</button>
            </form>
        </aside>

        <section class="dashboard-content">
            <header class="dashboard-header">
                <h2>EDITAR ENDEREÇO</h2>
                <a href="{{ route('enderecos.index') }}" class="btn btn-ghost">
                    cancelar edição
                </a>
            </header>

            <form action="{{ route('enderecos.update', $endereco->id) }}" method="POST" class="address-form info-card">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" name="cep" id="cep" value="{{ $endereco->cep }}" placeholder="00000-000" required maxlength="9">
                    </div>
                    <div class="form-group">
                        <label>ESTADO (UF)</label>
                        <input type="text" name="estado" id="estado" value="{{ $endereco->estado }}" placeholder="EX: SP" required maxlength="2">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>LOGRADOURO (RUA)</label>
                    <input type="text" name="logradouro" id="logradouro" value="{{ $endereco->logradouro }}" placeholder="NOME DA RUA OU AVENIDA" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NÚMERO</label>
                        <input type="text" name="numero" value="{{ $endereco->numero }}" placeholder="123" required>
                    </div>
                    <div class="form-group">
                        <label>COMPLEMENTO (OPCIONAL)</label>
                        <input type="text" name="complemento" value="{{ $endereco->complemento }}" placeholder="APTO, BLOCO, ETC.">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>BAIRRO</label>
                        <input type="text" name="bairro" id="bairro" value="{{ $endereco->bairro }}" placeholder="NOME DO BAIRRO" required>
                    </div>
                    <div class="form-group">
                        <label>CIDADE</label>
                        <input type="text" name="cidade" id="cidade" value="{{ $endereco->cidade }}" placeholder="NOME DA CIDADE" required>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn btn-marrom">atualizar endereço</button>
                </div>
            </form>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cepInput = document.getElementById('cep');

            // Máscara simples para o CEP (00000-000)
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.substring(0, 5) + '-' + value.substring(5, 8);
                }
                e.target.value = value;
            });

            // Consulta ao CEP
            cepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');

                if (cep.length === 8) {
                    const campos = ['logradouro', 'bairro', 'cidade', 'estado'];
                    campos.forEach(id => document.getElementById(id).value = '...');

                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('logradouro').value = data.logradouro;
                                document.getElementById('bairro').value = data.bairro;
                                document.getElementById('cidade').value = data.localidade;
                                document.getElementById('estado').value = data.uf;

                                document.getElementsByName('numero')[0].focus();
                            } else {
                                alert('CEP não encontrado.');
                                campos.forEach(id => document.getElementById(id).value = '');
                            }
                        })
                        .catch(error => {
                            console.error('Erro na consulta:', error);
                            campos.forEach(id => document.getElementById(id).value = '');
                        });
                }
            });
        });
    </script>
@endsection
