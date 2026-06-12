@extends('layouts.app')

@section('title', 'Termos de Uso — Casa MORÁ')
@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
@endpush

@section('content')
    <div class="institucional-container">
        <div class="institucional-header">
            <h1>Termos de Uso</h1>
            <p>Condições gerais de navegação e compra</p>
        </div>

        <div class="institucional-content">
            <section>
                <h2>1. Aceitação dos Termos</h2>
                <p>Ao navegar e realizar compras no site da Casa MORÁ, você concorda expressamente com as condições e termos estipulados neste documento. Se você não concordar com qualquer parte destes termos, recomendamos não prosseguir com a utilização do site.</p>
            </section>

            <section>
                <h2>2. Propriedade Intelectual</h2>
                <p>Todo o conteúdo visual, fotografias de produtos, designs de páginas, logotipos e textos apresentados neste ecossistema são de propriedade exclusiva da Casa MORÁ. A reprodução ou distribuição não autorizada é estritamente proibida.</p>
            </section>

            <section>
                <h2>3. Precisão das Informações</h2>
                <p>Esforçamo-nos para que as cores, acabamentos e texturas de nossos produtos de decoração sejam exibidos com a maior fidelidade possível. Contudo, variações de tonalidade podem ocorrer devido às calibrações de monitores e telas de dispositivos móveis.</p>
            </section>
        </div>
    </div>
@endsection
