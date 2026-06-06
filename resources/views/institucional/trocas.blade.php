@extends('layouts.app')

@section('title', 'Trocas e Devoluções — Casa MORÁ')
@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
@endpush

@section('content')
    <div class="institucional-container">
        <div class="institucional-header">
            <h1>Trocas e Devoluções</h1>
            <p>Sua satisfação em primeiro lugar</p>
        </div>

        <div class="institucional-content">
            <section>
                <h2>1. Direito de Arrependimento</h2>
                <p>Em conformidade com o Código de Defesa do Consumidor, o cliente tem o prazo de até 7 (sete) dias corridos, contados a partir da data de recebimento do produto, para solicitar a devolução por arrependimento.</p>
            </section>

            <section>
                <h2>2. Condições do Produto</h2>
                <p>Para que a devolução ou troca seja aceita, o produto da Casa MORÁ deverá atender às seguintes exigências:</p>
                <ul>
                    <li>Estar em sua embalagem original, sem indícios de uso ou avaria.</li>
                    <li>Estar acompanhado de todos os seus acessórios e manuais.</li>
                    <li>Estar acompanhado da Nota Fiscal ou Declaração de Conteúdo utilizada no envio.</li>
                </ul>
            </section>

            <section>
                <h2>3. Procedimento Logístico</h2>
                <p>Para iniciar o processo, entre em contato através do nosso e-mail oficial (casa.mora.decora@gmail.com). Após a análise da solicitação, forneceremos um código de postagem para o retorno da mercadoria sem custos logísticos para a primeira troca.</p>
            </section>
        </div>
    </div>
@endsection
