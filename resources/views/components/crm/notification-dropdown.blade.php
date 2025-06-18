<!-- CRM Notifications Dropdown -->
<div class="relative" x-data="crmNotificationDropdown()">
    <button type="button" 
            class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative" 
            @click="toggleDropdown()"
            x-ref="dropdownButton">
        <span class="sr-only">View notifications</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        <!-- Notification badge -->
        <span x-show="notificationCount > 0" 
              x-text="notificationCount" 
              class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[1.25rem] w-5 h-5"></span>
    </button>
    
    <!-- Dropdown panel -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="closeDropdown()"
         class="notification-dropdown absolute right-0 z-50 mt-3 w-[30rem] max-w-[90vw] origin-top-right rounded-xl bg-white shadow-2xl ring-1 ring-gray-900/10 border border-gray-100"
         style="display: none;">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">CRM Notifications</h3>
                        <p x-show="notificationCount > 0" x-text="`${notificationCount} new notification${notificationCount > 1 ? 's' : ''}`" class="text-xs text-gray-600 mt-2"></p>
                        <p x-show="notificationCount === 0" class="text-xs text-gray-600 mt-2">All caught up!</p>
                    </div>
                </div>
                <div class="ml-2">
                    <button type="button" 
                            @click="markAllAsRead()"
                            x-show="notificationCount > 0"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Mark all read
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Notifications list -->
        <div class="notification-scroll bg-white" style="height: 28rem; overflow-y: auto;">
            <template x-if="notifications.length === 0">
                <div class="px-6 py-12 text-center">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-r from-green-100 to-blue-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">All caught up!</h3>
                    <p class="text-sm text-gray-500">No new CRM notifications at the moment.</p>
                </div>
            </template>
            
            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-50 last:border-0">
                    <div class="px-6 py-4 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-blue-50 cursor-pointer transition-all duration-200"
                         :class="{ 'bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-indigo-400': !notification.read_at }"
                         @click="markAsRead(notification.id, getNotificationUrl(notification.data))">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-sm"
                                     :class="getNotificationIconClass(notification.data.type)">
                                    <span x-html="getNotificationIcon(notification.data.type)"></span>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-900 truncate" x-text="notification.data.title || 'CRM Notification'"></p>
                                    <p class="text-xs text-gray-500 flex-shrink-0 ml-2" x-text="formatTimeAgo(notification.created_at)"></p>
                                </div>
                                <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="notification.data.message || 'No message'"></p>
                                <div x-show="!notification.read_at" class="flex items-center mt-2">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                    <span class="text-xs text-blue-600 font-medium">New</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            <div class="flex justify-between items-center">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">View all notifications</a>
                <span class="text-xs text-gray-500">Real-time updates</span>
            </div>
        </div>
    </div>
</div>

<script>
function crmNotificationDropdown() {
    return {
        isOpen: false,
        notifications: [],
        notificationCount: 0,
        latestTimestamp: '1970-01-01T00:00:00Z',
        isFirstLoad: true,
        
        // Audio notification properties
        audioContext: null,
        audioBuffer: null,
        audioQueue: [],
        isPlayingAudio: false,
        maxConcurrentSounds: 3,
        
        init() {
            this.initializeAudio();
            this.loadNotifications(false); // false = don't play sound on initial load
            this.startRealTimePolling();
            this.requestNotificationPermission();
        },
        
        async initializeAudio() {
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                
                // Load notification sound
                const response = await fetch('/audio/notification.mp3');
                const arrayBuffer = await response.arrayBuffer();
                this.audioBuffer = await this.audioContext.decodeAudioData(arrayBuffer);
            } catch (error) {
                console.warn('Audio initialization failed:', error);
                // Fallback to HTML5 audio
                this.initializeFallbackAudio();
            }
        },
        
        initializeFallbackAudio() {
            this.audioElement = new Audio('/audio/notification.mp3');
            this.audioElement.preload = 'auto';
            this.audioElement.volume = 0.6;
        },
        
        queueNotificationSound() {
            if (this.audioQueue.length < this.maxConcurrentSounds) {
                this.audioQueue.push(Date.now());
                this.playNextSound();
            }
        },
        
        async playNextSound() {
            if (this.isPlayingAudio || this.audioQueue.length === 0) {
                return;
            }
            
            this.isPlayingAudio = true;
            this.audioQueue.shift(); // Remove from queue
            
            try {
                if (this.audioContext && this.audioBuffer) {
                    // Use Web Audio API
                    const source = this.audioContext.createBufferSource();
                    source.buffer = this.audioBuffer;
                    source.connect(this.audioContext.destination);
                    source.start(0);
                    
                    // Wait for sound to finish
                    setTimeout(() => {
                        this.isPlayingAudio = false;
                        this.playNextSound(); // Play next in queue
                    }, 800);
                } else if (this.audioElement) {
                    // Fallback to HTML5 audio
                    this.audioElement.currentTime = 0;
                    await this.audioElement.play();
                    this.audioElement.onended = () => {
                        this.isPlayingAudio = false;
                        this.playNextSound(); // Play next in queue
                    };
                } else {
                    this.isPlayingAudio = false;
                }
            } catch (error) {
                console.warn('Could not play notification sound:', error);
                this.isPlayingAudio = false;
                this.playNextSound(); // Continue with queue
            }
        },
        
        requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        },
        
        startRealTimePolling() {
            setInterval(() => {
                if (!this.isOpen) {
                    this.checkForNewNotifications();
                }
            }, 30000); // Check every 30 seconds
        },
        
        async checkForNewNotifications() {
            try {
                const response = await fetch(`/crm/notifications/check-new?since=${encodeURIComponent(this.latestTimestamp)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    
                    if (data.has_new && data.notifications.length > 0) {
                        // Add new notifications to the beginning of the list
                        data.notifications.reverse().forEach(notification => {
                            this.notifications.unshift(notification);
                        });
                        
                        // Update count and latest timestamp
                        this.notificationCount = data.count;
                        this.latestTimestamp = data.latest_timestamp;
                        
                        // Queue notification sound for each new notification
                        data.notifications.forEach(() => {
                            this.queueNotificationSound();
                        });
                        
                        // Show browser notification for the first new notification
                        if (data.notifications.length > 0) {
                            this.showBrowserNotification(data.notifications[0]);
                        }
                        
                        // Animate the bell icon
                        this.animateBellIcon();
                        
                        // Keep only the latest 20 notifications in the UI
                        if (this.notifications.length > 20) {
                            this.notifications = this.notifications.slice(0, 20);
                        }
                    }
                }
            } catch (error) {
                console.error('Error checking for new CRM notifications:', error);
            }
        },
        
        showBrowserNotification(notification) {
            if (Notification.permission === 'granted') {
                const title = notification.data.title || 'New CRM Notification';
                const body = notification.data.message || '';
                
                const browserNotification = new Notification(title, {
                    body: body,
                    icon: '/img/favicon/favicon-32x32.png',
                    tag: 'maxmed-crm-notification'
                });
                
                // Auto-close after 5 seconds
                setTimeout(() => {
                    browserNotification.close();
                }, 5000);
            }
        },
        
        animateBellIcon() {
            const bellIcon = this.$refs.dropdownButton;
            if (bellIcon) {
                bellIcon.classList.add('animate-bounce');
                setTimeout(() => {
                    bellIcon.classList.remove('animate-bounce');
                }, 1000);
            }
        },
        
        toggleDropdown() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.loadNotifications(false); // false = don't play sound for manual loading
            }
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        async loadNotifications(playSound = true) {
            try {
                const response = await fetch('/crm/notifications', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const previousCount = this.notificationCount;
                    this.notifications = data.notifications || [];
                    this.notificationCount = data.unread_count || 0;
                    
                    // Set initial latest notification timestamp for real-time polling
                    if (this.notifications.length > 0 && this.latestTimestamp === '1970-01-01T00:00:00Z') {
                        this.latestTimestamp = this.notifications[0].created_at;
                    }
                    
                    // Only play sound if explicitly allowed AND we have NEW notifications AND not first load
                    if (playSound && !this.isFirstLoad && this.notificationCount > previousCount && previousCount >= 0) {
                        const newNotificationsCount = this.notificationCount - previousCount;
                        // Queue one sound for each new notification
                        for (let i = 0; i < newNotificationsCount; i++) {
                            this.queueNotificationSound();
                        }
                        this.animateBellIcon();
                    }
                    
                    // Mark first load as complete
                    if (this.isFirstLoad) {
                        this.isFirstLoad = false;
                    }
                }
            } catch (error) {
                console.error('Error loading CRM notifications:', error);
            }
        },
        
        async markAsRead(notificationId, url) {
            try {
                const response = await fetch(`/crm/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Mark notification as read in the UI
                    const notification = this.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read_at) {
                        notification.read_at = new Date().toISOString();
                        this.notificationCount = Math.max(0, this.notificationCount - 1);
                    }
                    
                    // Navigate to the URL if provided
                    if (url) {
                        window.location.href = url;
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const response = await fetch('/crm/notifications/mark-all-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Mark all notifications as read
                    this.notifications.forEach(notification => {
                        notification.read_at = new Date().toISOString();
                    });
                    this.notificationCount = 0;
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        },
        
        getNotificationUrl(data) {
            if (data.type === 'lead') {
                return '/crm/leads';
            } else if (data.type === 'contact_submission') {
                return '/crm/contact-submissions';
            } else if (data.type === 'quotation_request') {
                return '/crm/quotation-requests';
            } else if (data.type === 'campaign') {
                return '/crm/marketing/campaigns';
            } else if (data.type === 'marketing') {
                return '/crm/marketing';
            }
            return '/crm';
        },
        
        getNotificationIconClass(type) {
            const classes = {
                'lead': 'bg-gradient-to-r from-green-500 to-emerald-600',
                'contact_submission': 'bg-gradient-to-r from-blue-500 to-indigo-600',
                'quotation_request': 'bg-gradient-to-r from-purple-500 to-pink-600',
                'campaign': 'bg-gradient-to-r from-orange-500 to-red-600',
                'marketing': 'bg-gradient-to-r from-cyan-500 to-blue-600'
            };
            return classes[type] || 'bg-gradient-to-r from-gray-500 to-gray-600';
        },

        getNotificationIcon(type) {
            const icons = {
                'lead': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'contact_submission': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
                'quotation_request': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4z" clip-rule="evenodd"/></svg>',
                'campaign': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>',
                'marketing': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>'
            };
            return icons[type] || '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>';
        },
        
        formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'Just now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`;
            return date.toLocaleDateString();
        },
        
        destroy() {
            if (this.audioContext) {
                this.audioContext.close();
                this.audioContext = null;
            }
            if (this.audioElement) {
                this.audioElement.pause();
                this.audioElement = null;
            }
            // Clear audio queue
            this.audioQueue = [];
            this.isPlayingAudio = false;
        }
    }
}
</script>

<style>
/* Custom scrollbar styles for notification dropdown */
.notification-scroll {
    scrollbar-width: auto;
    scrollbar-color: #6b7280 #e5e7eb;
}

.notification-scroll::-webkit-scrollbar {
    width: 12px;
    background: #f9fafb;
}

.notification-scroll::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 6px;
    margin: 6px 0;
}

.notification-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #6b7280 0%, #4b5563 100%);
    border-radius: 6px;
    border: 2px solid #f9fafb;
    min-height: 40px;
}

.notification-scroll::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #4b5563 0%, #374151 100%);
}

/* Text truncation */
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Animation for new notifications */
@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -10px, 0);
    }
    70% {
        transform: translate3d(0, -5px, 0);
    }
    90% {
        transform: translate3d(0, -2px, 0);
    }
}

.animate-bounce {
    animation: bounce 1s ease-in-out;
}
</style> 