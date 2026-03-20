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
        </aside>

        <section class="dashboard-content">
            <header class="dashboard-header">
                <h2>EDITAR ENDEREÇO</h2>
                <a href="{{ route('enderecos.index') }}" class="btn-add-mora" style="text-transform: lowercase;">
                    cancelar edição
                </a>
            </header>

            <form action="{{ route('enderecos.update', $endereco->id) }}" method="POST" class="info-card">
                @csrf
                @method('PUT')

                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="card-form">
                        <label class="cep-label">CEP</label>
                        <input type="text" name="cep" id="cep" class="input-mora" value="{{ $endereco->cep }}" required>
                    </div>
                    <div class="card-form">
                        <label class="cep-label">ESTADO (UF)</label>
                        <input type="text" name="estado" id="estado" class="input-mora" value="{{ $endereco->estado }}" required>
                    </div>
                </div>

                <div class="card-form" style="margin-bottom: 20px;">
                    <label class="cep-label">LOGRADOURO (RUA)</label>
                    <input type="text" name="logradouro" id="logradouro" class="input-mora" value="{{ $endereco->logradouro }}" required>
                </div>

                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="card-form">
                        <label class="cep-label">NÚMERO</label>
                        <input type="text" name="numero" class="input-mora" value="{{ $endereco->numero }}" required>
                    </div>
                    <div class="card-form">
                        <label class="cep-label">COMPLEMENTO (OPCIONAL)</label>
                        <input type="text" name="complemento" class="input-mora" value="{{ $endereco->complemento }}">
                    </div>
                </div>

                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div class="card-form">
                        <label class="cep-label">BAIRRO</label>
                        <input type="text" name="bairro" id="bairro" class="input-mora" value="{{ $endereco->bairro }}" required>
                    </div>
                    <div class="card-form">
                        <label class="cep-label">CIDADE</label>
                        <input type="text" name="cidade" id="cidade" class="input-mora" value="{{ $endereco->cidade }}" required>
                    </div>
                </div>

                <button type="submit" class="btn-add-mora" style="width: 100%; justify-content: center; font-size: 1rem; border: none; cursor: pointer;">
                    atualizar endereço
                </button>
            </form>
        </section>
    </main>
@endsection
