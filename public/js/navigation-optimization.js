/**
 * Navigation Optimization Script
 * Prevents performance issues during page navigation
 */

// Global navigation state
window.navigating = false;

// Detect navigation start
document.addEventListener('DOMContentLoaded', function() {
    // Intercept all link clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.href.startsWith('#') && !link.target) {
            window.navigating = true;
            
            // Reset navigation state after a delay
            setTimeout(() => {
                window.navigating = false;
            }, 2000);
        }
    });
    
    // Detect form submissions
    document.addEventListener('submit', function(e) {
        window.navigating = true;
        setTimeout(() => {
            window.navigating = false;
        }, 2000);
    });
    
    // Detect browser back/forward
    window.addEventListener('popstate', function(e) {
        window.navigating = true;
        setTimeout(() => {
            window.navigating = false;
        }, 1000);
    });
    
    // Pause all polling during page unload
    window.addEventListener('beforeunload', function(e) {
        window.navigating = true;
        
        // Clear all intervals
        for (let i = 1; i < 99999; i++) {
            window.clearInterval(i);
        }
    });
});

// Optimize Alpine.js initialization
document.addEventListener('alpine:init', () => {
    // Add global navigation state to Alpine
    Alpine.store('navigation', {
        isNavigating: false,
        
        startNavigation() {
            this.isNavigating = true;
            window.navigating = true;
        },
        
        endNavigation() {
            this.isNavigating = false;
            window.navigating = false;
        }
    });
});
