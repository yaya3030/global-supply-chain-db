/**
 * Realtime Dashboard Helper
 * Bisa dipakai di semua dashboard untuk auto-polling data
 */

class DashboardRealtimeHelper {
    constructor(config = {}) {
        this.config = {
            endpoint: null,
            interval: 5000,
            updateElements: {},
            chartId: null,
            tableId: null,
            ...config
        };
        
        this.pollingId = null;
    }

    /**
     * Start realtime polling
     */
    start() {
        if (!this.config.endpoint) {
            console.error('❌ Endpoint harus di-set untuk realtime');
            return;
        }

        console.log(`🚀 Starting realtime polling untuk ${this.config.endpoint}`);

        // Update immediately
        this.poll();

        // Then setup interval
        this.pollingId = setInterval(() => {
            this.poll();
        }, this.config.interval);
    }

    /**
     * Poll data dari API
     */
    async poll() {
        try {
            const response = await fetch(this.config.endpoint);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Update elements
            this.updateElements(data);
            
            // Update chart jika ada
            if (this.config.chartId) {
                this.updateChart(data);
            }
            
            // Update table jika ada
            if (this.config.tableId) {
                this.updateTable(data);
            }

        } catch (err) {
            console.error('❌ Realtime polling error:', err);
        }
    }

    /**
     * Update DOM elements
     */
    updateElements(data) {
        Object.entries(this.config.updateElements).forEach(([selector, dataPath]) => {
            const element = document.querySelector(selector);
            if (!element) return;

            const value = this.getNestedValue(data, dataPath);
            
            element.style.opacity = '0.7';
            setTimeout(() => {
                element.textContent = value;
                element.style.opacity = '1';
            }, 100);
        });
    }

    /**
     * Update chart realtime
     */
    updateChart(data) {
        if (!window.riskComparisonChart && !window.weatherChart) return;

        const chart = window.riskComparisonChart || window.weatherChart;
        const chartData = data.data?.chart || data.chart || data.results;

        if (chart && Array.isArray(chartData)) {
            chart.data.datasets[0].data = chartData.map(d => d.total_risk_score || d.value);
            chart.update('active');
        }
    }

    /**
     * Update table realtime
     */
    updateTable(data) {
        const tbody = document.querySelector(`#${this.config.tableId} tbody`);
        if (!tbody) return;

        const tableData = data.data || data.results || [];
        
        tbody.style.opacity = '0.7';
        setTimeout(() => {
            tbody.innerHTML = this.buildTableRows(tableData);
            tbody.style.opacity = '1';
        }, 100);
    }

    /**
     * Build table rows
     */
    buildTableRows(data) {
        if (!Array.isArray(data)) return '';

        return data.map(row => {
            const cells = Object.values(row)
                .slice(0, 5) // Limit ke 5 kolom
                .map(val => {
                    if (typeof val === 'object') {
                        return `<td><span class="badge bg-info">${JSON.stringify(val).substring(0, 20)}</span></td>`;
                    }
                    return `<td>${val}</td>`;
                })
                .join('');
            
            return `<tr>${cells}</tr>`;
        }).join('');
    }

    /**
     * Get nested value dari object
     */
    getNestedValue(obj, path) {
        if (!path) return obj;
        return path.split('.').reduce((current, key) => {
            return current?.[key] ?? 'N/A';
        }, obj);
    }

    /**
     * Stop polling
     */
    stop() {
        if (this.pollingId) {
            clearInterval(this.pollingId);
            console.log('🛑 Realtime polling stopped');
        }
    }
}

// Make it globally available
window.DashboardRealtimeHelper = DashboardRealtimeHelper;
