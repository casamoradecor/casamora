@extends('layouts.app')

@section('title', 'CASA MORÁ — ADICIONAR PRODUTO')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/create-admin.css') }}">
@endpush

@section('content')
<div class="admin-body-full">
    <main class="admin-main-create">
        
        <header class="admin-header-create">
            <h1>adicionar novo produto</h1>        
        </header>

        <form action="{{ route('admin.produto.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card-dashboard">
                <label class="admin-label">fotos do produto</label>
                <div class="upload-area">
                    <input type="file" name="imagem" required>
                </div>
            </div>

            <div class="card-dashboard">
                <label class="admin-label">nome do item</label>
                <input type="text" name="nome" placeholder="ex: vaso de cerâmica morá" class="admin-input" required>
                
                <label class="admin-label" style="margin-top: 25px;">descrição detalhada</label>
                <textarea name="descricao" rows="5" class="admin-input" placeholder="descreva as características e dimensões da peça..."></textarea>
            </div>

            <div class="grid-2-col">
                <div class="card-dashboard">
                    <label class="admin-label">valor de venda (r$)</label>
                    <input type="number" step="0.01" name="preco" class="admin-input" placeholder="0.00" required>
                </div>
                <div class="card-dashboard">
                    <label class="admin-label">quantidade em estoque</label>
                    <input type="number" name="estoque" class="admin-input" placeholder="0" required>
                </div>
            </div>

            <div class="card-dashboard">
                <label class="admin-label">selecione a categoria</label>
                <select name="categoria_id" class="admin-input" required>
                    <option value="1">vasos</option>
                    <option value="2">utensílios</option>
                    <option value="3">decorações</option>
                </select>
            </div>

            <div class="footer-actions">
                <button type="submit" class="btn-save-large">
                    salvar alterações
                </button>
            </div>
        </form>
    </main>
</div>
@endsection