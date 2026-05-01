@extends('layouts.app')

@section('title', $produto->nome . ' — Casa MORÁ')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/produto-detalhe.css') }}">
@endpush

@section('content')
    <div class="pagina-produto-wrapper">
        <div class="container-detalhe-produto">

            <div class="galeria-produto">
                @php
                    $caminho = $produto->imagem;
                    if ($caminho && str_contains($caminho, 'assets')) {
                        $urlFinal = asset(ltrim($caminho, '/'));
                    } else {
                        $urlFinal = $caminho ? Storage::url($caminho) : asset('assets/vasomora.png');
                    }
                @endphp
                <img src="{{ $urlFinal }}" alt="{{ $produto->nome }}">
            </div>

            <div class="lado-info-produto">
                <a href="#" class="link-categoria-produto">{{ $produto->categoria->nome ?? 'coleção morada' }}</a>
                <h1>{{ $produto->nome }}</h1>

                <div class="preco-grande-produto">
                    R$ {{ number_format($produto->preco, 2, ',', '.') }}
                </div>

                <div class="seletor-quantidade">
                    <button type="button" class="botao-qtd" onclick="ajustarQtd(-1)">-</button>
                    <input type="number" id="qtd-produto" value="1" min="1" readonly>
                    <button type="button" class="botao-qtd" onclick="ajustarQtd(1)">+</button>
                </div>

                <div class="wrapper-acoes-compra">
                    <div class="descricao-produto">
                        <h3>
                            Sobre o item
                        </h3>
                        <div class="descricao-produto-texto">
                            {!! nl2br(e($produto->descricao)) !!}
                        </div>
                    </div>

                    <div class="especificacoes-produto">
                        <h3>
                            Especificações Técnicas
                        </h3>
                        <div class="especificacoes-grid">
                            <span><strong>Peso:</strong> {{ number_format($produto->peso, 3, ',', '.') }} kg</span>
                            <span><strong>Altura:</strong> {{ $produto->altura }} cm</span>
                            <span><strong>Largura:</strong> {{ $produto->largura }} cm</span>
                            <span><strong>Comprimento:</strong> {{ $produto->comprimento }} cm</span>
                        </div>
                    </div>

                    <div class="coluna-botoes">
                        <button type="button" class="btn btn-branco" id="btn-add-carrinho"
                                onclick="adicionarComQtd(false)"
                                data-id="{{ $produto->id }}"
                                data-nome="{{ $produto->nome }}"
                                data-preco="{{ $produto->preco }}"
                                data-imagem="{{ $urlFinal }}">
                            adicionar ao carrinho
                        </button>

                        <button type="button" class="btn btn-marrom" id="btn-finalizar-agora"
                                onclick="adicionarComQtd(true)">
                            finalizar compra
                        </button>
                    </div>
                </div>

                <div class="calculadora-frete">
                    <label>
                        <i class="fa-solid fa-truck-fast"></i> Calcular Frete e Prazo
                    </label>
                    <div class="grupo-input-frete">
                        <input type="text" id="cep-destino" placeholder="00000-000" maxlength="8"
                               class="input-frete">
                        <button type="button" onclick="calcularFreteProduto({{ $produto->id }})" class="btn botao-calc-frete"
                                >
                            Calcular
                        </button>
                    </div>

                    <div id="resultado-frete" style="margin-top: 15px; display: none;"></div>
                </div>

            </div>
        </div>
    </div>
    @push('js')
        <script src="{{ asset('js/produto-detalhe.js') }}"></script>
    @endpush
@endsection
