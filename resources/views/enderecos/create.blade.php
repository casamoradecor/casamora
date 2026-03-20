@extends('layouts.app')

@section('title', 'NOVO ENDEREÇO — CASA MORÁ')

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
                <h2>NOVO ENDEREÇO</h2>
                <a href="{{ route('enderecos.index') }}" class="btn-pill">
                    voltar para lista
                </a>
            </header>

            <form action="{{ route('enderecos.store') }}" method="POST" class="address-form info-card">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>CEP</label>
                        <input type="text" name="cep" id="cep" placeholder="00000-000" required>
                    </div>
                    <div class="form-group">
                        <label>ESTADO (UF)</label>
                        <input type="text" name="estado" id="estado" placeholder="EX: SP" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>LOGRADOURO (RUA)</label>
                    <input type="text" name="logradouro" id="logradouro" placeholder="NOME DA RUA OU AVENIDA" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>NÚMERO</label>
                        <input type="text" name="numero" placeholder="123" required>
                    </div>
                    <div class="form-group">
                        <label>COMPLEMENTO (OPCIONAL)</label>
                        <input type="text" name="complemento" placeholder="APTO, BLOCO, ETC.">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>BAIRRO</label>
                        <input type="text" name="bairro" id="bairro" placeholder="NOME DO BAIRRO" required>
                    </div>
                    <div class="form-group">
                        <label>CIDADE</label>
                        <input type="text" name="cidade" id="cidade" placeholder="NOME DA CIDADE" required>
                    </div>
                </div>

                <div class="form-submit">
                    <button type="submit" class="btn-pill full-width">salvar endereço de entrega</button>
                </div>
            </form>
        </section>
    </main>
@endsection
