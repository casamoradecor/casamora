class CarrinhoManager {
    constructor() {
        // Seletores de Meta Tags com proteção contra erros
        const getMeta = (name) => document.querySelector(`meta[name="${name}"]`)?.getAttribute('content');

        this.urlAdicionar = getMeta('carrinho-url');
        this.urlListar = getMeta('carrinho-listar-url');
        this.urlDiminuir = getMeta('carrinho-diminuir-url');
        this.csrfToken = getMeta('csrf-token');

        this.sidebar = document.getElementById('carrinhoSidebar');
        this.overlay = document.getElementById('carrinhoOverlay');
        this.lista = document.getElementById('listaCarrinho');
        this.valorTotal = document.getElementById('valorTotal');
        this.btnCarrinho = document.getElementById('btnCarrinho');
        this.btnFecharCarrinho = document.getElementById('btnFecharCarrinho');

        this.itensLocais = {};
        this.totalLocal = 0;

        this.iniciarEventos();
        this.carregarCarrinho();
    }

    iniciarEventos() {
        // Eventos de Abertura/Fechamento
        this.btnCarrinho?.addEventListener('click', () => this.abrirSidebar());
        this.btnFecharCarrinho?.addEventListener('click', () => this.fecharSidebar());
        this.overlay?.addEventListener('click', () => this.fecharSidebar());

        // Evento de Compra
        document.querySelectorAll('.btn-comprar').forEach(botao => {
            botao.addEventListener('click', (e) => this.adicionarAoCarrinho(e));
        });

        // Delegação para botões internos do carrinho
        this.lista?.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            if (!id) return;

            if (e.target.classList.contains('btn-aumentar')) {
                this.alterarQuantidadeInstantanea(id, 'aumentar');
            } else if (e.target.classList.contains('btn-diminuir')) {
                this.alterarQuantidadeInstantanea(id, 'diminuir');
            }
        });
    }

    carregarCarrinho() {
        if (!this.urlListar) return;
        fetch(this.urlListar).then(r => r.json()).then(data => {
            this.itensLocais = data.itens || {};
            this.totalLocal = data.total || 0;
            this.renderizar();
        });
    }

    adicionarAoCarrinho(evento) {
        const botao = evento.currentTarget;
        const produtoId = botao.getAttribute('data-id');
        if (!produtoId || produtoId === "NULL") return; // Anti-NULL preventivo

        const produtoData = {
            nome: botao.getAttribute('data-nome'),
            preco: parseFloat(botao.getAttribute('data-preco') || 0),
            imagem: botao.getAttribute('data-imagem')
        };

        const originalText = botao.innerText;
        botao.innerText = "ADICIONANDO...";
        botao.disabled = true;

        this.alterarQuantidadeInstantanea(produtoId, 'aumentar', produtoData, () => {
            botao.innerText = originalText;
            botao.disabled = false;
            this.abrirSidebar();
        });
    }

    alterarQuantidadeInstantanea(produtoId, acao, produtoData = null, callback = null) {
        let item = this.itensLocais[produtoId];

        if (acao === 'aumentar') {
            if (item) {
                item.quantidade++;
            } else if (produtoData) {
                this.itensLocais[produtoId] = { id: produtoId, ...produtoData, quantidade: 1 };
            }
        } else if (acao === 'diminuir' && item) {
            if (item.quantidade > 1) {
                item.quantidade--;
            } else {
                delete this.itensLocais[produtoId];
            }
        }

        this.renderizar();
        if (callback) callback();

        const url = acao === 'aumentar' ? this.urlAdicionar : this.urlDiminuir;
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ produto_id: produtoId })
        }).catch(err => console.error('Erro de sync:', err));
    }

    renderizar() {
        const itens = this.itensLocais;
        // Recalcula o total com base no que está na tela
        this.totalLocal = Object.values(itens).reduce((acc, curr) => {
            return (curr.nome && curr.nome !== "NULL") ? acc + (curr.preco * curr.quantidade) : acc;
        }, 0);

        if (!itens || Object.keys(itens).length === 0) {
            this.lista.innerHTML = '<p class="carrinho-vazio">Seu carrinho está vazio.</p>';
            this.valorTotal.innerText = 'R$ 0,00';
            return;
        }

        let html = '';
        for (let id in itens) {
            let item = itens[id];

            // FILTRO ANTI-NULL
            if (!item.nome || item.nome === "NULL") continue;

            let precoTotalItem = (item.preco * item.quantidade).toLocaleString('pt-BR', {minimumFractionDigits: 2});

            // FIX DA IMAGEM: Usa a URL direta que vem do botão/controlador
            let imagemSrc = item.imagem || '/assets/vasomora.png';

            html += `
                <div class="carrinho-item" style="display: flex; gap: 15px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 15px; align-items: center;">
                    <img src="${imagemSrc}" alt="${item.nome}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                    <div style="flex: 1;">
                        <h4 style="font-size: 0.8rem; font-weight: 700; margin: 0; text-transform: uppercase;">${item.nome}</h4>
                        <p style="font-size: 0.85rem; margin: 5px 0; color: #3b1f15; font-weight: bold;">R$ ${precoTotalItem}</p>

                        <div style="display: flex; align-items: center; gap: 10px;">
                            <button class="btn-diminuir" data-id="${id}" style="width: 25px; height: 25px; cursor: pointer; border: 1px solid #ccc; background: #fff;">-</button>
                            <span style="font-size: 0.85rem; font-weight: 600;">${item.quantidade}</span>
                            <button class="btn-aumentar" data-id="${id}" style="width: 25px; height: 25px; cursor: pointer; border: 1px solid #ccc; background: #fff;">+</button>
                        </div>
                    </div>
                </div>
            `;
        }

        this.lista.innerHTML = html;
        this.valorTotal.innerText = `R$ ${this.totalLocal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
    }

    abrirSidebar() {
        this.sidebar?.classList.add('aberto');
        this.overlay?.classList.add('ativo');
    }

    fecharSidebar() {
        this.sidebar?.classList.remove('aberto');
        this.overlay?.classList.remove('ativo');
    }
}
