// Função auxiliar para selecionar um único elemento
function q(sel, root = document) { return root.querySelector(sel); }

// Função auxiliar para selecionar vários elementos e converter em array
function qAll(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

// Aguarda carregamento do DOM
document.addEventListener('DOMContentLoaded', () => {
    initNotifications(); // Inicializa sistema de notificações
    initLogin();         // Inicializa lógica de login
});


// -----------------------------
// Sistema de Notificações
// -----------------------------

let notifications = []; // Array que armazena as notificações

try {
    // Tenta carregar notificações salvas no localStorage
    const data = localStorage.getItem("notifications");
    notifications = data ? JSON.parse(data) : [];
} catch (e) {
    // Caso dê erro no parse, reseta tudo
    console.error("Erro ao ler notificações, resetando:", e);
    localStorage.removeItem("notifications");
    notifications = [];
}

function initNotifications() {
    updateNotificationBadge(); // Atualiza contador na interface

    const bell = q(".notif-btn");   // Botão do sino
    const panel = q(".notif-panel"); // Painel de notificações

    if (bell && panel) {
        // Clique no sino abre/fecha o painel
        bell.addEventListener("click", (e) => {
            e.stopPropagation(); // Evita que o clique feche o painel
            panel.classList.toggle("open");
            renderNotifications(); // Renderiza a lista
        });

        // Clique fora fecha o painel
        document.addEventListener("click", (e) => {
            if (panel.classList.contains("open") && 
                !panel.contains(e.target) && 
                !bell.contains(e.target)) {
                panel.classList.remove("open");
            }
        });
    }
}

// Adiciona notificação ao array e salva no localStorage
function addNotification(text) {
    notifications.push({
        text,
        date: new Date().toLocaleString()
    });
    localStorage.setItem("notifications", JSON.stringify(notifications));
    updateNotificationBadge();
    renderNotifications();
}

// Atualiza o badge (contador de notificações)
function updateNotificationBadge() {
    const badge = q(".notif-badge");
    if (!badge) return;

    const count = notifications.length;
    badge.textContent = count;
    badge.style.display = count > 0 ? "flex" : "none";
}

// Renderiza a lista de notificações no painel
function renderNotifications() {
    const box = q(".notif-list");
    if (!box) return;

    if (notifications.length === 0) {
        box.innerHTML = '<div class="notif-item"><p>Nenhuma notificação</p></div>';
        return;
    }

    box.innerHTML = notifications
        .map(n => `<div class="notif-item"><p>${n.text}</p><span>${n.date}</span></div>`)
        .join("");
}


// -----------------------------
// Sistema de Login (UI)
// -----------------------------

function initLogin() {
    const loginForm = document.getElementById("login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            // Evento existente para submissão do formulário (vazio)
        });
    }
}


// -----------------------------
// Menu do Usuário
// -----------------------------

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('active'); // Abre/fecha o menu
}

// Fecha o menu ao clicar fora dele
window.onclick = function(event) {
    if (!event.target.matches('.user-menu-trigger') && !event.target.matches('.user-menu-trigger *')) {
        var dropdowns = document.getElementsByClassName("user-dropdown");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('active')) {
                openDropdown.classList.remove('active');
            }
        }
    }
}


// -----------------------------
// Adicionar Fundos
// -----------------------------

// Define o valor do input ao clicar em botões rápidos
function setAmount(value) {
    document.getElementById('amount').value = value.toFixed(2);
}


// -----------------------------
// Flash Messages
// -----------------------------

document.addEventListener('DOMContentLoaded', function() {
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            closeFlash(); // Remove o alerta após 5 segundos
        }, 5000);
    }
});

// Anima e remove a mensagem flash
function closeFlash() {
    const flash = document.getElementById('flash-message');
    if (flash) {
        flash.style.animation = 'slideOut 0.5s forwards';
        setTimeout(() => {
            flash.remove();
        }, 500);
    }
}


// -----------------------------
// Modal de Anúncio de Licença
// -----------------------------

const FEE_PERCENT_RENT = 0.55; // Taxa para aluguel
const FEE_PERCENT_SALE = 0.50; // Taxa para venda

// Abre modal e preenche informações iniciais
function openAnnounceModal(licenseId, gameName) {
    document.getElementById('modalLicenseId').value = licenseId;
    document.getElementById('modalGameTitle').innerText = 'Anunciar: ' + gameName;
    document.getElementById('announceModal').style.display = 'flex';
    
    document.getElementById('announceForm').reset(); // Limpa form
    toggleOptions(); // Reseta opções
}

// Fecha modal de anúncio
function closeAnnounceModal() {
    document.getElementById('announceModal').style.display = 'none';
}

// Controla exibição e obrigatoriedade dos campos de venda e aluguel
function toggleOptions() {
    const isSale = document.getElementById('checkSale').checked;
    const isRent = document.getElementById('checkRent').checked;

    document.getElementById('saleInputs').style.display = isSale ? 'block' : 'none';
    document.getElementById('rentInputs').style.display = isRent ? 'block' : 'none';

    document.getElementById('salePrice').required = isSale;
    document.getElementById('rentPrice').required = isRent;
    document.getElementById('rentDays').required = isRent;

    const btn = document.getElementById('btnConfirm');
    const error = document.getElementById('errorMsg');

    // Desabilita botão se nenhuma opção estiver marcada
    if (!isSale && !isRent) {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        error.style.display = 'block';
    } else {
        btn.disabled = false;
        btn.style.opacity = '1';
        error.style.display = 'none';
    }
}

// Formata número para moeda BRL
function formatMoney(value) {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

// Calcula ganhos na venda considerando taxa
function calcSale() {
    const price = parseFloat(document.getElementById('salePrice').value) || 0;
    const net = price - (price * FEE_PERCENT_SALE);
    document.getElementById('saleEarnings').innerText = `Você recebe: ${formatMoney(net)} (Taxa: 50%)`;
}

// Calcula ganhos no aluguel considerando taxa
function calcRent() {
    const price = parseFloat(document.getElementById('rentPrice').value) || 0;
    const net = price - (price * FEE_PERCENT_RENT);
    document.getElementById('rentEarnings').innerText = `Você recebe: ${formatMoney(net)} (Taxa: 55%)`;
}
