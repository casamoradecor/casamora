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
   LÓGICA DO CARRINHO DE COMPRAS
   ========================================= */
let carrinho = [];

function atualizarCarrinho() {
  const listaCarrinho = document.getElementById('listaCarrinho');
  const valorTotal = document.getElementById('valorTotal');
  
  listaCarrinho.innerHTML = '';
  let total = 0;

  if (carrinho.length === 0) {
    listaCarrinho.innerHTML = '<p class="carrinho-vazio">Seu carrinho está vazio.</p>';
  } else {
    carrinho.forEach((item, index) => {
      total += item.preco * item.quantidade;
      
      // Injeta o HTML de cada item diretamente no carrinho (com CSS inline básico para agilizar)
      listaCarrinho.innerHTML += `
        <div class="carrinho-item" style="display: flex; gap: 15px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 15px;">
          <img src="${item.imagem}" alt="${item.nome}" style="width: 70px; height: 70px; object-fit: cover;">
          <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
            <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0;">${item.nome}</h4>
            <p style="font-size: 0.9rem; margin: 0;">R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</p>
            <div style="display: flex; justify-content: space-between; align-items: center; mt-2">
              <span style="font-size: 0.8rem;">Qtd: ${item.quantidade}</span>
              <button onclick="removerItem(${index})" style="background: none; border: none; color: #999; cursor: pointer; font-size: 0.8rem; text-decoration: underline;">Remover</button>
            </div>
          </div>
        </div>
      `;
    });
  }

  // Atualiza o texto do valor total
  valorTotal.innerText = `R$ ${total.toFixed(2).replace('.', ',')}`;
}

function adicionarAoCarrinho(evento) {
  const botao = evento.target;
  const nome = botao.getAttribute('data-nome');
  const preco = parseFloat(botao.getAttribute('data-preco'));
  const imagem = botao.getAttribute('data-imagem');

  // Verifica se o item já está no carrinho
  const itemExistente = carrinho.find(item => item.nome === nome);

  if (itemExistente) {
    itemExistente.quantidade += 1;
  } else {
    carrinho.push({ nome, preco, imagem, quantidade: 1 });
  }

  atualizarCarrinho();
  
  // Abre o carrinho automaticamente ao adicionar
  document.getElementById('carrinhoSidebar').classList.add('aberto');
  document.getElementById('carrinhoOverlay').classList.add('ativo');
}

// Função global para remover item pelo botão "Remover"
window.removerItem = function(index) {
  carrinho.splice(index, 1);
  atualizarCarrinho();
};

// Captura o clique em todos os botões "Adicionar ao Carrinho"
document.addEventListener('DOMContentLoaded', () => {
  const botoesComprar = document.querySelectorAll('.btn-comprar');
  botoesComprar.forEach(botao => {
    botao.addEventListener('click', adicionarAoCarrinho);
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const btnBusca = document.getElementById('btnBusca');
  const btnFecharBusca = document.getElementById('btnFecharBusca');
  const siteNav = document.getElementById('siteNav');
  const buscaInline = document.getElementById('buscaInline');
  const inputBusca = document.getElementById('inputBusca');

  if (btnBusca && btnFecharBusca) {
    btnBusca.addEventListener('click', () => {
      if (!buscaInline.classList.contains('ativo')) {
        // 1º Clique: Abre a barra de pesquisa
        buscaInline.classList.add('ativo');
        siteNav.classList.add('escondido');
        btnFecharBusca.style.display = 'inline-block';
        inputBusca.focus();
      } else {
        // 2º Clique: Executa a busca (ação real)
        if (inputBusca.value.trim() !== '') {
          alert('Buscando por: ' + inputBusca.value);
        }
      }
    });

    btnFecharBusca.addEventListener('click', () => {
      // Fecha a barra e restaura o menu
      buscaInline.classList.remove('ativo');
      siteNav.classList.remove('escondido');
      btnFecharBusca.style.display = 'none';
      inputBusca.value = '';
    });
  }
});