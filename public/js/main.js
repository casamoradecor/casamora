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

    // Abrir a busca ao clicar na lupa
    btnBusca.addEventListener('click', function(e) {
        if (!buscaContainer.classList.contains('active')) {
            e.preventDefault(); // Evita que o form envie vazio
            buscaContainer.classList.add('active');
            btnFechar.style.display = 'block';
            inputBusca.focus(); // Já coloca o cursor para digitar
        }
    });

    // Fechar a busca no X
    btnFechar.addEventListener('click', function() {
        buscaContainer.classList.remove('active');
        btnFechar.style.display = 'none';
        inputBusca.value = '';
        containerResultados.style.display = 'none';
    });

    // Fechar se clicar fora do cabeçalho
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscaInline') && buscaContainer.classList.contains('active')) {
            buscaContainer.classList.remove('active');
            btnFechar.style.display = 'none';
            containerResultados.style.display = 'none';
        }
    });
});
