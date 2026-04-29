@extends('layouts.app')

@section('title', 'Finalizar Compra — Casa MORÁ')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
    <main class="checkout-container">
        @if(session('erro'))
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-family: 'Poppins', sans-serif; font-size: 0.85rem;">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('erro') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-family: 'Poppins', sans-serif; font-size: 0.85rem;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="checkout-form-section">
            <h2>Dados de Entrega</h2>

            @if(Auth::check() && Auth::user()->enderecos->count() > 0)
                <div class="form-group">
                    <label style="margin-bottom: 10px; display: block;">Selecione um endereço salvo ou digite um novo:</label>
                    <div class="enderecos-salvos-grid">
                        @foreach(Auth::user()->enderecos as $end)
                            <label class="card-endereco" id="label-end-{{ $end->id }}">
                                <input type="radio" name="endereco_selecionado" class="radio-endereco"
                                       data-cep="{{ $end->cep }}"
                                       data-rua="{{ $end->logradouro }}"
                                       data-numero="{{ $end->numero }}"
                                       data-bairro="{{ $end->bairro }}"
                                       data-complemento="{{ $end->complemento }}"
                                       data-cidade="{{ $end->cidade }}"
                                       data-estado="{{ $end->estado }}">
                                {{-- Mudado de rua para logradouro aqui também --}}
                                <strong>{{ $end->logradouro }}, {{ $end->numero }}</strong>
                                <span>{{ $end->bairro }}</span>
                                <span>{{ $end->cidade }}/{{ $end->estado }}</span>
                                <span>CEP: {{ $end->cep }}</span>
                            </label>
                        @endforeach

                        <label class="card-endereco active" id="label-novo">
                            <input type="radio" name="endereco_selecionado" value="novo" checked class="radio-endereco">
                            <strong>Outro endereço</strong>
                            <span>Preencha os campos abaixo</span>
                        </label>
                    </div>
                </div>
            @endif

            <form action="{{ route('pedido.finalizar') }}" method="POST" class="checkout-form" id="form-checkout">
                @csrf

                {{-- Inputs ocultos para o Frete --}}
                <input type="hidden" name="frete_escolhido" id="frete_escolhido_input" required>
                <input type="hidden" name="valor_frete" id="valor_frete_input" value="0">

                <div class="form-group">
                    <label>Nome para Entrega</label>
                    <input type="text" value="{{ Auth::user()->name ?? '' }}" class="input-checkout input-disabled" readonly>
                </div>

                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" value="{{ Auth::user()->cpf ?? '' }}" class="input-checkout input-disabled" readonly>
                </div>

                <div class="form-group">
                    <label>CEP</label>
                    <input type="text" name="cep" id="cep" placeholder="00000-000" class="input-checkout" required maxlength="9">
                </div>

                <div class="address-grid">
                    <div class="form-group flex-3">
                        <label>RUA</label>
                        <input type="text" name="rua" id="logradouro" placeholder="Nome da rua" class="input-checkout" required>
                    </div>
                    <div class="form-group flex-1">
                        <label>NÚMERO</label>
                        <input type="text" name="numero" id="numero" placeholder="123" class="input-checkout" required>
                    </div>
                </div>

                <div class="address-grid">
                    <div class="form-group">
                        <label>BAIRRO</label>
                        <input type="text" name="bairro" id="bairro" placeholder="Seu bairro" class="input-checkout" required>
                    </div>
                    <div class="form-group">
                        <label>COMPLEMENTO</label>
                        <input type="text" name="complemento" id="complemento" placeholder="Apt, bloco..." class="input-checkout">
                    </div>
                </div>

                <div class="address-grid">
                    <div class="form-group flex-2">
                        <label>CIDADE</label>
                        <input type="text" name="cidade" id="localidade" class="input-checkout" required readonly>
                    </div>
                    <div class="form-group flex-1">
                        <label>UF</label>
                        <input type="text" name="estado" id="uf" class="input-checkout" required readonly>
                    </div>
                </div>

                <div id="box-opcoes-frete" class="opcoes-frete-container" style="display: none;">
                    <h3 class="titulo-frete-checkout">Escolha o envio</h3>
                    <div id="lista-fretes-checkout"></div>
                </div>

                <button type="submit" class="btn-confirmar" id="btn-finalizar" disabled>
                    CONFIRMAR PEDIDO
                </button>
            </form>
        </div>

        <div class="checkout-resumo">
            <h2>Resumo do Pedido</h2>
            <div class="checkout-itens-lista">
                @php $totalGeral = 0; @endphp
                @foreach($carrinho as $id => $item)
                    @if(!isset($item['nome']) || $item['nome'] === 'NULL') @continue @endif
                    @php
                        $imagemSrc = $item['imagem'] ?? asset('assets/vasomora.png');
                        $totalGeral += $item['preco'] * $item['quantidade'];
                    @endphp
                    <div class="checkout-item">
                        <img src="{{ $imagemSrc }}" alt="{{ $item['nome'] }}" style="border-radius: 4px;">
                        <div class="checkout-item-info">
                            <h4 style="text-transform: uppercase; font-size: 0.8rem; line-height: 1.2; margin-bottom: 8px;">{{ $item['nome'] }}</h4>
                            <div class="controle-qtd-checkout">
                                <button type="button" class="btn-qtd-mini" onclick="alterarQtdCheckout({{ $id }}, -1)">-</button>
                                <span class="qtd-numero-mini" id="qtd-val-{{ $id }}">{{ $item['quantidade'] }}</span>
                                <button type="button" class="btn-qtd-mini" onclick="alterarQtdCheckout({{ $id }}, 1)">+</button>
                            </div>
                            <p class="checkout-item-price">R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="linha-subtotal">
                <span>Subtotal</span>
                <span id="valor-subtotal" data-valor="{{ $totalGeral }}">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
            </div>
            <div class="linha-subtotal">
                <span>Frete</span>
                <span id="valor-frete-display">R$ 0,00</span>
            </div>
            <div class="checkout-total-row">
                <span>TOTAL</span>
                <span id="valor-total-final" style="color: #4a3427;">R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
            </div>
        </div>
    </main>

    <script>
        window.subtotalBase = {{ $totalGeral }};

        document.addEventListener('DOMContentLoaded', function() {
            const radios = document.querySelectorAll('.radio-endereco');
            const inputs = {
                cep: document.getElementById('cep'),
                rua: document.getElementById('logradouro'),
                numero: document.getElementById('numero'),
                bairro: document.getElementById('bairro'),
                complemento: document.getElementById('complemento'),
                cidade: document.getElementById('localidade'),
                estado: document.getElementById('uf')
            };

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    document.querySelectorAll('.card-endereco').forEach(l => l.classList.remove('active'));
                    // Adiciona active no label pai
                    this.parentElement.classList.add('active');

                    if (this.value !== 'novo') {
                        inputs.cep.value = this.dataset.cep;
                        inputs.rua.value = this.dataset.rua;
                        inputs.numero.value = this.dataset.numero;
                        inputs.bairro.value = this.dataset.bairro;
                        inputs.complemento.value = this.dataset.complemento;
                        inputs.cidade.value = this.dataset.cidade;
                        inputs.estado.value = this.dataset.estado;
                        inputs.cep.dispatchEvent(new Event('input', { bubbles: true }));
                    } else {
                        inputs.cep.value = '';
                        inputs.rua.value = '';
                        inputs.numero.value = '';
                        inputs.bairro.value = '';
                        inputs.complemento.value = '';
                        inputs.cidade.value = '';
                        inputs.estado.value = '';
                    }
                });
            });
        });
    </script>
    @push('js')
        <script src="{{ asset('js/checkout.js') }}"></script>
    @endpush
@endsection
