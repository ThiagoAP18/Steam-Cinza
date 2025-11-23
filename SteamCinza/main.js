const GAMES = {
  "101": { id: "101", name: "Jogo A", price: "R$ 49,90", seller: "Usuário 1", desc: "Descrição do Jogo A", video: "" },
  "102": { id: "102", name: "Jogo B", price: "R$ 89,90", seller: "Usuário 2", desc: "Descrição do Jogo B", video: "" },
  "103": { id: "103", name: "Jogo C", price: "R$ 19,90", seller: "Usuário 3", desc: "Descrição do Jogo C", video: "" },
  "104": { id: "104", name: "Jogo D", price: "R$ 39,90", seller: "Usuário 4", desc: "Descrição do Jogo D", video: "" }
};

function q(sel, root = document) { return root.querySelector(sel); }
function qAll(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

document.addEventListener('DOMContentLoaded', () => {
  attachHeaderListeners();
  attachBoxListeners();
  attachCardListeners();
  const path = location.pathname.split('/').pop();
  if (path === 'search.html') {
    renderSearchFromParams();
  } else if (path === 'product.html') {
    renderProductFromParams();
  } else if (path === 'vendor.html') {
    attachVendorListeners();
  } else {
    populateHomeCards();
  }
});

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
  qAll('.search-btn').forEach(btn => btn.addEventListener('click', (e) => {
    const ip = btn.closest('.site-header')?.querySelector('#search-input');
    const qv = ip && ip.value.trim();
    if (qv) location.href = `search.html?q=${encodeURIComponent(qv)}`;
  }));
}

function attachBoxListeners() {
  qAll('.box').forEach(b => {
    b.addEventListener('click', () => {
      const box = b.dataset.box;
      location.href = `search.html?box=${box}`;
    });
  });
}

function attachCardListeners() {
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
  attachCardListeners();
}

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
  let results = Object.values(GAMES);
  if (q) {
    const qq = q.toLowerCase();
    results = results.filter(g => g.name.toLowerCase().includes(qq));
  } else if (box) {
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

function renderProductFromParams() {
  const params = new URLSearchParams(location.search);
  const id = params.get('id');
  const prod = GAMES[id] || Object.values(GAMES)[0];
  if (!prod) return;
  const nameEl = q('#prod-name');
  const sellerEl = q('#prod-seller');
  const priceEl = q('#prod-price');
  const descEl = q('#prod-desc');
  const thumbsEl = q('#thumbs');
  const starsEl = q('#stars');
  if (nameEl) nameEl.textContent = prod.name;
  if (sellerEl) sellerEl.textContent = `De ${prod.seller}`;
  if (priceEl) priceEl.textContent = prod.price;
  if (descEl) descEl.textContent = prod.desc;
  if (thumbsEl) {
    thumbsEl.innerHTML = '';
    for (let i=0;i<4;i++){
      const b = document.createElement('button');
      b.className = 'thumb';
      b.textContent = 'IMAGEM';
      thumbsEl.appendChild(b);
    }
  }
  if (starsEl) starsEl.textContent = '★★★★★';
}

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
    alert('Produto aprovado! (simulado)');
  });
}

// ===== LOGIN =====
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Simulação de login
            const email = document.getElementById("login-email").value;
            const pass = document.getElementById("login-password").value;

            if (email !== "" && pass !== "") {
                localStorage.setItem("user_logged", "true");
                window.location.href = "index.html";
            }
        });
    }

    // Checar login nas páginas
    if (!localStorage.getItem("user_logged") && !window.location.href.includes("login.html")) {
        window.location.href = "login.html";
    }
});


// ===== SISTEMA DE NOTIFICAÇÕES =====
let notifications = JSON.parse(localStorage.getItem("notifications")) || [];

function addNotification(text) {
    notifications.push({
        text,
        date: new Date().toLocaleString()
    });
    localStorage.setItem("notifications", JSON.stringify(notifications));
    updateNotificationBadge();
}

function updateNotificationBadge() {
    const badge = document.querySelector(".notif-badge");
    if (!badge) return;

    const count = notifications.length;
    badge.textContent = count;
    badge.style.display = count > 0 ? "flex" : "none";
}

document.addEventListener("DOMContentLoaded", () => {
    updateNotificationBadge();

    // Abrir painel
    const bell = document.querySelector(".notif-btn");
    const panel = document.querySelector(".notif-panel");

    if (bell && panel) {
        bell.addEventListener("click", () => {
            panel.classList.toggle("open");
            renderNotifications();
        });
    }
});

// Renderizar lista de notificações
function renderNotifications() {
    const box = document.querySelector(".notif-list");
    if (!box) return;

    box.innerHTML = notifications
        .map(n => `<div class="notif-item"><p>${n.text}</p><span>${n.date}</span></div>`)
        .join("");
}
