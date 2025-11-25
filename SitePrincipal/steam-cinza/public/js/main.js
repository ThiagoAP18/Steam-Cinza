function q(sel, root = document) { return root.querySelector(sel); }
function qAll(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

document.addEventListener('DOMContentLoaded', () => {
    initNotifications();
    initLogin();
});


// Notifications System
let notifications = [];

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


// UI Login
function initLogin() {
    const loginForm = document.getElementById("login-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
        });
    }
}

// Toggle Main Menu
function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

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