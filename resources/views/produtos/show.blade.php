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

                    <div id="resultado-frete" style="margin-top: 15px; display: none;">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function ajustarQtd(valor) {
            const campo = document.getElementById('qtd-produto');
            let novaQtd = parseInt(campo.value) + valor;
            if (novaQtd >= 1) campo.value = novaQtd;
        }

        function adicionarComQtd(irParaCheckout) {
            const qtd = document.getElementById('qtd-produto').value;
            const produtoId = "{{ $produto->id }}";
            const urlAdicionar = document.querySelector('meta[name="carrinho-url"]').content;
            const token = document.querySelector('meta[name="csrf-token"]').content;

            fetch(urlAdicionar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({produto_id: produtoId, quantidade: qtd})
            })
                .then(response => response.json())
                .then(data => {
                    if (irParaCheckout) {
                        window.location.href = "{{ route('checkout') }}";
                    } else {
                        if (typeof CarrinhoManager !== 'undefined') {
                            const atualizador = new CarrinhoManager();
                            const sidebar = document.getElementById('carrinhoSidebar');
                            const overlay = document.getElementById('carrinhoOverlay');
                            if (sidebar) sidebar.classList.add('aberto');
                            if (overlay) overlay.classList.add('ativo');
                        } else {
                            location.reload();
                        }
                    }
                })
                .catch(error => console.error('Erro:', error));
        }

        function calcularFrete() {
            const cep = document.getElementById('cep-destino').value;
            const resultadoDiv = document.getElementById('resultado-frete');
            const produtoId = "{{ $produto->id }}";

            if (cep.length < 8) {
                alert('Por favor, digite um CEP válido.');
                return;
            }

            resultadoDiv.style.display = 'block';
            resultadoDiv.innerHTML = '<p style="font-size: 0.8rem; color: #999;"><i class="fa-solid fa-spinner fa-spin"></i> Consultando prazos...</p>';

            fetch(`/frete/calcular?cep=${cep}&produto_id=${produtoId}`)
                .then(response => response.json())
                .then(data => {
                    resultadoDiv.innerHTML = '';

                    if (data.length === 0) {
                        resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.8rem;">Não encontramos frete para este CEP.</p>';
                        return;
                    }

                    data.forEach(opcao => {
                        resultadoDiv.innerHTML += `
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border: 1px solid #f0ece9; margin-top: 10px; border-radius: 8px; background: #fff;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <i class="fa-solid ${opcao.icone}" style="color: #4a3427; font-size: 1.2rem;"></i>
                                    <div>
                                        <strong style="display: block; font-size: 0.8rem; text-transform: uppercase; color: #4a3427;">${opcao.nome}</strong>
                                        <small style="color: #999;">${opcao.prazo}</small>
                                    </div>
                                </div>
                                <span style="font-weight: 700; color: #4a3427;text-transform: uppercase">R$ ${opcao.preco}</span>
                            </div>
                        `;
                    });
                })
                .catch(error => {
                    resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.8rem;">Erro ao conectar com o servidor.</p>';
                });
        }
    </script>
@endsection
