// Calcula o frete de um único item (Tela de Produto)
function calcularFreteProduto(produtoId) {
    const cepInput = document.getElementById('cep-destino');
    const resultadoDiv = document.getElementById('resultado-frete');

    // Limpa o CEP (mantém só números) e valida
    const cep = cepInput.value.replace(/\D/g, '');

    if (cep.length < 8) return;

    resultadoDiv.style.display = 'block';
    resultadoDiv.innerHTML = '<p style="font-size: 0.7rem; color: #999; text-transform: uppercase;">Calculando...</p>';

    fetch(`/frete/calcular?cep=${cep}&produto_id=${produtoId}`)
        .then(response => response.json())
        .then(data => {
            resultadoDiv.innerHTML = '';
            if (!data || data.length === 0) {
                resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.75rem; text-transform: uppercase;">Indisponível para este CEP.</p>';
                return;
            }

            data.forEach(opcao => {
                // Monta o HTML com as classes do seu CSS
                resultadoDiv.innerHTML += `
                    <div class="resultado-frete-item">
                        <div class="frete-info">
                            <span class="frete-nome">${opcao.nome}</span>
                            <span class="frete-prazo">${opcao.prazo}</span>
                        </div>
                        <span class="frete-preco">R$ ${opcao.preco}</span>
                    </div>`;
            });
        })
        .catch(() => {
            resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.75rem; text-transform: uppercase;">Erro ao calcular.</p>';
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
            if (data.length === 0) {
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
