window.addEventListener('scroll', function() {
  const header = document.querySelector('.site-header');
  const hero = document.querySelector('.hero');
  
  // Pegamos a altura total da seção Hero
  const heroHeight = hero.offsetHeight;

  // Se o scroll passar da altura do Hero, adiciona a cor. 
  // Subtraímos uns 50px caso queira que a cor entre um pouquinho antes de sumir tudo.
  if (window.scrollY >= heroHeight - 80) { 
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const track = document.getElementById('carrosselTrack');
  const btnPrev = document.getElementById('btnPrev');
  const btnNext = document.getElementById('btnNext');

  if (track && btnPrev && btnNext) {
    // Distância a ser rolada (largura do card 250px + gap 20px)
    const scrollAmount = 270; 

    btnPrev.addEventListener('click', () => {
      track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    btnNext.addEventListener('click', () => {
      track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });
  }
});

document.addEventListener('DOMContentLoaded', () => {
  const hotspots = document.querySelectorAll('.hotspot');

  hotspots.forEach(hotspot => {
    const dot = hotspot.querySelector('.hotspot-dot');
    
    dot.addEventListener('click', (e) => {
      e.stopPropagation(); // Impede que o clique feche imediatamente
      
      // Fecha todas as outras caixas abertas
      hotspots.forEach(h => {
        if (h !== hotspot) h.classList.remove('ativo');
      });
      
      // Abre/fecha a caixa clicada
      hotspot.classList.toggle('ativo');
    });
  });

  // Fecha a caixa se clicar em qualquer lugar da tela
  document.addEventListener('click', () => {
    hotspots.forEach(h => h.classList.remove('ativo'));
  });
});
/* =========================================
   CARRINHO DE COMPRAS - ABRIR E FECHAR
   ========================================= */
document.addEventListener('DOMContentLoaded', () => {
  const btnCarrinho = document.getElementById('btnCarrinho');
  const btnFecharCarrinho = document.getElementById('btnFecharCarrinho');
  const carrinhoSidebar = document.getElementById('carrinhoSidebar');
  const carrinhoOverlay = document.getElementById('carrinhoOverlay');

  // Função para abrir
  function abrirCarrinho() {
    carrinhoSidebar.classList.add('aberto');
    carrinhoOverlay.classList.add('ativo');
  }

  // Função para fechar
  function fecharCarrinho() {
    carrinhoSidebar.classList.remove('aberto');
    carrinhoOverlay.classList.remove('ativo');
  }

  // Eventos de clique
  if (btnCarrinho) btnCarrinho.addEventListener('click', abrirCarrinho);
  if (btnFecharCarrinho) btnFecharCarrinho.addEventListener('click', fecharCarrinho);
  if (carrinhoOverlay) carrinhoOverlay.addEventListener('click', fecharCarrinho);
});

/* =========================================
   LÓGICA DO CARRINHO DE COMPRAS (OTIMIZADA)
   ========================================= */
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
        this.btnCarrinho = document.getElementById('btnCarrinho');
        this.btnFecharCarrinho = document.getElementById('btnFecharCarrinho');
        
        // Memória local para atualização instantânea
        this.itensLocais = {};
        this.totalLocal = 0;
        
        this.iniciarEventos();
        this.carregarCarrinho(); 
    }

    iniciarEventos() {
        if (this.btnCarrinho) this.btnCarrinho.addEventListener('click', () => this.abrirSidebar());
        if (this.btnFecharCarrinho) this.btnFecharCarrinho.addEventListener('click', () => this.fecharSidebar());
        if (this.overlay) this.overlay.addEventListener('click', () => this.fecharSidebar());

        this.botoesComprar.forEach(botao => {
            botao.addEventListener('click', (e) => this.adicionarAoCarrinho(e));
        });

        this.lista.addEventListener('click', (e) => {
            const btn = e.target;
            const produtoId = btn.getAttribute('data-id');

            if (btn.classList.contains('btn-aumentar')) {
                this.alterarQuantidadeInstantanea(produtoId, 'aumentar');
            } else if (btn.classList.contains('btn-diminuir')) {
                this.alterarQuantidadeInstantanea(produtoId, 'diminuir');
            }
        });
    }

    carregarCarrinho() {
        fetch(this.urlListar).then(r => r.json()).then(data => {
            this.itensLocais = data.itens || {};
            this.totalLocal = data.total || 0;
            this.renderizar(this.itensLocais, this.totalLocal);
        });
    }

    adicionarAoCarrinho(evento) {
        const botao = evento.currentTarget;
        const produtoId = botao.getAttribute('data-id');
        
        // Dados para a atualização visual imediata
        const produtoData = {
            nome: botao.getAttribute('data-nome'),
            preco: parseFloat(botao.getAttribute('data-preco') || 0),
            imagem: botao.getAttribute('data-imagem')
        };
        
        botao.innerText = "ADICIONANDO...";
        botao.disabled = true;

        this.alterarQuantidadeInstantanea(produtoId, 'aumentar', produtoData, () => {
            botao.innerText = "ADICIONAR AO CARRINHO";
            botao.disabled = false;
            this.abrirSidebar();
        });
    }

    // Atualiza a tela primeiro, depois avisa o servidor
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

        // Recalcula o total localmente e renderiza na hora
        this.totalLocal = Object.values(this.itensLocais).reduce((acc, curr) => acc + (curr.preco * curr.quantidade), 0);
        this.renderizar(this.itensLocais, this.totalLocal);
        
        if (callback) callback();

        // Sincroniza com o Laravel em segundo plano (sem travar a tela)
        const url = acao === 'aumentar' ? this.urlAdicionar : this.urlDiminuir;
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
            body: JSON.stringify({ produto_id: produtoId })
        }).catch(err => console.error('Erro de sync:', err));
    }

    renderizar(itens, total) {
        if (!itens || Object.keys(itens).length === 0) {
            this.lista.innerHTML = '<p class="carrinho-vazio">Seu carrinho está vazio.</p>';
            this.valorTotal.innerText = 'R$ 0,00';
            return;
        }

        let html = '';
        for (let id in itens) {
            let item = itens[id];
            let precoTotalItem = (item.preco * item.quantidade).toFixed(2).replace('.', ',');
            let imagemSrc = item.imagem && !item.imagem.includes('assets') ? '/storage/' + item.imagem : (item.imagem || '/assets/vasomora.png');
            
            html += `
                <div class="carrinho-item" style="display: flex; gap: 15px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 15px;">
                    <img src="${imagemSrc}" alt="${item.nome}" style="width: 70px; height: 70px; object-fit: cover;">
                    <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0;">${item.nome}</h4>
                        <p style="font-size: 0.9rem; margin: 0;">R$ ${precoTotalItem}</p>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; mt-2">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <button class="btn-diminuir" data-id="${item.id}" style="width: 20px; height: 20px; cursor: pointer; border: 1px solid #ccc; background: #fff;">-</button>
                                <span style="font-size: 0.8rem;">${item.quantidade}</span>
                                <button class="btn-aumentar" data-id="${item.id}" style="width: 20px; height: 20px; cursor: pointer; border: 1px solid #ccc; background: #fff;">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        this.lista.innerHTML = html;
        this.valorTotal.innerText = `R$ ${parseFloat(total).toFixed(2).replace('.', ',')}`;
    }

    abrirSidebar() {
        if (this.sidebar) this.sidebar.classList.add('aberto'); 
        if (this.overlay) this.overlay.classList.add('ativo');
    }

    fecharSidebar() {
        if (this.sidebar) this.sidebar.classList.remove('aberto'); 
        if (this.overlay) this.overlay.classList.remove('ativo');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CarrinhoManager();
});