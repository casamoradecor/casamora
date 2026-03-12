// Ajusta a quantidade no input visual
function ajustarQtd(valor) {
    const campo = document.getElementById('qtd-produto');
    if (!campo) return;
    let novaQtd = parseInt(campo.value) + valor;
    if (novaQtd >= 1) campo.value = novaQtd;
}

// Botão principal de comprar/adicionar na tela do produto
function adicionarComQtd(irParaCheckout) {
    const qtd = document.getElementById('qtd-produto') ? document.getElementById('qtd-produto').value : 1;
    const btnAdicionar = document.getElementById('btn-add-carrinho');

    if (!btnAdicionar) return;

    const produtoId = btnAdicionar.getAttribute('data-id');
    const urlAdicionar = document.querySelector('meta[name="carrinho-url"]').content;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Feedback visual e bloqueio do botão
    const originalText = btnAdicionar.innerText;
    btnAdicionar.innerText = "AGUARDE...";
    btnAdicionar.disabled = true;

    fetch(urlAdicionar, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ produto_id: produtoId, quantidade: qtd })
    })
        .then(response => response.json())
        .then(data => {
            btnAdicionar.innerText = originalText;
            btnAdicionar.disabled = false;

            if (!data.success) {
                window.showMoraToast(data.message || 'Estoque indisponível.', 'error');
                return;
            }

            if (irParaCheckout) {
                window.location.href = "/checkout";
            } else {
                window.showMoraToast('Produto adicionado ao carrinho!', 'success');
                // Atualiza o sidebar do carrinho
                if (typeof CarrinhoManager !== 'undefined') {
                    const manager = new CarrinhoManager();
                    manager.abrirSidebar();
                } else {
                    location.reload();
                }
            }
        })
        .catch(error => {
            btnAdicionar.innerText = originalText;
            btnAdicionar.disabled = false;
            console.error('Erro:', error);
            window.showMoraToast('Erro ao comunicar com o servidor.', 'error');
        });
}

// Calcula o frete de um único produto
function calcularFrete() {
    const cep = document.getElementById('cep-destino').value;
    const resultadoDiv = document.getElementById('resultado-frete');
    const btnAdicionar = document.getElementById('btn-add-carrinho');

    if (cep.length < 8 || !btnAdicionar) return;

    const produtoId = btnAdicionar.getAttribute('data-id');

    resultadoDiv.style.display = 'block';
    resultadoDiv.innerHTML = '<p style="font-size: 0.7rem; color: #999;">Calculando...</p>';

    fetch(`/frete/calcular?cep=${cep}&produto_id=${produtoId}`)
        .then(response => response.json())
        .then(data => {
            resultadoDiv.innerHTML = '';

            if (data.error || data.length === 0) {
                resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.75rem;">Indisponível para este CEP.</p>';
                return;
            }

            data.forEach(opcao => {
                resultadoDiv.innerHTML += `
                    <div class="frete-item">
                        <div><strong>${opcao.nome}</strong><span>${opcao.prazo}</span></div>
                        <strong>R$ ${opcao.preco}</strong>
                    </div>`;
            });
        })
        .catch(error => {
            resultadoDiv.innerHTML = '<p style="color: red; font-size: 0.75rem;">Erro ao calcular frete.</p>';
        });
}
