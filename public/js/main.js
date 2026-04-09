/* =========================================
   COMPORTAMENTO DO HEADER NO SCROLL
   ========================================= */
window.addEventListener('scroll', function() {
    const header = document.querySelector('.site-header');
    const hero = document.querySelector('.hero');

    // SÓ EXECUTA SE OS DOIS ELEMENTOS EXISTIREM NA PÁGINA ATUAL
    if (hero && header) {
        const heroHeight = hero.offsetHeight;

        // Adiciona classe quando passa quase todo o hero
        if (window.scrollY >= heroHeight - 80) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
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
document.addEventListener('DOMContentLoaded', () => {
    new CarrinhoManager();
});
document.addEventListener('DOMContentLoaded', function() {
    const buscaContainer = document.getElementById('buscaInline');
    const btnBusca = document.getElementById('btnBusca');
    const btnFechar = document.getElementById('btnFecharBusca');
    const inputBusca = document.getElementById('inputBusca');
    const containerResultados = document.getElementById('resultadosBusca');

    btnBusca.addEventListener('click', function(e) {
        if (!buscaContainer.classList.contains('active')) {
            e.preventDefault();
            buscaContainer.classList.add('active');
            btnFechar.style.display = 'block';
            inputBusca.focus();
        }
    });

    btnFechar.addEventListener('click', function() {
        buscaContainer.classList.remove('active');
        btnFechar.style.display = 'none';
        inputBusca.value = '';
        containerResultados.style.display = 'none';
    });

    inputBusca.addEventListener('input', function() {
        const query = this.value;

        if (query.length >= 3) {
            fetch(`/api/busca-produtos?q=${query}`)
                .then(response => response.json())
                .then(produtos => {
                    containerResultados.innerHTML = '';

                    if (produtos.length > 0) {
                        produtos.forEach(p => {
                            containerResultados.innerHTML += `
                                <a href="${p.link}" class="busca-item">
                                    <img src="${p.imagem}" alt="${p.nome}">
                                    <div class="busca-info">
                                        <span class="busca-nome">${p.nome}</span>
                                        <span class="busca-ver-mais">ver mais</span>
                                    </div>
                                </a>
                            `;
                        });
                        containerResultados.style.display = 'block';
                    } else {
                        containerResultados.innerHTML = '<div style="padding:15px; font-size:0.75rem; color:#888; text-align:center;">NENHUM ITEM ENCONTRADO</div>';
                        containerResultados.style.display = 'block';
                    }
                })
                .catch(error => console.error('Erro na busca:', error));
        } else {
            containerResultados.style.display = 'none';
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscaInline') && buscaContainer.classList.contains('active')) {
            buscaContainer.classList.remove('active');
            btnFechar.style.display = 'none';
            containerResultados.style.display = 'none';
        }
    });
});
