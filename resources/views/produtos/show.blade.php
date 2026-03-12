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
                    <div class="descricao-produto" style="margin-top: 30px;">
                        <h3 style="font-family: 'Poppins', sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px; color: #4a3427; margin-bottom: 10px;">
                            Sobre o item
                        </h3>
                        <div
                            style="font-family: 'Poppins', sans-serif; font-size: 0.9rem; line-height: 1.6; color: #666; text-align: justify;">
                            {!! nl2br(e($produto->descricao)) !!}
                        </div>
                    </div>

                    <div class="especificacoes-produto"
                         style="margin-top: 30px; background: #fafafa; padding: 15px; border-radius: 4px;">
                        <h3 style="font-family: 'Poppins', sans-serif; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #4a3427; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                            Especificações Técnicas
                        </h3>
                        <div
                            style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-family: 'Poppins', sans-serif; font-size: 0.8rem; color: #777;">
                            <span><strong>Peso:</strong> {{ number_format($produto->peso, 3, ',', '.') }} kg</span>
                            <span><strong>Altura:</strong> {{ $produto->altura }} cm</span>
                            <span><strong>Largura:</strong> {{ $produto->largura }} cm</span>
                            <span><strong>Comprimento:</strong> {{ $produto->comprimento }} cm</span>
                        </div>
                    </div>

                    <div class="coluna-botoes">
                        <button type="button" class="botao-mora-secundario" id="btn-add-carrinho"
                                onclick="adicionarComQtd(false)"
                                data-id="{{ $produto->id }}"
                                data-nome="{{ $produto->nome }}"
                                data-preco="{{ $produto->preco }}"
                                data-imagem="{{ $urlFinal }}">
                            adicionar ao carrinho
                        </button>

                        <button type="button" class="botao-mora-primario" id="btn-finalizar-agora"
                                onclick="adicionarComQtd(true)">
                            finalizar compra
                        </button>
                    </div>
                </div>

                <div class="calculadora-frete" style="margin-top: 35px; border-top: 1px solid #eee; padding-top: 25px;">
                    <label
                        style="font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #4a3427; display: block; margin-bottom: 10px;">
                        <i class="fa-solid fa-truck-fast"></i> Calcular Frete e Prazo
                    </label>
                    <div class="grupo-input-frete" style="display: flex; gap: 8px;">
                        <input type="text" id="cep-destino" placeholder="00000-000" maxlength="9"
                               style="flex: 1; padding: 12px; border: 1px solid #ddd; outline: none; font-size: 0.85rem;">
                        <button type="button" onclick="calcularFrete()" class="botao-calc-frete"
                                style="background: #4a3427; color: #fff; border: none; padding: 0 20px; text-transform: uppercase; font-size: 0.7rem; font-weight: 700; cursor: pointer;">
                            Calcular
                        </button>
                    </div>

                    <div id="resultado-frete" style="margin-top: 15px; display: none;"></div>
                </div>

            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/carrinho.js') }}"></script>
        <script src="{{ asset('js/produto-detalhe.js') }}"></script>
        <script src="{{ asset('js/frete.js') }}"></script>
    @endpush
@endsection
