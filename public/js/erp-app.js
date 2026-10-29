// ========== DATE/TIME HELPER FUNCTIONS ==========
const MONTHS_SHORT = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
const DAYS_FULL = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

function formatDate(d) {
    return d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()] + ' ' + d.getFullYear();
}
function formatTime12(d) {
    let h = d.getHours(), m = d.getMinutes();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + String(m).padStart(2, '0') + ' ' + ampm;
}
function formatDateTime(d) {
    return formatDate(d) + ' | ' + formatTime12(d);
}
function formatShortDateTime(d) {
    return d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()] + ' ' + formatTime12(d);
}
function getLast7DayLabels() {
    const labels = [];
    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        labels.push(d.getDate() + ' ' + MONTHS_SHORT[d.getMonth()]);
    }
    return labels;
}

// ========== LIVE CLOCK (updates every second) ==========
function updateLiveClock() {
    const now = new Date();

    const headerDT = document.getElementById('liveDateTime');
    const headerDay = document.getElementById('liveDay');
    if (headerDT) headerDT.textContent = formatDateTime(now);
    if (headerDay) headerDay.textContent = DAYS_FULL[now.getDay()];

    const wTime = document.getElementById('weatherTime');
    const wDate = document.getElementById('weatherDate');
    const wDay = document.getElementById('weatherDay');
    if (wTime) wTime.textContent = formatTime12(now);
    if (wDate) wDate.textContent = formatDate(now);
    if (wDay) wDay.textContent = DAYS_FULL[now.getDay()];

    const lastSync = document.getElementById('lastSyncTime');
    if (lastSync) lastSync.textContent = formatDate(now) + ' ' + formatTime12(now);
}

// ========== POPULATE ORDER TABLE DATES ==========
function populateOrderDates() {
    document.querySelectorAll('.live-date-today-time').forEach(el => {
        const offsetHours = parseInt(el.dataset.offsetHours || '0');
        const d = new Date();
        d.setHours(d.getHours() + offsetHours);
        el.textContent = formatShortDateTime(d);
    });
}

// ========== ERP MODE SWITCHER ==========
function switchErpMode(mode) {
    const btnPackage = document.getElementById('btnPackageERP');
    const btnFactory = document.getElementById('btnFactoryERP');
    const packageMenu = document.getElementById('packageMenuContainer');
    const factoryMenu = document.getElementById('factoryMenuContainer');
    const modeLabel = document.getElementById('currentModeLabel');
    const modeIcon = document.getElementById('currentModeIcon');

    if (mode === 'package') {
        if (btnPackage) btnPackage.classList.add('active');
        if (btnFactory) btnFactory.classList.remove('active');
        if (packageMenu) packageMenu.classList.remove('hidden');
        if (factoryMenu) factoryMenu.classList.add('hidden');
        if (modeLabel) {
            modeLabel.innerText = 'Package ERP Mode';
            modeLabel.className = 'text-[11px] font-bold text-cyan-300 uppercase tracking-wider';
        }
        if (modeIcon) modeIcon.className = 'fa-solid fa-box-open text-xs text-cyan-400';
        localStorage.setItem('tbg_erp_mode', 'package');
    } else {
        if (btnFactory) btnFactory.classList.add('active');
        if (btnPackage) btnPackage.classList.remove('active');
        if (factoryMenu) factoryMenu.classList.remove('hidden');
        if (packageMenu) packageMenu.classList.add('hidden');
        if (modeLabel) {
            modeLabel.innerText = 'Factory ERP Mode';
            modeLabel.className = 'text-[11px] font-bold text-blue-300 uppercase tracking-wider';
        }
        if (modeIcon) modeIcon.className = 'fa-solid fa-industry text-xs text-blue-400';
        localStorage.setItem('tbg_erp_mode', 'factory');
    }
}

// ========== CHARTS INITIALIZATION ==========
function initSalesOverviewChart() {
    const el = document.getElementById('salesOverviewChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    
    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(14, 165, 233, 0.45)');
    gradient.addColorStop(1, 'rgba(14, 165, 233, 0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: getLast7DayLabels(),
            datasets: [{
                label: 'Sales (৳)',
                data: [120000, 190000, 165000, 230000, 210000, 280000, 245680],
                borderColor: '#00f2fe',
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#00f2fe',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                fill: true,
                backgroundColor: gradient
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(10, 18, 42, 0.9)',
                    titleColor: '#00f2fe',
                    bodyColor: '#ffffff',
                    borderColor: 'rgba(56, 189, 248, 0.3)',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            return ' ৳ ' + context.parsed.y.toLocaleString('en-IN');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', font: { size: 10 } }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 10 },
                        callback: function(value) {
                            return '৳ ' + (value / 1000) + 'k';
                        }
                    }
                }
            }
        }
    });
}

function initCategoryDonutChart() {
    const el = document.getElementById('categoryDonutChart');
    if (!el) return;
    const ctx = el.getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ["Men's Innerwear", "Women's Innerwear", "Socks", "T-Shirt", "Others"],
            datasets: [{
                data: [32, 24, 15, 12, 17],
                backgroundColor: [
                    '#38bdf8',
                    '#c084fc',
                    '#3b82f6',
                    '#34d399',
                    '#f472b6'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(10, 18, 42, 0.9)',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

// ========== INIT ON DOM READY ==========
document.addEventListener('DOMContentLoaded', () => {
    const savedMode = localStorage.getItem('tbg_erp_mode') || 'factory';
    switchErpMode(savedMode);
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
    populateOrderDates();
    initSalesOverviewChart();
    initCategoryDonutChart();
});
