/**
 * Dashboard Realtime Features
 * Fitur: Country Search + FULL Realtime Updates (semua data setiap 5 detik)
 */

class DashboardRealtime {
    constructor() {
        this.searchInput = document.getElementById('countrySearch');
        this.searchDropdown = document.getElementById('searchDropdown');
        this.searchResults = document.getElementById('searchResults');
        this.countries = window.DASHBOARD_COUNTRIES || [];
        this.currentCountry = this.countries[0] || 'Germany';
        this.realtimeInterval = null;
        this.isUpdating = false;
        
        this.init();
    }

    init() {
        this.setupSearch();
        this.startRealtimePolling();
        this.setupKeyboardNavigation();
    }

    /**
     * Setup Search Functionality
     */
    setupSearch() {
        if (!this.searchInput) return;

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-box-container')) {
                this.searchDropdown.style.display = 'none';
            }
        });

        // Input event listener
        this.searchInput.addEventListener('input', (e) => {
            const value = e.target.value.trim().toLowerCase();
            
            if (!value) {
                this.searchDropdown.style.display = 'none';
                return;
            }

            const filtered = this.countries.filter(country => 
                country.toLowerCase().includes(value)
            );

            this.renderSearchResults(filtered);
            this.searchDropdown.style.display = filtered.length > 0 ? 'block' : 'none';
        });

        // Focus event
        this.searchInput.addEventListener('focus', () => {
            if (this.searchInput.value) {
                this.searchDropdown.style.display = 'block';
            }
        });
    }

    /**
     * Render Search Results
     */
    renderSearchResults(filtered) {
        this.searchResults.innerHTML = '';

        if (filtered.length === 0) {
            const notFound = document.createElement('div');
            notFound.className = 'search-result-item';
            notFound.style.cursor = 'default';
            notFound.style.color = 'var(--neutral-text)';
            notFound.textContent = 'Negara tidak ditemukan';
            this.searchResults.appendChild(notFound);
            return;
        }

        filtered.forEach(country => {
            const item = document.createElement('div');
            item.className = 'search-result-item';
            if (country === this.currentCountry) {
                item.classList.add('selected');
            }
            
            const icon = document.createElement('span');
            icon.className = 'search-result-icon';
            icon.innerHTML = '🌍';

            const text = document.createElement('span');
            text.textContent = country;

            item.appendChild(icon);
            item.appendChild(text);

            item.addEventListener('click', () => {
                this.selectCountry(country);
            });

            this.searchResults.appendChild(item);
        });
    }

    /**
     * Select Country from Search
     */
    selectCountry(country) {
        this.currentCountry = country;
        this.searchInput.value = country;
        this.searchDropdown.style.display = 'none';

        // Update tabs
        document.querySelectorAll('.ctab').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.country === country) {
                btn.classList.add('active');
            }
        });

        // Load country data dengan smooth transition
        this.transitionAndLoad(country);
    }

    /**
     * START Realtime Polling untuk SEMUA data
     */
    startRealtimePolling() {
        // Polling setiap 5 detik - UPDATE SEMUA DATA
        this.realtimeInterval = setInterval(() => {
            this.updateAllRealtimeData();
        }, 5000);

        // First update immediately
        this.updateAllRealtimeData();
    }

    /**
     * UPDATE SEMUA DATA REALTIME
     * Mengambil data terbaru untuk country yang aktif
     */
    async updateAllRealtimeData() {
        if (this.isUpdating) return;
        this.isUpdating = true;

        try {
            const apiBase = window.DASHBOARD_API_BASE || '/api';
            const response = await fetch(
                `${apiBase}/dashboard/country-data?country=${encodeURIComponent(this.currentCountry)}`
            );

            if (!response.ok) throw new Error('Failed to fetch');
            
            const data = await response.json();

            // Update semua elemen dengan smooth transition
            this.updateElementSmoothly('v-population', data.population ?? '-');
            this.updateElementSmoothly('v-gdp', data.gdp ?? '-');
            this.updateElementSmoothly('v-inflation', data.inflation ?? '-');
            
            // Currency dengan format khusus
            if (data.currency) {
                const dirSymbol = data.currency.direction === 'up' ? '▲' : '▼';
                const rate = data.currency.display || '-';
                const change = data.currency.rate_change_percent || '0';
                const currencyText = `${rate} ${dirSymbol} ${change}%`;
                this.updateElementSmoothly('v-currency', currencyText);
            }

            // Weather
            if (data.weather) {
                const tempText = (data.weather.temp ?? '-') + '°C';
                this.updateElementSmoothly('v-weather-temp', tempText);
                this.updateElementSmoothly('v-weather-cond', data.weather.condition ?? '-');
            }

            // Risk
            if (data.risk) {
                const riskText = `${data.risk.score ?? '-'} (${data.risk.level ?? '-'})`;
                this.updateElementSmoothly('v-risk', riskText);
            }

            // News (update jika ada news baru)
            if (data.news && data.news.length > 0) {
                this.updateNews(data.news);
            }

        } catch (err) {
            console.error('Realtime update error:', err);
        } finally {
            this.isUpdating = false;
        }
    }

    /**
     * Update element dengan smooth fade transition
     */
    updateElementSmoothly(elementId, newValue) {
        const el = document.getElementById(elementId);
        if (!el) return;

        const currentValue = el.textContent;
        if (currentValue === newValue) return; // Tidak perlu update

        // Smooth fade transition
        el.style.transition = 'opacity 0.3s ease';
        el.style.opacity = '0.6';

        setTimeout(() => {
            el.textContent = newValue;
            el.style.opacity = '1';
        }, 150);
    }

    /**
     * Update News Section
     */
    updateNews(news) {
        const grid = document.getElementById('newsGrid');
        if (!grid) return;

        const currentNews = grid.innerHTML;
        const newHtml = this.buildNewsHtml(news.slice(0, 3));

        if (currentNews !== newHtml) {
            grid.innerHTML = newHtml;
        }
    }

    /**
     * Build news HTML
     */
    buildNewsHtml(newsList) {
        return newsList.map((item, index) => {
            const sentimentClass = item.sentiment === 'positive' ? 'badge-positive'
                : item.sentiment === 'negative' ? 'badge-negative' : 'badge-neutral';
            
            return `
                <div class="news-item" style="animation-delay: ${index * 0.1}s">
                    <span>${item.title}</span>
                    <span class="news-badge ${sentimentClass}">${item.sentiment.charAt(0).toUpperCase()}</span>
                </div>
            `;
        }).join('');
    }

    /**
     * Smooth Country Data Transition
     */
    async transitionAndLoad(country) {
        const cards = document.querySelectorAll('.kpi-card, .panel-card');
        
        // Fade out
        cards.forEach(card => {
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
        });

        // Wait for fade
        await new Promise(resolve => setTimeout(resolve, 150));

        // Load new data via tab click
        const tabButton = document.querySelector(`[data-country="${country}"]`);
        if (tabButton) {
            tabButton.click();
        }

        // Fade in
        setTimeout(() => {
            cards.forEach(card => {
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
                card.classList.add('fade-in');
            });
        }, 100);
    }

    /**
     * Keyboard Navigation
     */
    setupKeyboardNavigation() {
        if (!this.searchInput) return;

        let selectedIndex = -1;

        this.searchInput.addEventListener('keydown', (e) => {
            if (!this.searchDropdown.style.display || this.searchDropdown.style.display === 'none') {
                return;
            }

            const items = this.searchResults.querySelectorAll('.search-result-item');

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                this.updateSelection(items, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                this.updateSelection(items, selectedIndex);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (selectedIndex >= 0 && items[selectedIndex]) {
                    items[selectedIndex].click();
                }
            } else if (e.key === 'Escape') {
                this.searchDropdown.style.display = 'none';
            }
        });
    }

    /**
     * Update Selection Visual
     */
    updateSelection(items, index) {
        items.forEach((item, i) => {
            if (i === index) {
                item.style.backgroundColor = 'var(--primary-light)';
                item.style.color = 'var(--primary-blue)';
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.style.backgroundColor = '';
                item.style.color = '';
            }
        });
    }

    /**
     * Cleanup
     */
    destroy() {
        if (this.realtimeInterval) {
            clearInterval(this.realtimeInterval);
        }
    }
}

// Initialize setelah DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardRealtime = new DashboardRealtime();
});
