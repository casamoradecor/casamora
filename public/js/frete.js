// Calcula o frete de um único item (Tela de Produto)
function calcularFreteProduto(produtoId) {
    const cep = document.getElementById('cep-destino').value;
    const resultadoDiv = document.getElementById('resultado-frete');

    if (cep.length < 8) return;

    resultadoDiv.style.display = 'block';
    resultadoDiv.innerHTML = '<p style="font-size: 0.7rem; color: #999;">Calculando...</p>';

    fetch(`/frete/calcular?cep=${cep}&produto_id=${produtoId}`)
        .then(response => response.json())
        .then(data => {
            resultadoDiv.innerHTML = '';
            if (data.length === 0) {
                resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.75rem;">Indisponível para este CEP.</p>';
                return;
            }
            data.forEach(opcao => {
                resultadoDiv.innerHTML += `
                    <div class="frete-item">
                        <div><strong>${opcao.nome}</strong><span>${opcao.prazo}</span></div>
                        <strong>r$ ${opcao.preco}</strong>
                    </div>`;
            });
        });
}

// Calcula o frete de todo o carrinho (Tela de Checkout)
function buscarFreteCheckout(cep, subtotalBase) {
    const containerFrete = document.getElementById('box-opcoes-frete');
    const listaFretes = document.getElementById('lista-fretes-checkout');

    containerFrete.style.display = 'block';
    listaFretes.innerHTML = '<p style="font-size:0.8rem; color:#888;">Calculando opções de envio...</p>';

    fetch(`/frete/calcular-carrinho?cep=${cep}`)
        .then(response => response.json())
        .then(data => {
            listaFretes.innerHTML = '';
            if(data.length === 0) {
                listaFretes.innerHTML = '<p style="color:red; font-size:0.8rem;">Frete indisponível.</p>';
                return;
            }

            data.forEach(opcao => {
                const precoFloat = parseFloat(opcao.preco.replace('.', '').replace(',', '.'));
                listaFretes.innerHTML += `
                    <label class="frete-radio-item">
                        <input type="radio" name="frete_radio" value="${opcao.nome}"
                            onchange="selecionarFrete(${precoFloat}, '${opcao.nome}', ${subtotalBase})" required>
                        <div class="frete-radio-content">
                            <div style="display: flex; flex-direction: column;">
                                <span class="f-nome-chk">${opcao.nome}</span>
                                <span class="f-prazo-chk">${opcao.prazo}</span>
                            </div>
                            <span class="f-preco-chk">R$ ${opcao.preco}</span>
                        </div>
                    </label>
                `;
            });
        });
}

function selecionarFrete(valor, nome, subtotalBase) {
    document.getElementById('frete_escolhido_input').value = nome;
    document.getElementById('valor_frete_input').value = valor;
    document.getElementById('valor-frete-display').innerText = `R$ ${valor.toFixed(2).replace('.', ',')}`;

    const total = subtotalBase + valor;
    document.getElementById('valor-total-final').innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
    document.getElementById('btn-finalizar').disabled = false;
}
