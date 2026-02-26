@extends('layouts.app')

@section('title', 'Finalizar Compra - Casa MORÁ')

@section('header_class', 'scrolled') 

@section('content')
<main class="checkout-container" style="padding: 120px 10% 50px; display: flex; gap: 40px; flex-wrap: wrap; background-color: var(--cor-fundo, #ffffff); color: var(--cor-texto, #333333);">
    
    <div style="flex: 2; min-width: 300px;">
        <h2 style="font-family: var(--fonte-titulos, 'Playfair Display', serif);">Dados de Entrega</h2>
        
        <form action="#" method="POST" style="display: flex; flex-direction: column; gap: 15px; margin-top: 20px;">
            @csrf
            <input type="text" name="nome" placeholder="Nome Completo" required style="padding: 12px; border: 1px solid var(--cor-borda, #cccccc); border-radius: 4px; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            <input type="email" name="email" placeholder="E-mail" required style="padding: 12px; border: 1px solid var(--cor-borda, #cccccc); border-radius: 4px; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            <input type="text" name="cpf" placeholder="CPF" required style="padding: 12px; border: 1px solid var(--cor-borda, #cccccc); border-radius: 4px; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            <input type="text" name="cep" placeholder="CEP" required style="padding: 12px; border: 1px solid var(--cor-borda, #cccccc); border-radius: 4px; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            <input type="text" name="endereco" placeholder="Endereço Completo" required style="padding: 12px; border: 1px solid var(--cor-borda, #cccccc); border-radius: 4px; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            
            <button type="submit" style="padding: 15px; background-color: var(--cor-primaria, #000000); color: var(--cor-secundaria, #ffffff); border: none; cursor: pointer; margin-top: 10px; font-weight: bold; font-family: var(--fonte-texto, 'Poppins', sans-serif); transition: opacity 0.3s;">
                CONFIRMAR PEDIDO
            </button>
        </form>
    </div>

    <div class="checkout-resumo" style="flex: 1; min-width: 300px; background: var(--cor-fundo-secundaria, #f9f9f9); padding: 30px; border: 1px solid var(--cor-borda, #eeeeee); border-radius: 8px;">
        <h2 style="font-family: var(--fonte-titulos, 'Playfair Display', serif);">Resumo do Pedido</h2>
        
        <div style="margin-top: 20px;">
            @foreach($carrinho as $item)
                @php 
                    // Garante que o caminho da imagem seja lido corretamente
                    $imagemSrc = isset($item['imagem']) && !str_contains($item['imagem'], 'assets') 
                                 ? asset('storage/' . $item['imagem']) 
                                 : asset($item['imagem'] ?? 'assets/vasomora.png');
                @endphp
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px; border-bottom: 1px solid var(--cor-borda, #dddddd); padding-bottom: 15px; align-items: center;">
                    <img src="{{ $imagemSrc }}" alt="{{ $item['nome'] }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 4px;">
                    
                    <div style="flex: 1;">
                        <h4 style="margin: 0; font-size: 1rem; font-family: var(--fonte-texto, 'Poppins', sans-serif);">{{ $item['nome'] }}</h4>
                        <p style="margin: 5px 0; font-weight: bold; font-size: 0.95rem;">R$ {{ number_format($item['preco'] * $item['quantidade'], 2, ',', '.') }}</p>
                        
                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                            <button class="btn-qtd-checkout btn-diminuir-checkout" data-id="{{ $item['id'] }}" style="width: 25px; height: 25px; cursor: pointer; border: 1px solid var(--cor-borda, #ccc); background: var(--cor-fundo, #fff); display: flex; align-items: center; justify-content: center;">-</button>
                            <span style="font-size: 0.9rem; font-weight: bold;">{{ $item['quantidade'] }}</span>
                            <button class="btn-qtd-checkout btn-aumentar-checkout" data-id="{{ $item['id'] }}" style="width: 25px; height: 25px; cursor: pointer; border: 1px solid var(--cor-borda, #ccc); background: var(--cor-fundo, #fff); display: flex; align-items: center; justify-content: center;">+</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 20px; font-size: 1.3rem; font-weight: bold; display: flex; justify-content: space-between; font-family: var(--fonte-texto, 'Poppins', sans-serif);">
            <span>Total:</span>
            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const containerResumo = document.querySelector('.checkout-resumo');
    if(!containerResumo) return;

    containerResumo.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-qtd-checkout');
        if(!btn) return;

        const produtoId = btn.getAttribute('data-id');
        const acao = btn.classList.contains('btn-aumentar-checkout') 
            ? '{{ route("carrinho.adicionar") }}' 
            : '{{ route("carrinho.diminuir") }}';
        
        // Feedback visual rápido
        btn.disabled = true;
        btn.innerHTML = '...';

        fetch(acao, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ produto_id: produtoId })
        }).then(response => response.json())
          .then(() => {
              // Recarrega a página para o PHP recalcular o total da compra e redesenhar os itens
              window.location.reload(); 
          })
          .catch(error => console.error('Erro ao alterar quantidade:', error));
    });
});
</script>
@endsection