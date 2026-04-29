// ==========================================
// 1. FUNÇÃO GLOBAL DO TOAST (CASA MORÁ)
// ==========================================
window.showMoraToast = function(mensagem, tipo = 'success') {
    let container = document.getElementById('mora-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'mora-toast-container';
        document.body.appendChild(container);

        // Estilos injetados dinamicamente
        const style = document.createElement('style');
        style.innerHTML = `
            #mora-toast-container { position: fixed; top: 30px; right: 30px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; }
            .mora-toast { min-width: 250px; background-color: #4a3427; color: #fff; padding: 16px 24px; border-radius: 4px; font-family: 'Poppins', sans-serif; font-size: 0.8rem; font-weight: 500; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transform: translateX(120%); opacity: 0; transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55); }
            .mora-toast.show { transform: translateX(0); opacity: 1; }
            .mora-toast.error { background-color: #c94c4c; }
        `;
        document.head.appendChild(style);
    }

    const toast = document.createElement('div');
    toast.className = `mora-toast ${tipo}`;
    toast.innerHTML = `<i class="fa-solid ${tipo === 'error' ? 'fa-circle-exclamation' : 'fa-check'}"></i> &nbsp; ${mensagem}`;
    container.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 400);
    }, 3500);
};

// ==========================================
// 2. CLASSE GERENCIADORA DO CARRINHO
// ==========================================
class CarrinhoManager {
    constructor() {
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
        this.btnCarrinho?.addEventListener('click', () => this.abrirSidebar());
        this.btnFecharCarrinho?.addEventListener('click', () => this.fecharSidebar());
        this.overlay?.addEventListener('click', () => this.fecharSidebar());

        // Botões "Comprar" da Home / Vitrine
        document.querySelectorAll('.btn-comprar').forEach(botao => {
            botao.addEventListener('click', (e) => this.adicionarAoCarrinho(e));
        });

        // Botões + e - dentro do Sidebar
        this.lista?.addEventListener('click', (e) => {
            const id = e.target.getAttribute('data-id');
            if (!id) return;
            if (e.target.classList.contains('btn-aumentar')) {
                this.alterarQuantidadeInstantanea(id, 'aumentar', e.target);
            } else if (e.target.classList.contains('btn-diminuir')) {
                this.alterarQuantidadeInstantanea(id, 'diminuir', e.target);
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
        if (!produtoId || produtoId === "NULL") return;

        const originalText = botao.innerText;
        botao.innerText = "AGUARDE...";
        botao.disabled = true;

        fetch(this.urlAdicionar, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ produto_id: produtoId, quantidade: 1 })
        })
            .then(response => response.json())
            .then(data => {
                botao.innerText = originalText;
                botao.disabled = false;

                if (data.success) {
                    this.itensLocais = data.itens;
                    this.totalLocal = data.total;
                    this.renderizar();
                    this.abrirSidebar();
                    window.showMoraToast('Produto adicionado ao carrinho!', 'success');
                } else {
                    window.showMoraToast(data.message || 'Estoque indisponível.', 'error');
                }
            })
            .catch(err => {
                botao.innerText = originalText;
                botao.disabled = false;
                window.showMoraToast('Erro ao comunicar com o servidor.', 'error');
            });
    }

    // O parâmetro 'botao' serve para desativarmos ele temporariamente
    alterarQuantidadeInstantanea(produtoId, acao, botao = null) {
        const url = acao === 'aumentar' ? this.urlAdicionar : this.urlDiminuir;
        if (botao) botao.disabled = true;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ produto_id: produtoId, quantidade: 1 })
        })
            .then(response => response.json())
            .then(data => {
                if (botao) botao.disabled = false;
                if (data.success) {
                    this.itensLocais = data.itens;
                    this.totalLocal = data.total;
                    this.renderizar();
                } else {
                    window.showMoraToast(data.message || 'Estoque insuficiente.', 'error');
                }
            })
            .catch(err => {
                if (botao) botao.disabled = false;
                window.showMoraToast('Erro ao atualizar quantidade.', 'error');
            });
    }

    renderizar() {
        const itens = this.itensLocais;
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
            if (!item.nome || item.nome === "NULL") continue;

            let precoTotalItem = (item.preco * item.quantidade).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            let imagemSrc = item.imagem || '/assets/vasomora.png';

            html += `
                <div class="carrinho-item">
                    <img src="${imagemSrc}" alt="${item.nome}" class="carrinho-item-img">
                    <div class="carrinho-item-info">
                        <h4>${item.nome}</h4>
                        <p>R$ ${precoTotalItem}</p>
                        <div class="carrinho-qtd">
                            <button class="btn-diminuir" data-id="${id}">-</button>
                            <span>${item.quantidade}</span>
                            <button class="btn-aumentar" data-id="${id}">+</button>
                        </div>
                    </div>
                </div>
            `;
        }
        this.lista.innerHTML = html;
        this.valorTotal.innerText = `R$ ${this.totalLocal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
    }

    abrirSidebar() { this.sidebar?.classList.add('aberto'); this.overlay?.classList.add('ativo'); }
    fecharSidebar() { this.sidebar?.classList.remove('aberto'); this.overlay?.classList.remove('ativo'); }
}

// ==========================================
// 3. FUNÇÕES DA TELA DE PRODUTO
// ==========================================
function ajustarQtd(valor) {
    const campo = document.getElementById('qtd-produto');
    if (!campo) return;
    let novaQtd = parseInt(campo.value) + valor;
    if (novaQtd >= 1) campo.value = novaQtd;
}

function adicionarComQtd(irParaCheckout) {
    const qtd = document.getElementById('qtd-produto') ? document.getElementById('qtd-produto').value : 1;
    const btnAdicionar = document.getElementById('btn-add-carrinho');
    if (!btnAdicionar) return;

    const produtoId = btnAdicionar.getAttribute('data-id');
    const urlAdicionar = document.querySelector('meta[name="carrinho-url"]').content;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    fetch(urlAdicionar, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ produto_id: produtoId, quantidade: qtd })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                window.showMoraToast(data.message || 'Estoque indisponível.', 'error');
                return;
            }

            if (irParaCheckout) {
                window.location.href = "/checkout";
            } else {
                window.showMoraToast('Produto adicionado ao carrinho!', 'success');
                if (typeof CarrinhoManager !== 'undefined') {
                    const manager = new CarrinhoManager();
                    manager.abrirSidebar();
                } else { location.reload(); }
            }
        })
        .catch(error => console.error('Erro:', error));
}

// ==========================================
// 4. FUNÇÃO DO CHECKOUT (+ e - do resumo)
// ==========================================
function alterarQtdCheckout(produtoId, variacao) {
    const token = document.querySelector('meta[name="csrf-token"]').content;

    fetch('/carrinho/atualizar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({ produto_id: produtoId, variacao: variacao })
    })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                window.location.reload();
            } else {
                window.showMoraToast(data.error || 'Quantidade indisponível no momento.', 'error');
            }
        })
        .catch(error => console.error('Erro:', error));
}
