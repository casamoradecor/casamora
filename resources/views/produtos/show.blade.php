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
                        <input type="text" id="cep-destino" placeholder="00000-000" maxlength="9"
                               class="input-frete">
                        <button type="button" onclick="calcularFreteProduto({{ $produto->id }})"
                                class="btn botao-calc-frete"
                        >
                            Calcular
                        </button>
                    </div>

                    <div id="resultado-frete" style="margin-top: 15px; display: none;"></div>
                </div>

            </div>
        </div> @if(isset($produtosRelacionados) && $produtosRelacionados->count() > 0)
            <div class="related-products-section">
                <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
                    <h3 style="text-align: center; margin-bottom: 40px; text-transform: uppercase; font-size: 1.1rem; letter-spacing: 2px; color: #4B3621; font-family: 'Poppins', sans-serif;">
                        Produtos relacionados comprados pelos clientes
                    </h3>

                    <div class="destaque-carrossel">
                        <div class="carrossel-track">
                            @foreach($produtosRelacionados as $relacionado)
                                @php
                                    $imgRelacionado = $relacionado->imagem;
                                    $urlRelacionado = ($imgRelacionado && str_contains($imgRelacionado, 'assets'))
                                        ? asset(ltrim($imgRelacionado, '/'))
                                        : ($imgRelacionado ? Storage::url($imgRelacionado) : asset('assets/vasomora.png'));
                                @endphp

                                <div class="produto-card">
                                    <a href="{{ route('produto.show', $relacionado->id) }}" style="text-decoration: none;">
                                        <img src="{{ $urlRelacionado }}" alt="{{ $relacionado->nome }}">
                                    </a>

                                    <div class="produto-info">
                                        <h3 class="produto-titulo">{{ $relacionado->nome }}</h3>
                                        <p class="produto-preco">R$ {{ number_format($relacionado->preco, 2, ',', '.') }}</p>
                                    </div>

                                    <a href="{{ route('produto.show', $relacionado->id) }}" class="btn btn-branco">
                                        ver mais
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @push('js')
        <script src="{{ asset('js/produto-detalhe.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const cepInput = document.getElementById('cep-destino');

                if (cepInput) {
                    cepInput.addEventListener('input', function (e) {
                        let valor = e.target.value.replace(/\D/g, '');
                        if (valor.length > 5) {
                            valor = valor.replace(/^(\d{5})(\d)/, '$1-$2');
                        }
                        e.target.value = valor;
                    });
                }
            });</script>
    @endpush
@endsection
