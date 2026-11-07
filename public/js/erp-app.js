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

    const labels = (window.ERP_DASHBOARD && window.ERP_DASHBOARD.chartDates && window.ERP_DASHBOARD.chartDates.length) 
        ? window.ERP_DASHBOARD.chartDates 
        : getLast7DayLabels();

    const data = (window.ERP_DASHBOARD && window.ERP_DASHBOARD.chartSales && window.ERP_DASHBOARD.chartSales.length) 
        ? window.ERP_DASHBOARD.chartSales 
        : [0, 0, 0, 0, 0, 0, 0];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales (৳)',
                data: data,
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
                            if (value >= 1000) {
                                return '৳ ' + (value / 1000) + 'k';
                            }
                            return '৳ ' + value;
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

    const labels = (window.ERP_DASHBOARD && window.ERP_DASHBOARD.donutLabels && window.ERP_DASHBOARD.donutLabels.length)
        ? window.ERP_DASHBOARD.donutLabels
        : ["No Products"];

    const data = (window.ERP_DASHBOARD && window.ERP_DASHBOARD.donutData && window.ERP_DASHBOARD.donutData.length)
        ? window.ERP_DASHBOARD.donutData
        : [100];

    const colors = (window.ERP_DASHBOARD && window.ERP_DASHBOARD.donutColors && window.ERP_DASHBOARD.donutColors.length)
        ? window.ERP_DASHBOARD.donutColors
        : ['#00f2fe', '#a855f7', '#3b82f6', '#10b981', '#ec4899'];

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
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
    const isFactory = window.location.pathname.includes('/factory');
    const savedMode = isFactory ? 'factory' : (localStorage.getItem('tbg_erp_mode') || 'factory');
    switchErpMode(savedMode);
    updateLiveClock();
    setInterval(updateLiveClock, 1000);
    populateOrderDates();
    initSalesOverviewChart();
    initCategoryDonutChart();
});

// ==========================================================================
// UNIVERSAL ERP TABLE MANAGER (PROGRESSIVE LOAD MORE & REAL-TIME SEARCH)
// ==========================================================================
window.initErpTableManager = function(options) {
    if (!options) return null;

    const id = options.id || '';
    const tableBodySelector = options.tableBody || (id ? `#${id}TableBody` : null);
    if (!tableBodySelector) return null;

    const tbody = typeof tableBodySelector === 'string' 
        ? document.querySelector(tableBodySelector) 
        : tableBodySelector;
    if (!tbody) return null;

    const rowSelector = options.rowSelector || 'tr:not(.no-search-row):not(.table-header-row)';
    const pageSize = options.pageSize || 10;
    const scrollOffset = options.scrollOffset !== undefined ? options.scrollOffset : 140;

    const searchInput = options.searchInput 
        ? (typeof options.searchInput === 'string' ? document.querySelector(options.searchInput) : options.searchInput)
        : (id ? document.querySelector(`#${id}SearchInput`) : null);

    const clearSearchBtn = options.clearSearchBtn 
        ? (typeof options.clearSearchBtn === 'string' ? document.querySelector(options.clearSearchBtn) : options.clearSearchBtn)
        : (id ? document.querySelector(`#${id}ClearSearchBtn`) : null);

    const loadMoreBtn = options.loadMoreBtn 
        ? (typeof options.loadMoreBtn === 'string' ? document.querySelector(options.loadMoreBtn) : options.loadMoreBtn)
        : (id ? document.querySelector(`#${id}LoadMoreBtn`) : null);

    const loadMoreContainer = options.loadMoreContainer 
        ? (typeof options.loadMoreContainer === 'string' ? document.querySelector(options.loadMoreContainer) : options.loadMoreContainer)
        : (id ? document.querySelector(`#${id}LoadMoreContainer`) : null);

    const allLoadedMsg = options.allLoadedMsg 
        ? (typeof options.allLoadedMsg === 'string' ? document.querySelector(options.allLoadedMsg) : options.allLoadedMsg)
        : (id ? document.querySelector(`#${id}AllLoadedMsg`) : null);

    const noResultRow = options.noResultRow 
        ? (typeof options.noResultRow === 'string' ? document.querySelector(options.noResultRow) : options.noResultRow)
        : (id ? document.querySelector(`#${id}NoResultRow`) : null);

    const scrollContainer = options.scrollContainer 
        ? (typeof options.scrollContainer === 'string' ? document.querySelector(options.scrollContainer) : options.scrollContainer)
        : (tbody.closest('.erp-table-scroll') || tbody.closest('.clients-scrollbar') || tbody.parentElement);

    // Collect visible and total count elements
    let visibleCountEls = [];
    if (options.visibleCountEl) {
        visibleCountEls = typeof options.visibleCountEl === 'string'
            ? Array.from(document.querySelectorAll(options.visibleCountEl))
            : [options.visibleCountEl];
    } else if (id) {
        const defaultEl = document.querySelector(`#${id}VisibleCount`);
        if (defaultEl) visibleCountEls.push(defaultEl);
    }

    let totalCountEls = [];
    if (options.totalCountEl) {
        totalCountEls = typeof options.totalCountEl === 'string'
            ? Array.from(document.querySelectorAll(options.totalCountEl))
            : [options.totalCountEl];
    } else if (id) {
        const defaultEl = document.querySelector(`#${id}TotalCount`);
        if (defaultEl) totalCountEls.push(defaultEl);
    }

    let currentLimit = pageSize;

    function getSearchableText(row) {
        const ds = row.getAttribute('data-search');
        if (ds) return ds.toLowerCase();
        return (row.innerText || row.textContent || '').toLowerCase();
    }

    function setCounts(visible, total) {
        visibleCountEls.forEach(el => { if (el) el.textContent = visible; });
        totalCountEls.forEach(el => { if (el) el.textContent = total; });
    }

    function applyVisibility() {
        const rows = Array.from(tbody.querySelectorAll(rowSelector));
        const searchVal = searchInput ? searchInput.value.trim().toLowerCase() : '';

        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('hidden', searchVal === '');
        }

        if (searchVal !== '') {
            const queryWords = searchVal.split(/\s+/).filter(w => w.length > 0);
            let matched = 0;

            rows.forEach(row => {
                const text = getSearchableText(row);
                const isMatch = queryWords.every(word => text.includes(word));
                if (isMatch) {
                    row.classList.remove('hidden');
                    row.style.display = '';
                    matched++;
                } else {
                    row.classList.add('hidden');
                    row.style.display = 'none';
                }
            });

            if (noResultRow) {
                noResultRow.classList.toggle('hidden', matched > 0);
                noResultRow.style.display = matched > 0 ? 'none' : '';
                const queryTextDisplay = noResultRow.querySelector('.queryTextDisplay');
                if (queryTextDisplay) queryTextDisplay.textContent = searchVal;
            }

            if (loadMoreBtn) loadMoreBtn.classList.add('hidden');
            if (allLoadedMsg) allLoadedMsg.classList.add('hidden');
            setCounts(matched, rows.length);

        } else {
            if (noResultRow) {
                noResultRow.classList.add('hidden');
                noResultRow.style.display = 'none';
            }

            let visibleCount = 0;
            rows.forEach((row, index) => {
                if (index < currentLimit) {
                    row.classList.remove('hidden');
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                    row.style.display = 'none';
                }
            });

            const hasMore = currentLimit < rows.length;
            if (loadMoreBtn) {
                loadMoreBtn.classList.toggle('hidden', !hasMore);
            }
            if (loadMoreContainer) {
                loadMoreContainer.classList.remove('hidden');
            }
            if (allLoadedMsg) {
                allLoadedMsg.classList.toggle('hidden', hasMore || rows.length === 0);
            }

            setCounts(visibleCount, rows.length);
        }
    }

    function loadMore() {
        currentLimit += pageSize;
        applyVisibility();

        if (scrollContainer && scrollOffset > 0) {
            setTimeout(() => {
                scrollContainer.scrollBy({
                    top: scrollOffset,
                    behavior: 'smooth'
                });
            }, 60);
        }
    }

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', (e) => {
            e.preventDefault();
            loadMore();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyVisibility);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
            applyVisibility();
        });
    }

    // Initial setup
    applyVisibility();

    return {
        refresh: applyVisibility,
        loadMore: loadMore,
        reset: function() {
            currentLimit = pageSize;
            if (searchInput) searchInput.value = '';
            applyVisibility();
        }
    };
};


