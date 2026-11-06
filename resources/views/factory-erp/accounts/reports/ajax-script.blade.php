<script>
function handleReportTabClick(e, url) {
    if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
    e.preventDefault();
    loadReportTab(url);
}

function handleReportFormSubmit(e, form) {
    e.preventDefault();
    const action = form.action || window.location.href;
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const targetUrl = action.split('?')[0] + '?' + params;
    loadReportTab(targetUrl);
}

let activeReportController = null;

async function loadReportTab(url, push = true) {
    const container = document.getElementById('reportTabContent');
    if (!container) {
        window.location.href = url;
        return;
    }

    if (activeReportController) {
        activeReportController.abort();
    }
    activeReportController = new AbortController();

    // Highlight target tab immediately
    highlightActiveTab(url);

    // Visual loading state
    container.style.transition = 'opacity 0.15s ease';
    container.style.opacity = '0.4';
    container.style.pointerEvents = 'none';

    try {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: activeReportController.signal
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newContent = doc.getElementById('reportTabContent');
        const newNav = doc.getElementById('reportTabsNav');
        const newHeader = doc.getElementById('reportPageHeader');

        if (newContent) {
            container.innerHTML = newContent.innerHTML;
        }
        if (newNav) {
            const currentNav = document.getElementById('reportTabsNav');
            if (currentNav) currentNav.innerHTML = newNav.innerHTML;
        }
        if (newHeader) {
            const currentHeader = document.getElementById('reportPageHeader');
            if (currentHeader) currentHeader.innerHTML = newHeader.innerHTML;
        }

        if (doc.title) {
            document.title = doc.title;
        }

        if (push) {
            window.history.pushState({ reportUrl: url }, '', url);
        }

        bindReportAjax();
    } catch (err) {
        if (err.name === 'AbortError') return;
        console.error('Report AJAX navigation failed, falling back:', err);
        window.location.href = url;
    } finally {
        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
    }
}

function highlightActiveTab(targetUrl) {
    try {
        const parsed = new URL(targetUrl, window.location.origin);
        const path = parsed.pathname;
        const tabParam = parsed.searchParams.get('tab');

        let activeKey = '';
        if (path.includes('/profitability')) {
            activeKey = 'profitability';
        } else if (path.includes('/inventory-valuation')) {
            activeKey = 'inventory_valuation';
        } else {
            activeKey = tabParam || 'profit_loss';
        }

        document.querySelectorAll('#reportTabsNav .report-nav-tab').forEach(tab => {
            const key = tab.getAttribute('data-tab');
            if (key === activeKey) {
                tab.className = 'report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-lg shadow-emerald-500/25 border border-emerald-400/30';
            } else {
                tab.className = 'report-nav-tab px-3.5 py-2.5 rounded-xl font-bold transition-all flex items-center gap-1.5 text-slate-400 hover:text-white hover:bg-slate-800/60 font-medium';
            }
        });
    } catch (e) {}
}

function bindReportAjax() {
    const content = document.getElementById('reportTabContent');
    if (!content) return;

    // 1. Intercept filter forms (Date filters, Product dropdown, Customer dropdown)
    content.querySelectorAll('form').forEach(form => {
        if (!form.hasAttribute('data-ajax-bound') && !form.getAttribute('action')?.includes('export') && !form.getAttribute('action')?.includes('pdf')) {
            form.setAttribute('data-ajax-bound', '1');
            form.addEventListener('submit', function(e) {
                handleReportFormSubmit(e, this);
            });
        }
    });

    // 2. Intercept internal sub-tab navigation (e.g. Product vs Delivery inside Profitability Reports)
    content.querySelectorAll('a').forEach(a => {
        const href = a.getAttribute('href');
        if (href && (href.includes('/accounts/reports') || href.includes('/accounts/profitability') || href.includes('/accounts/inventory-valuation')) && !a.hasAttribute('data-ajax-bound') && !href.includes('export') && !href.includes('pdf')) {
            a.setAttribute('data-ajax-bound', '1');
            a.addEventListener('click', function(e) {
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;
                e.preventDefault();
                loadReportTab(this.href);
            });
        }
    });
}

// Handle Browser Back / Forward buttons seamlessly
window.addEventListener('popstate', function(e) {
    if (e.state && e.state.reportUrl) {
        loadReportTab(e.state.reportUrl, false);
    } else {
        loadReportTab(window.location.href, false);
    }
});

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    bindReportAjax();
});
</script>

