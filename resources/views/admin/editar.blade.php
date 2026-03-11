@extends('layouts.app')

@section('title', 'CASA MORÁ — EDITAR')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/novo-produto.css') }}">
@endpush

@section('header_class', 'admin-hidden')

@section('content')
    <div class="admin-wrapper admin-body-novo">

        @include('sidebar.menu_lateral')

        <main class="admin-main">
            <div class="novo-container">
                <form action="{{ route('admin.produto.update', $produto->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-form">
                        <label class="label-mora">foto atual</label>
                        <img src="{{ asset('storage/' . $produto->imagem) }}" class="edit-img-preview">

                        <div class="upload-placeholder">
                            <input type="file" name="imagem">
                            <p class="info-helper-text">deixe vazio para manter a foto atual</p>
                        </div>
                    </div>

                    <div class="card-form">
                        <label class="label-mora">nome do item</label>
                        <input type="text" name="nome" value="{{ $produto->nome }}" class="input-mora" required>
                        <label class="label-mora margin-top-20">descrição detalhada</label>
                        <textarea name="descricao" rows="5" class="input-mora" placeholder="DETALHES TÉCNICOS E ESTILO...">{{ $produto->descricao }}</textarea>
                    </div>


                    <div class="form-grid-2">
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
            </div>
        </main>
    </div>
@endsection
