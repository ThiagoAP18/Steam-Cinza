// Funções auxiliares
function q(sel, root = document) { return root.querySelector(sel); }
function qAll(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

// Inicialização principal
document.addEventListener('DOMContentLoaded', () => {
    // Apenas inicializa componentes de UI globais
    initNotifications();
    initLogin();
    
    // Se houver lógica específica de validação de formulário de vendedor, pode ficar aqui
    // mas a renderização de produtos e rotas agora é com o Laravel.
});


// ===== SISTEMA DE NOTIFICAÇÕES =====
let notifications = [];

// Carrega notificações com segurança (evita crash se o JSON for inválido)
try {
    const data = localStorage.getItem("notifications");
    notifications = data ? JSON.parse(data) : [];
} catch (e) {
    console.error("Erro ao ler notificações, resetando:", e);
    localStorage.removeItem("notifications");
    notifications = [];
}

function initNotifications() {
    updateNotificationBadge();

    const bell = q(".notif-btn");
    const panel = q(".notif-panel");

    if (bell && panel) {
        bell.addEventListener("click", (e) => {
            e.stopPropagation(); // Previne propagação indesejada
            panel.classList.toggle("open");
            renderNotifications();
        });

        // Opcional: Fechar ao clicar fora do painel
        document.addEventListener("click", (e) => {
            if (panel.classList.contains("open") && 
                !panel.contains(e.target) && 
                !bell.contains(e.target)) {
                panel.classList.remove("open");
            }
        });
    }
}

function addNotification(text) {
    notifications.push({
        text,
        date: new Date().toLocaleString()
    });
    localStorage.setItem("notifications", JSON.stringify(notifications));
    updateNotificationBadge();
    renderNotifications();
}

function updateNotificationBadge() {
    const badge = q(".notif-badge");
    if (!badge) return;

    const count = notifications.length;
    badge.textContent = count;
    badge.style.display = count > 0 ? "flex" : "none";
}

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


// ===== LOGIN (UI Apenas) =====
function initLogin() {
    const loginForm = document.getElementById("login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            // Nota: Em produção, o Laravel lidará com o submit via POST real.
            // Mantendo apenas se quiser interceptar algo visualmente antes do envio.
            // Se for login 100% Laravel, você pode remover este bloco inteiro.
            
            // e.preventDefault(); 
            // Lógica de feedback visual aqui...
        });
    }
}