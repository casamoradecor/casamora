@extends('layouts.app')

@section('title', 'Política de Privacidade — Casa MORÁ')
@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/institucional.css') }}">
@endpush

@section('content')
    <div class="institucional-container">
        <div class="institucional-header">
            <h1>Política de Privacidade</h1>
            <p>Última atualização: Junho de 2026</p>
        </div>

        <div class="institucional-content">
            <section>
                <h2>1. Coleta de Informações</h2>
                <p>A Casa MORÁ tem o compromisso de proteger a sua privacidade. Coletamos informações estritamente necessárias para o processamento de pedidos, tais como seu nome, CPF, e-mail, telefone e endereço de entrega.</p>
            </section>

            <section>
                <h2>2. Uso dos Dados</h2>
                <p>Seus dados são utilizados exclusivamente para as seguintes finalidades:</p>
                <ul>
                    <li>Processamento, faturamento e envio de seus pedidos.</li>
                    <li>Comunicação automatizada sobre atualizações do status de entrega.</li>
                    <li>Envio de novidades e ofertas exclusivas através da nossa newsletter (caso previamente autorizado).</li>
                </ul>
            </section>

            <section>
                <h2>3. Segurança e Compartilhamento</h2>
                <p>Não comercializamos seus dados pessoais. O compartilhamento com terceiros ocorre de forma automatizada apenas com parceiros logísticos essenciais (como o Melhor Envio/Jadlog) e gateways de pagamento (Mercado Pago), com o único objetivo de viabilizar a conclusão da sua compra.</p>
            </section>
        </div>
    </div>
@endsection
