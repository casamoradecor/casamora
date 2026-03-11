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
            <div class="novo-container">
                <form action="{{ route('admin.produto.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="card-form">
                        <label class="label-mora">fotos do produto</label>
                        <div class="upload-placeholder">
                            <input type="file" name="imagem" required>
                        </div>
                    </div>

                    <div class="card-form">
                        <label class="label-mora">nome do item</label>
                        <input type="text" name="nome" placeholder="ex: vaso de cerâmica morá" class="input-mora" required>

                        <label class="label-mora margin-top-20">descrição detalhada</label>
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
                            <div class="form-col-45 checkbox-group">
                                <input type="checkbox" name="lancamento" value="1" id="check_lancamento" class="checkbox-mora">
                                <label for="check_lancamento" class="label-mora">
                                    definir como lançamento
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
@endsection
