@extends('layouts.app')

@section('title', 'CASA MORÁ — ADICIONAR')

@push('css')
    {{-- CHAMADA DO CSS EXCLUSIVO --}}
    <link rel="stylesheet" href="{{ asset('css/novo-produto.css') }}">
@endpush

@section('content')
<div class="admin-body-novo">
    <main class="novo-container">
        
        <header class="novo-header">
            <h1>adicionar novo produto</h1>
        </header>

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
                
                <label class="label-mora" style="margin-top: 20px;">descrição detalhada</label>
                <textarea name="descricao" rows="5" class="input-mora" placeholder="detalhes técnicos e estilo..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
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
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div style="width: 45%;">
                        <label class="label-mora">categoria</label>
                        <select name="categoria_id" class="input-mora">
                            <option value="1">vasos</option>
                            <option value="2">decoração</option>
                        </select>
                    </div>
                    <div style="width: 45%; display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="lancamento" value="1" id="check_lancamento" style="width: 20px; height: 20px; accent-color: #3b1f15;">
                        <label for="check_lancamento" class="label-mora" style="margin-bottom: 0; cursor: pointer;">
                            definir como lançamento
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-save-mora">
                salvar produto
            </button>
        </form>
    </main>
</div>
@endsection