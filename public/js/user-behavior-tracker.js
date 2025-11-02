/**
 * User Behavior Tracker
 * Tracks user interactions, mouse clicks, scroll depth, and other behavior patterns
 */
class UserBehaviorTracker {
    constructor(options = {}) {
        this.options = {
            endpoint: '/api/user-behavior/track',
            batchEndpoint: '/api/user-behavior/track-batch',
            batchSize: 10,
            batchTimeout: 5000, // 5 seconds
            trackClicks: true,
            trackScroll: true,
            trackMouseMove: true, // Enabled by default for more data
            trackFormInteractions: true,
            trackExitIntent: true,
            trackTimeOnPage: true,
            scrollThreshold: 25, // Track scroll every 25%
            mouseMoveThrottle: 100, // Throttle mouse move events
            ...options
        };

        this.events = [];
        this.batchTimer = null;
        this.pageStartTime = Date.now();
        this.lastScrollDepth = 0;
        this.isTracking = false;
        this.hasConsent = this.checkCookieConsent();

        this.init();
    }

    /**
     * Initialize the tracker
     */
    init() {
        if (!this.hasConsent) {
            return;
        }

        this.isTracking = true;
        this.trackPageView();
        this.setupEventListeners();
        this.startBatchTimer();
    }

    /**
     * Check if user has given cookie consent
     */
    checkCookieConsent() {
        const consent = this.getCookie('cookie_consent');
        return consent === 'accepted';
    }

    /**
     * Get cookie value
     */
    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        if (this.options.trackClicks) {
            this.setupClickTracking();
        }

        if (this.options.trackScroll) {
            this.setupScrollTracking();
        }

        if (this.options.trackMouseMove) {
            this.setupMouseMoveTracking();
        }

        if (this.options.trackFormInteractions) {
            this.setupFormTracking();
        }

        if (this.options.trackExitIntent) {
            this.setupExitIntentTracking();
        }

        if (this.options.trackTimeOnPage) {
            this.setupTimeOnPageTracking();
        }

        // Track page visibility changes
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.trackTimeOnPage();
            }
        });

        // Track before unload
        window.addEventListener('beforeunload', () => {
            this.trackTimeOnPage();
            this.flushBatch(true);
        });

        // Track copy, paste, cut
        document.addEventListener('copy', () => {
            if (this.isTracking) {
                this.trackEvent({
                    event_type: 'copy',
                    page_url: window.location.href,
                    event_data: { selection: window.getSelection().toString() }
                });
            }
        });
        document.addEventListener('paste', (e) => {
            if (this.isTracking) {
                this.trackEvent({
                    event_type: 'paste',
                    page_url: window.location.href,
                    event_data: { pasted: (e.clipboardData || window.clipboardData).getData('text') }
                });
            }
        });
        document.addEventListener('cut', () => {
            if (this.isTracking) {
                this.trackEvent({
                    event_type: 'cut',
                    page_url: window.location.href,
                    event_data: { selection: window.getSelection().toString() }
                });
            }
        });
        // Track print
        window.addEventListener('beforeprint', () => {
            if (this.isTracking) {
                this.trackEvent({ event_type: 'beforeprint', page_url: window.location.href });
            }
        });
        window.addEventListener('afterprint', () => {
            if (this.isTracking) {
                this.trackEvent({ event_type: 'afterprint', page_url: window.location.href });
            }
        });
        // Track window resize
        window.addEventListener('resize', () => {
            if (this.isTracking) {
                this.trackEvent({
                    event_type: 'resize',
                    page_url: window.location.href,
                    event_data: {
                        screen_width: window.screen.width,
                        screen_height: window.screen.height,
                        viewport_width: window.innerWidth,
                        viewport_height: window.innerHeight
                    }
                });
            }
        });
    }

    /**
     * Setup click tracking
     */
    setupClickTracking() {
        document.addEventListener('click', (event) => {
            if (!this.isTracking) return;

            const target = event.target;
            const clickData = {
                event_type: 'click',
                page_url: window.location.href,
                mouse_position: {
                    x: event.clientX,
                    y: event.clientY
                },
                click_target: {
                    tag: target.tagName.toLowerCase(),
                    id: target.id || null,
                    class: target.className || null,
                    text: this.getClickText(target),
                    selector: this.getElementSelector(target),
                    href: target.href || null,
                    type: target.type || null
                },
                event_data: {
                    button: event.button,
                    ctrlKey: event.ctrlKey,
                    shiftKey: event.shiftKey,
                    altKey: event.altKey,
                    metaKey: event.metaKey
                }
            };

            this.trackEvent(clickData);
        });
    }

    /**
     * Setup scroll tracking
     */
    setupScrollTracking() {
        let scrollTimeout;
        
        window.addEventListener('scroll', () => {
            if (!this.isTracking) return;

            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                const scrollDepth = this.calculateScrollDepth();
                
                // Only track if scroll depth has changed significantly
                if (Math.abs(scrollDepth - this.lastScrollDepth) >= this.options.scrollThreshold) {
                    this.lastScrollDepth = scrollDepth;
                    
                    this.trackEvent({
                        event_type: 'scroll',
                        page_url: window.location.href,
                        scroll_depth: scrollDepth,
                        event_data: {
                            scrollTop: window.pageYOffset,
                            scrollHeight: document.documentElement.scrollHeight,
                            clientHeight: document.documentElement.clientHeight
                        }
                    });
                }
            }, 150); // Debounce scroll events
        });
    }

    /**
     * Setup mouse move tracking (throttled)
     */
    setupMouseMoveTracking() {
        let lastMouseMove = 0;
        
        document.addEventListener('mousemove', (event) => {
            if (!this.isTracking) return;

            const now = Date.now();
            if (now - lastMouseMove < this.options.mouseMoveThrottle) return;
            
            lastMouseMove = now;

            this.trackEvent({
                event_type: 'mouse_move',
                page_url: window.location.href,
                mouse_position: {
                    x: event.clientX,
                    y: event.clientY
                },
                event_data: {
                    screenX: event.screenX,
                    screenY: event.screenY
                }
            });
        });
    }

    /**
     * Setup form interaction tracking
     */
    setupFormTracking() {
        // Track form focus events
        document.addEventListener('focusin', (event) => {
            if (!this.isTracking) return;
            
            const target = event.target;
            if (this.isFormElement(target)) {
                this.trackEvent({
                    event_type: 'form_interaction',
                    page_url: window.location.href,
                    event_data: {
                        action: 'focus',
                        element: target.tagName.toLowerCase(),
                        name: target.name || null,
                        type: target.type || null,
                        id: target.id || null
                    }
                });
            }
        });

        // Track form blur events
        document.addEventListener('focusout', (event) => {
            if (!this.isTracking) return;
            
            const target = event.target;
            if (this.isFormElement(target)) {
                this.trackEvent({
                    event_type: 'form_interaction',
                    page_url: window.location.href,
                    event_data: {
                        action: 'blur',
                        element: target.tagName.toLowerCase(),
                        name: target.name || null,
                        type: target.type || null,
                        id: target.id || null,
                        value_length: target.value ? target.value.length : 0
                    }
                });
            }
        });

        // Track form submissions
        document.addEventListener('submit', (event) => {
            if (!this.isTracking) return;
            
            const form = event.target;
            this.trackEvent({
                event_type: 'form_interaction',
                page_url: window.location.href,
                event_data: {
                    action: 'submit',
                    form_id: form.id || null,
                    form_action: form.action || null,
                    form_method: form.method || 'get',
                    field_count: form.elements.length
                }
            });
        });
    }

    /**
     * Setup exit intent tracking
     */
    setupExitIntentTracking() {
        let exitIntentTriggered = false;
        
        document.addEventListener('mouseleave', (event) => {
            if (!this.isTracking || exitIntentTriggered) return;
            
            // Only trigger if mouse leaves from the top of the page
            if (event.clientY <= 0) {
                exitIntentTriggered = true;
                
                this.trackEvent({
                    event_type: 'exit_intent',
                    page_url: window.location.href,
                    event_data: {
                        trigger: 'mouse_leave',
                        time_on_page: Date.now() - this.pageStartTime
                    }
                });
            }
        });
    }

    /**
     * Setup time on page tracking
     */
    setupTimeOnPageTracking() {
        // Track time on page every 30 seconds
        setInterval(() => {
            if (this.isTracking && !document.hidden) {
                this.trackTimeOnPage();
            }
        }, 30000);
    }

    /**
     * Track time on page
     */
    trackTimeOnPage() {
        const timeOnPage = Date.now() - this.pageStartTime;
        
        this.trackEvent({
            event_type: 'time_on_page',
            page_url: window.location.href,
            duration: Math.floor(timeOnPage / 1000), // Convert to seconds
            event_data: {
                page_title: document.title,
                referrer: document.referrer
            }
        });
    }

    /**
     * Track page view
     */
    trackPageView() {
        this.trackEvent({
            event_type: 'page_view',
            page_url: window.location.href,
            event_data: {
                page_title: document.title,
                referrer: document.referrer,
                screen_width: window.screen.width,
                screen_height: window.screen.height,
                viewport_width: window.innerWidth,
                viewport_height: window.innerHeight
            }
        });
    }

    /**
     * Track a single event
     */
    trackEvent(eventData) {
        if (!this.isTracking) return;
        // Add timestamp if not provided
        if (!eventData.timestamp) {
            eventData.timestamp = new Date().toISOString();
        }
        // Add required fields if missing
        if (!eventData.page_url) {
            eventData.page_url = window.location.href;
        }
        if (!eventData.event_type) {
            eventData.event_type = 'unknown';
        }
        // Add device/environment info
        eventData.device_info = {
            user_agent: navigator.userAgent,
            screen_width: window.screen.width,
            screen_height: window.screen.height,
            viewport_width: window.innerWidth,
            viewport_height: window.innerHeight,
            language: navigator.language,
            platform: navigator.platform,
            cookies_enabled: navigator.cookieEnabled
        };
        this.events.push(eventData);

        // Send immediately for important events
        if (['page_view', 'form_interaction', 'exit_intent'].includes(eventData.event_type)) {
            this.sendEvent(eventData);
        } else {
            // Add to batch for other events
            if (this.events.length >= this.options.batchSize) {
                this.flushBatch();
            }
        }
    }

    /**
     * Send a single event
     */
    async sendEvent(eventData) {
        try {
            const response = await fetch(this.options.endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify(eventData)
            });
            if (!response.ok) {
                console.warn('UserBehaviorTracker: Failed to send event', response.status);
            }
        } catch (error) {
            console.warn('UserBehaviorTracker: Error sending event', error);
        }
    }

    /**
     * Send batch of events
     */
    async flushBatch(isUnload = false) {
        if (this.events.length === 0) return;
        const eventsToSend = [...this.events];
        this.events = [];
        try {
            console.log('[UserBehaviorTracker] flushBatch payload:', eventsToSend);
            const payload = JSON.stringify({ events: eventsToSend });
            if (isUnload && navigator.sendBeacon) {
                const blob = new Blob([payload], { type: 'application/json' });
                navigator.sendBeacon(this.options.batchEndpoint, blob);
                return;
            }
            const response = await fetch(this.options.batchEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: payload
            });
            if (!response.ok) {
                console.warn('UserBehaviorTracker: Failed to send batch', response.status);
            }
        } catch (error) {
            console.warn('UserBehaviorTracker: Error sending batch', error);
        }
    }

    /**
     * Start batch timer
     */
    startBatchTimer() {
        this.batchTimer = setInterval(() => {
            this.flushBatch();
        }, this.options.batchTimeout);
    }

    /**
     * Calculate scroll depth percentage
     */
    calculateScrollDepth() {
        const scrollTop = window.pageYOffset;
        const scrollHeight = document.documentElement.scrollHeight;
        const clientHeight = document.documentElement.clientHeight;
        
        if (scrollHeight <= clientHeight) return 100;
        
        return Math.round((scrollTop / (scrollHeight - clientHeight)) * 100);
    }

    /**
     * Get click text from element
     */
    getClickText(element) {
        // Try to get meaningful text from the clicked element
        let text = '';
        
        if (element.textContent) {
            text = element.textContent.trim().substring(0, 100);
        } else if (element.alt) {
            text = element.alt;
        } else if (element.title) {
            text = element.title;
        } else if (element.placeholder) {
            text = element.placeholder;
        }

        return text;
    }

    /**
     * Get CSS selector for element
     */
    getElementSelector(element) {
        if (element.id) {
            return `#${element.id}`;
        }
        
        if (element.className) {
            const classes = element.className.split(' ').filter(c => c.trim());
            if (classes.length > 0) {
                return `${element.tagName.toLowerCase()}.${classes.join('.')}`;
            }
        }
        
        return element.tagName.toLowerCase();
    }

    /**
     * Check if element is a form element
     */
    isFormElement(element) {
        const formElements = ['input', 'textarea', 'select', 'button'];
        return formElements.includes(element.tagName.toLowerCase());
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    /**
     * Enable tracking
     */
    enable() {
        this.isTracking = true;
        this.hasConsent = true;
    }

    /**
     * Disable tracking
     */
    disable() {
        this.isTracking = false;
        this.flushBatch();
    }

    /**
     * Destroy tracker
     */
    destroy() {
        this.disable();
        if (this.batchTimer) {
            clearInterval(this.batchTimer);
        }
    }
}

// Auto-initialize if script is loaded
if (typeof window !== 'undefined') {
    window.UserBehaviorTracker = UserBehaviorTracker;
    
    // Check if tracking is disabled
    if (window.userBehaviorTrackingDisabled) {
        // Tracking disabled for this page
        console.log('User behavior tracking disabled for this page');
    } else {
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                window.userBehaviorTracker = new UserBehaviorTracker();
            });
        } else {
            window.userBehaviorTracker = new UserBehaviorTracker();
        }
    }
} 

// Expose a global function to start tracking after consent
window.startUserBehaviorTracking = function() {
    if (!window._userBehaviorTracker) {
        window._userBehaviorTracker = new UserBehaviorTracker();
    }
}; 