@extends('layouts.app')
@section('title', 'Sobre Nós — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/sobre-nos.css') }}">
@endpush

@section('content')
    <main class="sobre-nos-page">
        <div class="container">
            <h1 class="sobre-titulo">
                {{ $conteudo->titulo_header ?? 'Quem Somos?' }}
            </h1>

            <section class="secao-zigzag">
                <div class="sobre-img-container">
                    <img src="{{ $conteudo->imagem_1 ? Storage::url($conteudo->imagem_1) : asset('assets/placeholder.png') }}"
                         class="shadow-right" alt="Casa MORÁ - História">
                </div>
                <div class="sobre-text-container">
                    <p>{!! nl2br(e($conteudo->texto_1)) !!}</p>
                </div>
            </section>

            <section class="secao-zigzag flex-reverse">
                <div class="sobre-img-container">
                    <img src="{{ $conteudo->imagem_2 ? Storage::url($conteudo->imagem_2) : asset('assets/placeholder.png') }}"
                         class="shadow-left" alt="Casa MORÁ - Essência">
                </div>
                <div class="sobre-text-container">
                    <p>{!! nl2br(e($conteudo->texto_2)) !!}</p>
                </div>
            </section>
        </div>
    </main>
@endsection
