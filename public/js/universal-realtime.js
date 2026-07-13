/**
 * Universal Realtime Engine untuk Semua Dashboard
 * Fitur: Auto-polling data, smooth updates, shared realtime logic
 */

class UniversalRealtime {
    constructor(config = {}) {
        this.config = {
            pollingInterval: 5000, // 5 seconds default
            fadeInDuration: 300,
            fadeOutDuration: 150,
            ...config
        };
        
        this.pollingIntervals = [];
        this.isUpdating = false;
        
        this.init();
    }

    init() {
        console.log('🚀 Universal Realtime Engine initialized');
        this.startPolling();
    }

    /**
     * Start realtime polling untuk setiap element dengan data-realtime attribute
     */
    startPolling() {
        // Find all elements yang punya data-realtime attribute
        const realtimeElements = document.querySelectorAll('[data-realtime]');
        
        if (realtimeElements.length === 0) {
            console.log('⚠️  No realtime elements found');
            return;
        }

        console.log(`📊 Found ${realtimeElements.length} realtime elements`);

        // Start polling untuk setiap element
        realtimeElements.forEach(element => {
            const endpoint = element.getAttribute('data-realtime');
            const interval = element.getAttribute('data-realtime-interval') || this.config.pollingInterval;
            const updateType = element.getAttribute('data-realtime-type') || 'text';
            
            if (!endpoint) return;

            // Update immediately
            this.updateElement(element, endpoint, updateType);

            // Then setup interval
            const intervalId = setInterval(() => {
                this.updateElement(element, endpoint, updateType);
            }, parseInt(interval));

            this.pollingIntervals.push(intervalId);
        });
    }

    /**
     * Update single element
     */
    async updateElement(element, endpoint, updateType) {
        if (this.isUpdating) return;
        this.isUpdating = true;

        try {
            const apiBase = window.DASHBOARD_API_BASE || '/api';
            const url = endpoint.startsWith('http') ? endpoint : `${apiBase}${endpoint}`;
            
            const response = await fetch(url);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const data = await response.json();
            
            // Extract data value based on updateType
            let displayValue = this.extractValue(data, updateType, element);
            
            if (!displayValue) return;

            // Update with smooth transition
            this.updateElementSmoothly(element, displayValue, updateType);

        } catch (err) {
            console.error(`❌ Realtime update error:`, err);
        } finally {
            this.isUpdating = false;
        }
    }

    /**
     * Extract value from API response
     */
    extractValue(data, updateType, element) {
        const selector = element.getAttribute('data-realtime-selector');
        
        // If custom selector provided, use it
        if (selector) {
            return this.getNestedProperty(data, selector);
        }

        // Default extractors based on updateType
        switch(updateType) {
            case 'json':
                return JSON.stringify(data.data || data, null, 2);
            case 'count':
                return data.data?.length || data.length || 0;
            case 'percentage':
                return `${data.data?.percentage || data.percentage || 0}%`;
            case 'currency':
                return `${data.data?.symbol || '$'} ${data.data?.value || data.value || 0}`;
            case 'status':
                return data.data?.status || data.status || 'unknown';
            default:
                return data.data?.value || data.data || data;
        }
    }

    /**
     * Get nested property dari object
     */
    getNestedProperty(obj, path) {
        return path.split('.').reduce((current, prop) => {
            return current?.[prop];
        }, obj);
    }

    /**
     * Update element dengan smooth transition
     */
    updateElementSmoothly(element, newValue, updateType) {
        const currentValue = element.textContent || element.value;
        
        // If value same, skip update
        if (currentValue === newValue) return;

        // Apply smooth transition
        element.style.transition = `opacity ${this.config.fadeOutDuration}ms ease`;
        element.style.opacity = '0.6';

        setTimeout(() => {
            if (updateType === 'json' || updateType === 'html') {
                element.innerHTML = newValue;
            } else {
                element.textContent = newValue;
            }
            element.style.opacity = '1';
        }, this.config.fadeOutDuration);
    }

    /**
     * Setup realtime chart updates
     */
    setupChartRealtime(chartId, endpoint, updateInterval = 5000) {
        const chart = document.getElementById(chartId);
        if (!chart || !window.Chart) return;

        // Get chart instance (from Chart.js)
        const chartInstance = Chart.helpers.each(Chart.instances, function(instance) {
            if (instance.chart.canvas.id === chartId) return instance;
        });

        if (!chartInstance) return;

        setInterval(async () => {
            try {
                const apiBase = window.DASHBOARD_API_BASE || '/api';
                const url = endpoint.startsWith('http') ? endpoint : `${apiBase}${endpoint}`;
                
                const response = await fetch(url);
                if (!response.ok) return;
                
                const data = await response.json();
                const chartData = data.data?.chart || data.chart;

                if (chartData) {
                    // Update chart with smooth animation
                    chartInstance.data.labels = chartData.labels;
                    chartInstance.data.datasets[0].data = chartData.values;
                    chartInstance.update('active');
                }
            } catch (err) {
                console.error('Chart update error:', err);
            }
        }, updateInterval);
    }

    /**
     * Setup table realtime updates
     */
    setupTableRealtime(tableId, endpoint, updateInterval = 5000) {
        const table = document.getElementById(tableId);
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        setInterval(async () => {
            try {
                const apiBase = window.DASHBOARD_API_BASE || '/api';
                const url = endpoint.startsWith('http') ? endpoint : `${apiBase}${endpoint}`;
                
                const response = await fetch(url);
                if (!response.ok) return;
                
                const data = await response.json();
                const rows = data.data || data;

                if (Array.isArray(rows)) {
                    // Update table rows dengan smooth transition
                    tbody.style.opacity = '0.7';
                    
                    setTimeout(() => {
                        tbody.innerHTML = this.buildTableRows(rows);
                        tbody.style.opacity = '1';
                    }, 150);
                }
            } catch (err) {
                console.error('Table update error:', err);
            }
        }, updateInterval);
    }

    /**
     * Build table rows HTML
     */
    buildTableRows(rows) {
        return rows.map(row => {
            if (typeof row === 'object') {
                const cells = Object.values(row).map(val => `<td>${val}</td>`).join('');
                return `<tr>${cells}</tr>`;
            }
            return `<tr><td>${row}</td></tr>`;
        }).join('');
    }

    /**
     * Destroy all intervals
     */
    destroy() {
        this.pollingIntervals.forEach(id => clearInterval(id));
        this.pollingIntervals = [];
        console.log('🛑 Universal Realtime Engine destroyed');
    }
}

// Initialize setelah DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if there are realtime elements
    const hasRealtimeElements = document.querySelector('[data-realtime]');
    if (hasRealtimeElements) {
        window.universalRealtime = new UniversalRealtime();
    }
});

// Make it globally available
window.UniversalRealtime = UniversalRealtime;
