class CarrinhoManager {
    constructor() {
        this.botoesComprar = document.querySelectorAll('.btn-comprar');
        this.urlAdicionar = document.querySelector('meta[name="carrinho-url"]').getAttribute('content');
        this.urlListar = document.querySelector('meta[name="carrinho-listar-url"]').getAttribute('content');
        this.urlDiminuir = document.querySelector('meta[name="carrinho-diminuir-url"]').getAttribute('content');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        this.sidebar = document.getElementById('carrinhoSidebar');
        this.overlay = document.getElementById('carrinhoOverlay');
        this.lista = document.getElementById('listaCarrinho');
        this.valorTotal = document.getElementById('valorTotal');
        
        this.iniciarEventos();
        this.carregarCarrinho();
    }

    iniciarEventos() {
        // Evento para os botões da vitrine
        this.botoesComprar.forEach(botao => {
            botao.addEventListener('click', (e) => this.adicionar(e));
        });

        // Delegação de eventos para os botões + e - gerados dinamicamente
        this.lista.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-aumentar')) {
                this.alterarQuantidade(e.target.getAttribute('data-id'), 'aumentar');
            } else if (e.target.classList.contains('btn-diminuir')) {
                this.alterarQuantidade(e.target.getAttribute('data-id'), 'diminuir');
            }
        });
    }

    carregarCarrinho() {
        fetch(this.urlListar)
        .then(response => response.json())
        .then(data => this.renderizar(data.itens, data.total))
        .catch(error => console.error('Erro:', error));
    }

    adicionar(event) {
        const botao = event.currentTarget;
        const produtoId = botao.getAttribute('data-id');
        this.alterarQuantidade(produtoId, 'aumentar', botao);
    }

    // Função unificada para aumentar ou diminuir
    alterarQuantidade(produtoId, acao, botaoOrigem = null) {
        const url = acao === 'aumentar' ? this.urlAdicionar : this.urlDiminuir;
        let textoOriginal = '';

        if (botaoOrigem) {
            textoOriginal = botaoOrigem.innerText;
            botaoOrigem.innerText = "...";
            botaoOrigem.disabled = true;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken
            },
            body: JSON.stringify({ produto_id: produtoId })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                this.renderizar(data.itens, data.total);
                if (botaoOrigem) this.abrirSidebar();
            }
        })
        .finally(() => {
            if (botaoOrigem) {
                botaoOrigem.innerText = textoOriginal;
                botaoOrigem.disabled = false;
            }
        });
    }

    renderizar(itens, total) {
        if (Object.keys(itens).length === 0) {
            this.lista.innerHTML = '<p class="carrinho-vazio">Seu carrinho está vazio.</p>';
            this.valorTotal.innerText = 'R$ 0,00';
            return;
        }

        let html = '';
        for (let id in itens) {
            let item = itens[id];
            let preco = parseFloat(item.preco).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            let imagemSrc = item.imagem ? '/storage/' + item.imagem : '/assets/vasomora.png';
            
            // HTML atualizado com os botões + e -
            html += `
                <div class="carrinho-item" style="display: flex; gap: 10px; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; align-items: center;">
                    <img src="${imagemSrc}" alt="${item.nome}" style="width: 60px; height: 60px; object-fit: cover;">
                    <div class="carrinho-item-info" style="flex: 1;">
                        <h4 style="margin: 0; font-size: 14px;">${item.nome}</h4>
                        <p style="margin: 5px 0; font-weight: bold; font-size: 14px;">R$ ${preco}</p>
                        
                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 5px;">
                            <button class="btn-diminuir" data-id="${item.id}" style="width: 25px; height: 25px; cursor: pointer;">-</button>
                            <span style="font-size: 14px;">${item.quantidade}</span>
                            <button class="btn-aumentar" data-id="${item.id}" style="width: 25px; height: 25px; cursor: pointer;">+</button>
                        </div>
                    </div>
                </div>
            `;
        }

        this.lista.innerHTML = html;
        this.valorTotal.innerText = 'R$ ' + parseFloat(total).toLocaleString('pt-BR', {minimumFractionDigits: 2});
    }

    abrirSidebar() {
        if(this.sidebar && this.overlay) {
            this.sidebar.classList.add('ativo'); 
            this.overlay.classList.add('ativo');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CarrinhoManager();
});