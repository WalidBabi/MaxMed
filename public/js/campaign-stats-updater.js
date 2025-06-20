// Campaign Statistics Auto-Updater
class CampaignStatsUpdater {
    constructor(campaignId, updateInterval = 30000) {
        this.campaignId = campaignId;
        this.updateInterval = updateInterval; // Default 30 seconds
        this.isPolling = false;
        this.pollTimer = null;
        this.lastUpdated = null;
        this.animationTimeout = null;
        
        // Statistics elements cache
        this.elements = {};
        this.cacheElements();
        
        // Initialize
        this.init();
    }
    
    init() {
        console.log(`🔄 Initializing campaign statistics updater for campaign ${this.campaignId}`);
        this.startRealTimePolling();
        
        // Add to global scope for debugging
        window.campaignStatsUpdater = this;
    }
    
    cacheElements() {
        // Cache all statistics elements for better performance
        this.elements = {
            // Main statistics cards
            totalRecipients: document.querySelector('[data-stat="total-recipients"]'),
            sentCount: document.querySelector('[data-stat="sent-count"]'),
            deliveredCount: document.querySelector('[data-stat="delivered-count"]'),
            openedCount: document.querySelector('[data-stat="opened-count"]'),
            clickedCount: document.querySelector('[data-stat="clicked-count"]'),
            bouncedCount: document.querySelector('[data-stat="bounced-count"]'),
            unsubscribedCount: document.querySelector('[data-stat="unsubscribed-count"]'),
            
            // Rate displays
            deliveryRate: document.querySelector('[data-stat="delivery-rate"]'),
            openRate: document.querySelector('[data-stat="open-rate"]'),
            clickRate: document.querySelector('[data-stat="click-rate"]'),
            bounceRate: document.querySelector('[data-stat="bounce-rate"]'),
            unsubscribeRate: document.querySelector('[data-stat="unsubscribe-rate"]'),
            
            // Sidebar statistics
            sidebarTotalRecipients: document.querySelector('[data-sidebar-stat="total-recipients"]'),
            sidebarSentCount: document.querySelector('[data-sidebar-stat="sent-count"]'),
            sidebarDeliveryRate: document.querySelector('[data-sidebar-stat="delivery-rate"]'),
            sidebarOpenRate: document.querySelector('[data-sidebar-stat="open-rate"]'),
            sidebarClickRate: document.querySelector('[data-sidebar-stat="click-rate"]'),
            sidebarBounceRate: document.querySelector('[data-sidebar-stat="bounce-rate"]'),
            sidebarUnsubscribeRate: document.querySelector('[data-sidebar-stat="unsubscribe-rate"]'),
            
            // Last updated indicator
            lastUpdated: document.querySelector('[data-last-updated]')
        };
        
        console.log('📊 Cached statistics elements:', Object.keys(this.elements).filter(key => this.elements[key]));
    }
    
    startRealTimePolling() {
        if (this.isPolling) {
            console.log('⚠️ Polling already started');
            return;
        }
        
        this.isPolling = true;
        console.log(`⏰ Starting real-time polling every ${this.updateInterval/1000} seconds`);
        
        // Update status indicator
        if (this.elements.lastUpdated) {
            this.elements.lastUpdated.textContent = 'Starting auto-update...';
            this.elements.lastUpdated.style.color = '#3b82f6'; // Blue color
        }
        
        // Initial check after 2 seconds
        setTimeout(() => {
            this.fetchStatistics();
        }, 2000);
        
        // Then check at regular intervals
        this.pollTimer = setInterval(() => {
            this.fetchStatistics();
        }, this.updateInterval);
    }
    
    stopRealTimePolling() {
        if (!this.isPolling) {
            console.log('⚠️ Polling not active');
            return;
        }
        
        this.isPolling = false;
        console.log('⏸️ Stopping real-time polling');
        
        if (this.pollTimer) {
            clearInterval(this.pollTimer);
            this.pollTimer = null;
        }
        
        // Update status indicator
        if (this.elements.lastUpdated) {
            this.elements.lastUpdated.textContent = 'Auto-update stopped';
            this.elements.lastUpdated.style.color = '#6b7280'; // Gray color
        }
    }
    
    async fetchStatistics() {
        try {
            console.log(`📡 Fetching campaign statistics for campaign ${this.campaignId}`);
            
            // Show loading indicator
            if (this.elements.lastUpdated) {
                this.elements.lastUpdated.textContent = 'Updating...';
                this.elements.lastUpdated.style.color = '#3b82f6'; // Blue color
            }
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('❌ CSRF token not found in meta tags');
                return;
            }
            
            const response = await fetch(`/crm/marketing/campaigns/${this.campaignId}/statistics`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            console.log(`📊 Response status: ${response.status} ${response.statusText}`);
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error(`❌ HTTP ${response.status}: ${response.statusText}`, errorText);
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            console.log('📈 Received data:', data);
            
            if (data.success && data.statistics) {
                this.updateStatistics(data.statistics);
                this.lastUpdated = data.updated_at;
                this.updateLastUpdatedDisplay();
                console.log('✅ Campaign statistics updated successfully');
            } else {
                console.warn('⚠️ Invalid response format:', data);
            }
            
        } catch (error) {
            console.error('❌ Error fetching campaign statistics:', error);
            // Show user-friendly error message
            if (this.elements.lastUpdated) {
                this.elements.lastUpdated.textContent = 'Error updating statistics';
                this.elements.lastUpdated.style.color = '#ef4444'; // Red color
            }
        }
    }
    
    updateStatistics(stats) {
        console.log('📊 Updating UI with new statistics:', stats);
        
        // Update main statistics cards with animation
        this.updateElement(this.elements.totalRecipients, this.formatNumber(stats.total_recipients));
        this.updateElement(this.elements.sentCount, this.formatNumber(stats.sent_count));
        this.updateElement(this.elements.deliveredCount, this.formatNumber(stats.delivered_count));
        this.updateElement(this.elements.openedCount, this.formatNumber(stats.opened_count));
        this.updateElement(this.elements.clickedCount, this.formatNumber(stats.clicked_count));
        this.updateElement(this.elements.bouncedCount, this.formatNumber(stats.bounced_count));
        this.updateElement(this.elements.unsubscribedCount, this.formatNumber(stats.unsubscribed_count));
        
        // Update rate displays
        this.updateElement(this.elements.deliveryRate, `${stats.delivery_rate}%`);
        this.updateElement(this.elements.openRate, `${stats.open_rate}%`);
        this.updateElement(this.elements.clickRate, `${stats.click_rate}%`);
        this.updateElement(this.elements.bounceRate, `${stats.bounce_rate}%`);
        this.updateElement(this.elements.unsubscribeRate, `${stats.unsubscribe_rate}%`);
        
        // Update sidebar statistics
        this.updateElement(this.elements.sidebarTotalRecipients, this.formatNumber(stats.total_recipients));
        this.updateElement(this.elements.sidebarSentCount, this.formatNumber(stats.sent_count));
        this.updateElement(this.elements.sidebarDeliveryRate, `${stats.delivery_rate}%`);
        this.updateElement(this.elements.sidebarOpenRate, `${stats.open_rate}%`);
        this.updateElement(this.elements.sidebarClickRate, `${stats.click_rate}%`);
        this.updateElement(this.elements.sidebarBounceRate, `${stats.bounce_rate}%`);
        this.updateElement(this.elements.sidebarUnsubscribeRate, `${stats.unsubscribe_rate}%`);
        
        // Animate cards to show they've been updated
        this.animateStatCards();
    }
    
    updateElement(element, newValue) {
        if (!element) return;
        
        const currentValue = element.textContent.trim();
        if (currentValue !== newValue) {
            // Add flash effect
            element.classList.add('bg-green-100', 'transition-colors', 'duration-300');
            element.textContent = newValue;
            
            setTimeout(() => {
                element.classList.remove('bg-green-100');
            }, 800);
        }
    }
    
    animateStatCards() {
        // Find all stat cards and add a subtle animation
        const statCards = document.querySelectorAll('.card-hover');
        
        statCards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.add('animate-pulse');
                setTimeout(() => {
                    card.classList.remove('animate-pulse');
                }, 600);
            }, index * 100);
        });
    }
    
    updateLastUpdatedDisplay() {
        if (this.elements.lastUpdated) {
            if (this.lastUpdated) {
                const now = new Date();
                const updatedTime = new Date(this.lastUpdated);
                const diffSeconds = Math.floor((now - updatedTime) / 1000);
                
                let displayText;
                if (diffSeconds < 10) {
                    displayText = 'Updated just now';
                } else if (diffSeconds < 60) {
                    displayText = `Updated ${diffSeconds} seconds ago`;
                } else {
                    const diffMinutes = Math.floor(diffSeconds / 60);
                    if (diffMinutes === 1) {
                        displayText = 'Updated 1 minute ago';
                    } else {
                        displayText = `Updated ${diffMinutes} minutes ago`;
                    }
                }
                
                this.elements.lastUpdated.textContent = displayText;
                this.elements.lastUpdated.style.color = '#10b981'; // Green color for success
            } else {
                this.elements.lastUpdated.textContent = 'Auto-updating every 30s';
                this.elements.lastUpdated.style.color = '#6b7280'; // Gray color
            }
        }
    }
    
    formatNumber(num) {
        if (num === null || num === undefined) return '0';
        return new Intl.NumberFormat().format(num);
    }
    
    // Manual trigger for immediate update
    triggerUpdate() {
        console.log('🔄 Manual update triggered');
        this.fetchStatistics();
    }
    
    // Change update interval
    setUpdateInterval(interval) {
        console.log(`⏰ Changing update interval to ${interval}ms`);
        this.updateInterval = interval;
        
        if (this.isPolling) {
            this.stopRealTimePolling();
            this.startRealTimePolling();
        }
    }
    
    // Restart polling after an error
    restartPolling() {
        console.log('🔄 Restarting polling after error...');
        this.stopRealTimePolling();
        setTimeout(() => {
            this.startRealTimePolling();
        }, 5000); // Wait 5 seconds before restarting
    }
}

// Auto-initialize if campaign ID is found
function initializeCampaignStatsUpdater() {
    const campaignIdElement = document.querySelector('[data-campaign-id]');
    if (campaignIdElement) {
        const campaignId = campaignIdElement.getAttribute('data-campaign-id');
        console.log(`🚀 Auto-initializing campaign stats updater for campaign ${campaignId}`);
        
        // Create global instance
        window.campaignStatsUpdater = new CampaignStatsUpdater(campaignId);
        
        // Add manual trigger button if it exists
        const manualTriggerBtn = document.querySelector('[data-trigger-stats-update]');
        if (manualTriggerBtn) {
            manualTriggerBtn.addEventListener('click', () => {
                window.campaignStatsUpdater.triggerUpdate();
            });
        }
    } else {
        console.log('⚠️ No campaign ID found, campaign stats updater not initialized');
    }
}

// Initialize immediately if DOM is already loaded, otherwise wait for DOMContentLoaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCampaignStatsUpdater);
} else {
    // DOM is already loaded, initialize immediately
    initializeCampaignStatsUpdater();
}

// Export for manual usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CampaignStatsUpdater;
} 