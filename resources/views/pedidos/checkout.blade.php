@extends('layouts.app')

@section('title', 'Finalizar Compra — Casa MORÁ')

@section('header_class', 'scrolled')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endpush

@section('content')
<main class="checkout-container">
    
    <div class="checkout-form-section">
        <h2>Dados de Entrega</h2>
        
        <form action="{{ route('pedido.finalizar') }}" method="POST" class="checkout-form">
            @csrf
            
            <div class="form-group">
                <label>Nome para Entrega</label>
                <input type="text" value="{{ Auth::user()->name ?? '' }}" class="input-checkout input-disabled" disabled>
            </div>

            <div class="form-group">
                <label>CPF</label>
                <input type="text" value="{{ Auth::user()->cpf }}" class="input-checkout input-disabled" disabled>
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
            
            <button type="submit" class="btn-confirmar">
                CONFIRMAR PEDIDO
            </button>
        </form>
    </div>

    <div class="checkout-resumo">
        <h2>Resumo do Pedido</h2>
        
        <div class="checkout-itens-lista">
            @php $totalGeral = 0; @endphp @foreach($carrinho as $item)
                @php 
                    // Lógica corrigida: Se começa com 'assets', ignora a pasta 'storage'
                    $imagemSrc = str_starts_with($item['imagem'], 'assets') 
                                 ? asset($item['imagem']) 
                                 : asset('storage/' . $item['imagem']);
                    
                    $totalGeral += $item['preco'] * $item['quantidade'];
                @endphp
                
                <div class="checkout-item">
                    <img src="{{ $imagemSrc }}" alt="{{ $item['nome'] }}">
                    <div class="checkout-item-info">
                        <h4>{{ $item['nome'] }}</h4>
                        <p>Qtd: {{ $item['quantidade'] }}</p>
                        <p class="checkout-item-price">R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="checkout-total-row">
            <span>TOTAL</span>
            <span>R$ {{ number_format($totalGeral, 2, ',', '.') }}</span>
        </div>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const cepInput = document.getElementById('cep');

    cepInput.addEventListener('blur', function() {
        let cep = this.value.replace(/\D/g, '');

        if (cep.length === 8) {
            // Feedback visual
            document.getElementById('logradouro').value = "...";
            document.getElementById('bairro').value = "...";
            document.getElementById('localidade').value = "...";
            document.getElementById('uf').value = "...";

            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('logradouro').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('localidade').value = data.localidade;
                        document.getElementById('uf').value = data.uf;
                        document.getElementById('numero').focus(); // Foca no número para o usuário digitar
                    } else {
                        alert("CEP não encontrado.");
                    }
                })
                .catch(error => console.error('Erro ao buscar CEP:', error));
        }
    });

    // Máscara de CEP (00000-000)
    cepInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        v = v.replace(/^(\d{5})(\d)/, '$1-$2');
        e.target.value = v.slice(0, 9);
    });
});
</script>
@endsection