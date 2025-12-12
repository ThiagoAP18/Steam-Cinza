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

//Add Funds

function setAmount(value) {
        document.getElementById('amount').value = value.toFixed(2);
}

//Flash Messages
document.addEventListener('DOMContentLoaded', function() {
    const flash = document.getElementById('flash-message');
    if (flash) {
        setTimeout(() => {
            closeFlash();
        }, 5000);
    }
});

function closeFlash() {
    const flash = document.getElementById('flash-message');
    if (flash) {
        flash.style.animation = 'slideOut 0.5s forwards';
        setTimeout(() => {
            flash.remove();
        }, 500);
    }
}

//Modal Button
const FEE_PERCENT_RENT_BASE = 0.4; 
const FEE_PERCENT_RENT_DAILY = 0.05; 
const FEE_PERCENT_SALE = 0.50;

function openAnnounceModal(licenseId, gameName) {
    document.getElementById('modalLicenseId').value = licenseId;
    document.getElementById('modalGameTitle').innerText = 'Anunciar: ' + gameName;
    document.getElementById('announceModal').style.display = 'flex';
    
    document.getElementById('announceForm').reset();
    document.getElementById('saleEarnings').innerText = "Você recebe: R$ 0,00";
    document.getElementById('rentEarnings').innerText = "Você recebe: R$ 0,00";
    
    toggleOptions(); 
}

function closeAnnounceModal() {
    document.getElementById('announceModal').style.display = 'none';
}

function toggleOptions() {
    const isSale = document.getElementById('checkSale').checked;
    const isRent = document.getElementById('checkRent').checked;

    document.getElementById('saleInputs').style.display = isSale ? 'block' : 'none';
    document.getElementById('rentInputs').style.display = isRent ? 'block' : 'none';
    validateInputs();
}

function validateInputs() {
    const isSale = document.getElementById('checkSale').checked;
    const isRent = document.getElementById('checkRent').checked;
    
    const salePrice = parseFloat(document.getElementById('salePrice').value) || 0;
    const rentPrice = parseFloat(document.getElementById('rentPrice').value) || 0;
    const rentDays = parseInt(document.getElementById('rentDays').value) || 0;

    let isValid = true;
    let msg = "Selecione pelo menos uma opção.";

    if (!isSale && !isRent) {
        isValid = false;
    } else {
        if (isSale) {
            document.getElementById('salePrice').setAttribute('required', 'required');
            if (salePrice <= 0) {
                isValid = false;
                msg = "Valor da venda deve ser maior que R$ 0,00.";
            }
        } else {
            document.getElementById('salePrice').removeAttribute('required');
        }

        if (isRent) {
            document.getElementById('rentPrice').setAttribute('required', 'required');
            document.getElementById('rentDays').setAttribute('required', 'required');
            
            if (rentPrice <= 0) {
                isValid = false;
                msg = "Valor do aluguel deve ser maior que R$ 0,00.";
            }
            if (rentDays < 1 || rentDays > 7) {
                isValid = false;
                msg = "Dias de aluguel devem ser entre 1 e 7.";
            }
        } else {
            document.getElementById('rentPrice').removeAttribute('required');
            document.getElementById('rentDays').removeAttribute('required');
        }
    }

    const btn = document.getElementById('btnConfirm');
    const error = document.getElementById('errorMsg');

    if (!isValid) {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        error.style.display = 'block';
        error.innerText = msg;
    } else {
        btn.disabled = false;
        btn.style.opacity = '1';
        error.style.display = 'none';
    }
}

function formatMoney(value) {
    return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function calcSale() {
    const price = parseFloat(document.getElementById('salePrice').value) || 0;
    const net = price - (price * FEE_PERCENT_SALE);
    
    document.getElementById('saleEarnings').innerText = `Você recebe: ${formatMoney(net)} (Taxa: 50%)`;
    validateInputs();
}

function calcRent() {
    const price = parseFloat(document.getElementById('rentPrice').value) || 0;
    const days = parseFloat(document.getElementById('rentDays').value) || 0;

    let sharePercentage = (FEE_PERCENT_RENT_DAILY * days) + FEE_PERCENT_RENT_BASE;
    
    if(sharePercentage > 1) 
        sharePercentage = 1;

    const net = price * sharePercentage;
    const percentageDisplay = (sharePercentage * 100).toFixed(0);

    document.getElementById('rentEarnings').innerText = `Você recebe: ${formatMoney(net)} (Sua Parte: ${percentageDisplay}%)`;
    validateInputs();
}