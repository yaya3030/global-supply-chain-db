/**
 * Dashboard Search & Realtime Features
 * Fitur: Country Search, Realtime Currency Updates, Smooth Transitions
 */

class DashboardEnhanced {
    constructor() {
        this.searchInput = document.getElementById('countrySearch');
        this.searchDropdown = document.getElementById('searchDropdown');
        this.searchResults = document.getElementById('searchResults');
        this.countries = window.DASHBOARD_COUNTRIES || [];
        this.currentCountry = this.countries[0] || 'Germany';
        this.currencyUpdateInterval = null;
        
        this.init();
    }

    init() {
        this.setupSearch();
        this.setupRealtime();
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
        this.transitionCountryData(country);
    }

    /**
     * Setup Realtime Currency Updates (Polling)
     */
    setupRealtime() {
        // Update currency setiap 5 detik untuk realtime effect
        this.currencyUpdateInterval = setInterval(() => {
            this.updateCurrencyRealtime();
        }, 5000);
    }

    /**
     * Update Currency Realtime
     */
    async updateCurrencyRealtime() {
        const currencyElement = document.getElementById('v-currency');
        if (!currencyElement) return;

        try {
            // Get country metadata
            const countryCode = this.getCountryCode(this.currentCountry);
            const currencyCode = this.getCurrencyCode(this.currentCountry);
            
            if (!currencyCode) return;

            const apiBase = window.DASHBOARD_API_BASE || '/api';
            const res = await fetch(
                `${apiBase}/currency/realtime?country=${countryCode}`
            );
            
            if (!res.ok) return;
            
            const data = await res.json();
            
            if (!data.success || !data.data) return;

            const rate = data.data.rate;
            const change = data.data.rate_change_percent || '0';
            const direction = data.data.direction || 'up';
            
            // Format display
            const displayRate = rate >= 1000
                ? (rate >= 1000000 ? (rate / 1000000).toFixed(2) + 'M' : (rate / 1000).toFixed(1) + 'K')
                : rate.toFixed(2);
            
            const dirSymbol = direction === 'up' ? '▲' : '▼';
            const newText = `${displayRate} ${currencyCode}/USD ${dirSymbol} ${change}%`;
            
            // Smooth transition
            if (currencyElement.textContent !== newText) {
                currencyElement.style.transition = 'opacity 0.3s ease';
                currencyElement.style.opacity = '0.7';
                
                setTimeout(() => {
                    currencyElement.textContent = newText;
                    currencyElement.style.opacity = '1';
                }, 150);
            }
            
        } catch (err) {
            // Silently fail, don't spam console
        }
    }
    
    /**
     * Get Country Code
     */
    getCountryCode(country) {
        const codes = {
            'Germany': 'DE',
            'China': 'CN',
            'Indonesia': 'ID',
            'Australia': 'AU'
        };
        return codes[country] || country.substring(0, 2).toUpperCase();
    }
    
    /**
     * Get Currency Code
     */
    getCurrencyCode(country) {
        const currencies = {
            'Germany': 'EUR',
            'China': 'CNY',
            'Indonesia': 'IDR',
            'Australia': 'AUD'
        };
        return currencies[country] || null;
    }

    /**
     * Smooth Country Data Transition
     */
    async transitionCountryData(country) {
        const cards = document.querySelectorAll('.kpi-card, .panel-card');
        
        // Fade out
        cards.forEach(card => {
            card.style.opacity = '0.5';
            card.style.pointerEvents = 'none';
        });

        // Wait for fade
        await new Promise(resolve => setTimeout(resolve, 150));

        // Load new data
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
     * Stop Realtime Updates (cleanup)
     */
    destroy() {
        if (this.currencyUpdateInterval) {
            clearInterval(this.currencyUpdateInterval);
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const dashboard = new DashboardEnhanced();
    
    // Store instance for cleanup if needed
    window.dashboardEnhanced = dashboard;
});
