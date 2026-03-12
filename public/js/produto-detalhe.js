document.addEventListener('DOMContentLoaded', function () {
    const campoQtd = document.getElementById('qtd-produto');
    const btnAumentar = document.getElementById('aumentar-qtd');
    const btnDiminuir = document.getElementById('diminuir-qtd');
    const btnAdicionar = document.getElementById('btn-add-carrinho');
    const btnFinalizar = document.getElementById('btn-finalizar-agora');

    // 1. Controle do + e -
    btnAumentar.addEventListener('click', () => campoQtd.value = parseInt(campoQtd.value) + 1);
    btnDiminuir.addEventListener('click', () => {
        if (parseInt(campoQtd.value) > 1) campoQtd.value = parseInt(campoQtd.value) - 1;
    });

    // 2. Função de Adicionar (Igual ao que o CarrinhoManager faz)
    async function mandarProCarrinho(irParaCheckout) {
        // Pegamos as URLs e o Token das Meta Tags do App Blade
        const urlAdicionar = document.querySelector('meta[name="carrinho-url"]').content;
        const token = document.querySelector('meta[name="csrf-token"]').content;

        const dados = {
            produto_id: btnAdicionar.getAttribute('data-id'), // O nome tem que ser produto_id
            quantidade: campoQtd.value
        };

        try {
            const response = await fetch(urlAdicionar, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(dados)
            });

            if (irParaCheckout) {
                window.location.href = "/checkout";
            } else {
                // ABRE A BARRA LATERAL (Suas classes do carrinho.js)
                const sidebar = document.getElementById('carrinhoSidebar');
                const overlay = document.getElementById('carrinhoOverlay');

                sidebar.classList.add('aberto');
                overlay.classList.add('ativo');

                // Aqui está o truque: disparar um evento para o carrinho.js atualizar
                // Como não queremos mexer no app.blade, vamos apenas recarregar a lista
                // chamando o clique no botão de abrir o carrinho que já existe no topo
                document.getElementById('btnCarrinho').click();
            }
        } catch (error) {
            console.error("Erro ao adicionar:", error);
        }
    }

    btnAdicionar.addEventListener('click', () => mandarProCarrinho(false));
    btnFinalizar.addEventListener('click', () => mandarProCarrinho(true));
});
