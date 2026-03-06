
@extends('layouts.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/novo-produto.css') }}">
@endpush

@section('content')
<div class="admin-body-novo"> {{-- Usa a classe do fundo branco sólido --}}
    <main class="novo-container">
        <header class="novo-header">
            <h1>editar produto: {{ $produto->nome }}</h1>
        </header>

        <form action="{{ route('admin.produto.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- OBRIGATÓRIO PARA UPDATES --}}

            <div class="card-form">
                <label class="label-mora">foto atual</label>
                <img src="{{ asset('storage/' . $produto->imagem) }}" style="width: 100px; border-radius: 8px; margin-bottom: 15px;">
                <div class="upload-placeholder">
                    <input type="file" name="imagem">
                    <p style="font-size: 0.6rem; color: #888; margin-top: 10px;">DEIXE VAZIO PARA MANTER A FOTO ATUAL</p>
                </div>
            </div>

            <div class="card-form">
                <label class="label-mora">nome do item</label>
                <input type="text" name="nome" value="{{ $produto->nome }}" class="input-mora" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="card-form">
                    <label class="label-mora">valor (r$)</label>
                    <input type="number" step="0.01" name="preco" value="{{ $produto->preco }}" class="input-mora" required>
                </div>
                <div class="card-form">
                    <label class="label-mora">estoque atual</label>
                    <input type="number" name="estoque" value="{{ $produto->estoque }}" class="input-mora" required>
                </div>
            </div>

            <button type="submit" class="btn-save-mora">
                salvar alterações
            </button>
        </form>
    </main>
</div>
@endsection