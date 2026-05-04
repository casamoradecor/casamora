@extends('layouts.app')
@section('title', 'CASA MORÁ — EDITOR VISUAL')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/edit-mode.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sobre-nos.css') }}">
@endpush

@section('content')
    <header class="admin-header">
        <div class="header-content" style="margin-left: 260px;">

        </div>
    </header>

    <main class="admin-visual-editor">
        <div class="container">
            <form action="{{ route('admin.sobre.updateTexto') }}" method="POST">
                @csrf
                <section class="secao-zigzag">
                    <div class="img-container edit-container">
                        <span class="label-sessao">Foto Principal</span>
                        <img src="{{ $conteudo->imagem_1 ? Storage::url($conteudo->imagem_1) : asset('assets/placeholder.png') }}">
                        <div class="edit-overlay">
                            <label class="btn-edit-label" onclick="document.getElementById('upload-foto-1').click()">
                                <i class="fa-solid fa-camera"></i> trocar foto 1
                            </label>
                        </div>
                    </div>

                    <div class="text-container">
                        <span class="label-sessao">Texto de Introdução</span>
                        <textarea name="texto_1" class="input-mora" style="height: 200px;">{{ $conteudo->texto_1 }}</textarea>
                    </div>
                </section>

                <section class="secao-zigzag flex-reverse">
                    <div class="img-container edit-container">
                        <span class="label-sessao">Foto Secundária</span>
                        <img src="{{ $conteudo->imagem_2 ? Storage::url($conteudo->imagem_2) : asset('assets/placeholder.png') }}">
                        <div class="edit-overlay">
                            <label class="btn-edit-label" onclick="document.getElementById('upload-foto-2').click()">
                                <i class="fa-solid fa-camera"></i> trocar foto 2
                            </label>
                        </div>
                    </div>

                    <div class="text-container">
                        <span class="label-sessao">Texto Final</span>
                        <textarea name="texto_2" class="input-mora" style="height: 200px;">{{ $conteudo->texto_2 }}</textarea>
                    </div>
                </section>

                <div class="admin-actions-footer">
                    <button type="submit" class="btn btn-marrom">
                        salvar todas as alterações
                    </button>
                </div>
            </form>

            <form id="form-foto-1" action="{{ route('admin.sobre.updateFoto', 1) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="upload-foto-1" name="foto" onchange="this.form.submit()">
            </form>
            <form id="form-foto-2" action="{{ route('admin.sobre.updateFoto', 2) }}" method="POST" enctype="multipart/form-data" style="display: none;">
                @csrf
                <input type="file" id="upload-foto-2" name="foto" onchange="this.form.submit()">
            </form>
        </div>
    </main>
@endsection
