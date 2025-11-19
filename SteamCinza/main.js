/* main.js
 - Navegação de caixas e cards
 - Preenchimento das pages search/product
 - Aprovar vendedor (simulação)
*/

// Exemplo de "base de dados" local (substitua pelos seus dados)
const GAMES = {
  "101": { id: "101", name: "Jogo A", price: "R$ 49,90", seller: "Usuário 1", desc: "Descrição do Jogo A", video: "" },
  "102": { id: "102", name: "Jogo B", price: "R$ 89,90", seller: "Usuário 2", desc: "Descrição do Jogo B", video: "" },
  "103": { id: "103", name: "Jogo C", price: "R$ 19,90", seller: "Usuário 3", desc: "Descrição do Jogo C", video: "" },
  "104": { id: "104", name: "Jogo D", price: "R$ 39,90", seller: "Usuário 4", desc: "Descrição do Jogo D", video: "" }
};

// --- Helpers
function q(sel, root = document) { return root.querySelector(sel); }
function qAll(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

// Ao carregar o documento
document.addEventListener('DOMContentLoaded', () => {
  attachHeaderListeners();
  attachBoxListeners();
  attachCardListeners();

  // Roteamento simples por pathname
  const path = location.pathname.split('/').pop();

  if (path === 'search.html') {
    renderSearchFromParams();
  } else if (path === 'product.html') {
    renderProductFromParams();
  } else if (path === 'vendor.html') {
    attachVendorListeners();
  } else {
    // page index: podemos popular com jogos
    populateHomeCards();
  }
});

// --- Header interactions: busca
function attachHeaderListeners() {
  const input = q('#search-input');
  if (!input) return;
  input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const qv = input.value.trim();
      if (qv) location.href = `search.html?q=${encodeURIComponent(qv)}`;
    }
  });
  // search button click
  qAll('.search-btn').forEach(btn => btn.addEventListener('click', (e) => {
    const ip = btn.closest('.site-header')?.querySelector('#search-input');
    const qv = ip && ip.value.trim();
    if (qv) location.href = `search.html?q=${encodeURIComponent(qv)}`;
  }));
}

// --- Boxes (caixas)
function attachBoxListeners() {
  qAll('.box').forEach(b => {
    b.addEventListener('click', () => {
      const box = b.dataset.box;
      location.href = `search.html?box=${box}`;
    });
  });
}

// --- Cards (pill)
function attachCardListeners() {
  // existing cards
  qAll('.pill').forEach(p => {
    p.addEventListener('click', () => {
      const id = p.dataset.id;
      if (id) location.href = `product.html?id=${id}`;
    });
    p.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') p.click();
    });
  });
}

// --- Home: popular cards dynamically (merge com static fallback)
function populateHomeCards() {
  const container = q('#home-cards');
  if (!container) return;
  container.innerHTML = '';
  Object.values(GAMES).forEach(g => {
    const art = document.createElement('article');
    art.className = 'card pill';
    art.dataset.id = g.id;
    art.tabIndex = 0;
    art.innerHTML = `<div class="card-image" aria-hidden="true"></div><h3 class="card-title">${g.name}</h3>`;
    art.addEventListener('click', () => location.href = `product.html?id=${g.id}`);
    container.appendChild(art);
  });
  attachCardListeners(); // reattach
}

// --- Search page: ler params e popular resultados
function renderSearchFromParams() {
  const params = new URLSearchParams(location.search);
  const box = params.get('box');
  const q = params.get('q') || '';
  const title = q ? `Resultados para "${q}"` : (box ? `Relacionados à caixa ${box}` : 'Resultados');
  const titleEl = q('#search-title');
  if (titleEl) titleEl.textContent = title;

  const grid = q('#result-grid');
  if (!grid) return;
  grid.innerHTML = '';

  // filtro simples: se tiver query, filtra por nome; se tiver box, mostra subset
  let results = Object.values(GAMES);
  if (q) {
    const qq = q.toLowerCase();
    results = results.filter(g => g.name.toLowerCase().includes(qq));
  } else if (box) {
    // ex: simula relação entre caixa e ids
    const map = { '1': ['101','102'], '2': ['103','104'], '3': ['101','104'] };
    const ids = map[box] || Object.keys(GAMES);
    results = ids.map(id => GAMES[id]).filter(Boolean);
  }

  if (results.length === 0) {
    grid.innerHTML = '<p>Nenhum resultado</p>';
    return;
  }

  results.forEach(g => {
    const art = document.createElement('article');
    art.className = 'card pill';
    art.dataset.id = g.id;
    art.tabIndex = 0;
    art.innerHTML = `<div class="card-image" aria-hidden="true"></div><h3 class="card-title">${g.name}</h3><p class="card-price">${g.price}</p>`;
    art.addEventListener('click', () => location.href = `product.html?id=${g.id}`);
    grid.appendChild(art);
  });
}

// --- Product page: ler id da url e preencher
function renderProductFromParams() {
  const params = new URLSearchParams(location.search);
  const id = params.get('id');
  const prod = GAMES[id] || Object.values(GAMES)[0];
  if (!prod) return;

  const nameEl = q('#prod-name');
  const sellerEl = q('#prod-seller');
  const priceEl = q('#prod-price');
  const descEl = q('#prod-desc');
  const videoEl = q('#prod-video');

  if (nameEl) nameEl.textContent = prod.name;
  if (sellerEl) sellerEl.textContent = `De ${prod.seller}`;
  if (priceEl) priceEl.textContent = prod.price;
  if (descEl) descEl.textContent = prod.desc;
  if (videoEl && prod.video) {
    videoEl.src = prod.video;
  }
}

// --- Vendor interactions
function attachVendorListeners() {
  const btn = q('#approve-btn');
  if (!btn) return;
  btn.addEventListener('click', () => {
    const form = q('#vendor-form');
    const price = form?.price?.value || '';
    const buy = form?.modal_buy?.checked;
    const rent = form?.modal_rent?.checked;
    const trade = form?.modal_trade?.checked;

    if (!price || Number(price) <= 0) {
      alert('Defina um preço válido.');
      return;
    }
    if (!buy && !rent && !trade) {
      alert('Selecione ao menos uma modalidade.');
      return;
    }
    // aqui você chamaria sua API; por enquanto só simula
    alert('Produto aprovado! (simulado)');
  });
}
