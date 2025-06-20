// Simple, reliable notification system
class SimpleNotificationSystem {
    constructor() {
        this.notificationCount = 0;
        this.isPolling = false;
        this.audioEnabled = false;
        this.lastNotificationTime = new Date().toISOString();
        this.pollInterval = null;
        this.isFirstCheck = true; // Flag to track first check
        
        // Initialize the system
        this.init();
    }
    
    init() {
        console.log('🔔 Initializing Simple Notification System');
        
        // Try to enable audio on first user interaction
        document.addEventListener('click', () => this.enableAudio(), { once: true });
        
        // Start polling after page loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.startPolling());
        } else {
            this.startPolling();
        }
        
        // Make globally available
        window.notificationSystem = this;
    }
    
    enableAudio() {
        if (!this.audioEnabled) {
            this.audioEnabled = true;
            console.log('🔊 Audio enabled for notifications');
        }
    }
    
    startPolling() {
        if (this.isPolling) return;
        
        this.isPolling = true;
        console.log('🔄 Starting notification polling every 5 seconds');
        
        // Initial check
        setTimeout(() => this.checkNotifications(), 1000);
        
        // Regular polling
        this.pollInterval = setInterval(() => this.checkNotifications(), 5000);
    }
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
        this.isPolling = false;
        console.log('⏹️ Notification polling stopped');
    }
    
    async checkNotifications() {
        try {
            // Check multiple endpoints to cover all user types
            const endpoints = this.getNotificationEndpoints();
            
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint.url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.processNotificationData(data, endpoint.type);
                        break; // Stop after first successful endpoint
                    }
                } catch (error) {
                    console.log(`ℹ️ ${endpoint.type} endpoint not accessible (normal if not logged in)`);
                }
            }
        } catch (error) {
            console.warn('⚠️ Notification check failed:', error);
        }
    }
    
    getNotificationEndpoints() {
        return [
            { url: '/api/notification-status', type: 'public' },
            { url: '/admin/notifications', type: 'admin' },
            { url: '/crm/notifications', type: 'crm' },
            { url: '/supplier/notifications', type: 'supplier' }
        ];
    }
    
    processNotificationData(data, type) {
        let newCount = 0;
        let notifications = [];
        
        if (type === 'public') {
            // Handle public API response
            newCount = data.total_notifications || 0;
            const activityCount = (data.recent_submissions || 0) + (data.recent_quotations || 0);
            console.log(`📊 Public API: ${data.total_notifications} notifications, ${activityCount} recent activity`);
        } else {
            // Handle authenticated API response
            newCount = data.unread_count || data.count || 0;
            notifications = data.notifications || [];
            console.log(`📊 ${type} notifications: ${newCount} unread, ${notifications.length} recent`);
        }
        
        // Handle first check - just initialize count without showing notifications
        if (this.isFirstCheck) {
            this.notificationCount = newCount;
            this.updateNotificationBadges(newCount);
            this.isFirstCheck = false;
            console.log(`🔄 Initial notification count set to ${newCount} (no sounds/alerts on first load)`);
            return;
        }
        
        // Check for new notifications (only after first check)
        if (newCount > this.notificationCount) {
            const newNotificationsCount = newCount - this.notificationCount;
            console.log(`🆕 ${newNotificationsCount} new ${type} notifications!`);
            
            // Update count
            this.notificationCount = newCount;
            
            // Update UI
            this.updateNotificationBadges(newCount);
            
            // Play sound
            if (this.audioEnabled && newNotificationsCount > 0) {
                for (let i = 0; i < Math.min(newNotificationsCount, 3); i++) {
                    setTimeout(() => this.playNotificationSound(), i * 300);
                }
            }
            
            // Browser notifications disabled - using only sound and visual indicators
            
            // Animate bell icons
            this.animateBellIcons();
            
            // Log success
            console.log(`✅ Processed ${newNotificationsCount} new notifications from ${type} endpoint`);
        } else if (newCount !== this.notificationCount) {
            // Count changed (possibly decreased)
            this.notificationCount = newCount;
            this.updateNotificationBadges(newCount);
            console.log(`🔄 Updated notification count to ${newCount}`);
        }
    }
    
    updateNotificationBadges(count) {
        // Update all notification badges on the page
        const badges = document.querySelectorAll('[data-notification-badge], .notification-badge, [x-text*="notificationCount"]');
        
        badges.forEach(badge => {
            if (badge.hasAttribute('x-text')) {
                // Alpine.js badge - trigger Alpine update if available
                if (badge._x_dataStack && badge._x_dataStack[0]) {
                    badge._x_dataStack[0].notificationCount = count;
                }
            } else {
                // Regular badge
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline-flex' : 'none';
            }
        });
        
        console.log(`🔄 Updated ${badges.length} notification badges to show ${count}`);
    }
    
    animateBellIcons() {
        const bellIcons = document.querySelectorAll('[class*="bell"], [data-notification-bell]');
        
        bellIcons.forEach(icon => {
            icon.classList.add('animate-bounce');
            setTimeout(() => {
                icon.classList.remove('animate-bounce');
            }, 1000);
        });
    }
    
    playNotificationSound() {
        if (!this.audioEnabled) return;
        
        try {
            // Try MP3 first
            const audio = new Audio('/audio/notification.mp3');
            audio.volume = 0.6;
            
            const playPromise = audio.play();
            
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    console.log('🔊 Notification sound played');
                }).catch(error => {
                    console.log('🔊 MP3 failed, trying generated sound');
                    this.playGeneratedSound();
                });
            }
        } catch (error) {
            console.log('🔊 Audio failed, trying generated sound');
            this.playGeneratedSound();
        }
    }
    
    playGeneratedSound() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            
            if (audioContext.state === 'suspended') {
                audioContext.resume();
            }
            
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            // Create a pleasant chime sound
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.15);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.4);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.4);
            
            console.log('🎵 Generated notification sound played');
        } catch (error) {
            console.warn('🔇 Could not play any notification sound:', error);
        }
    }
    

    
    // Manual trigger functions
    manualCheck() {
        console.log('🔄 Manual notification check triggered');
        this.checkNotifications();
    }
    
    testSound() {
        console.log('🧪 Testing notification sound');
        this.enableAudio();
        this.playNotificationSound();
    }
    
    testNotification() {
        console.log('🧪 Testing notification display');
        this.notificationCount = Math.max(0, this.notificationCount - 1); // Temporarily decrease
        setTimeout(() => {
            this.processNotificationData({ unread_count: this.notificationCount + 1 }, 'test');
        }, 100);
    }
}

// Auto-initialize when script loads
window.addEventListener('DOMContentLoaded', () => {
    new SimpleNotificationSystem();
});

// Fallback for pages that load after DOM ready
if (document.readyState !== 'loading') {
    new SimpleNotificationSystem();
}

// Global trigger functions for form submissions
window.triggerNotificationCheck = function() {
    if (window.notificationSystem) {
        console.log('🔔 Global notification check triggered');
        window.notificationSystem.manualCheck();
    } else {
        console.log('⚠️ Notification system not initialized yet');
    }
};

window.playNotificationSound = function() {
    if (window.notificationSystem) {
        window.notificationSystem.testSound();
    }
};

window.testNotificationSystem = function() {
    if (window.notificationSystem) {
        window.notificationSystem.testNotification();
    }
}; 