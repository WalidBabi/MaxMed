<!-- Notifications Dropdown -->
<div class="relative" x-data="notificationDropdown()">
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
              class="absolute top-0 right-0 inline-flex items-center justify-center text-xs font-bold leading-none text-white transform translate-x-1/3 -translate-y-1/3 bg-red-600 rounded-full min-w-[1.25rem] w-5 h-5"></span>
    </button>
    
    <!-- Dropdown panel - Increased width for better content display -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="transform opacity-0 scale-95" 
         x-transition:enter-end="transform opacity-100 scale-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="transform opacity-100 scale-100" 
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="closeDropdown()"
         class="notification-dropdown absolute right-0 z-50 mt-3 w-[75rem] max-w-[90vw] origin-top-right rounded-xl bg-white shadow-2xl ring-1 ring-gray-900/10 border border-gray-100"
         style="width: 30rem !important; max-width: 55vw !important; min-width: 300px !important;"
         style="display: none;">
        
        <!-- Header - Improved spacing and typography -->
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
                        <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
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
        
        <!-- Notifications list - Fixed height with functional scrollbar -->
        <div class="notification-scroll bg-white" style="height: 28rem; overflow-y: auto; overflow-x: hidden;">
            <template x-if="notifications.length === 0">
                <div class="px-6 py-12 text-center">
                    <div class="w-20 h-20 mx-auto bg-gradient-to-r from-green-100 to-blue-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">All caught up!</h3>
                    <p class="text-sm text-gray-500">No new notifications at the moment.</p>
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
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path x-show="notification.data.type === 'feedback'" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" />
                                        <path x-show="notification.data.type === 'system_feedback'" fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        <path x-show="notification.data.type === 'order'" fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zM8 6a2 2 0 114 0v1H8V6zm0 3a1 1 0 012 0v1a1 1 0 11-2 0V9zm4 0a1 1 0 012 0v1a1 1 0 11-2 0V9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 pr-6 max-w-[92%]">
                                        <p class="text-base font-semibold text-gray-900 mb-1 leading-snug px-4" x-text="notification.data.title || notification.data.message"></p>
                                        <p class="text-xs text-gray-600 leading-relaxed px-4" x-text="notification.data.message" style="max-height: none; overflow: visible; word-wrap: break-word; white-space: normal;"></p>
                                    </div>
                                    <div class="flex items-center space-x-2 flex-shrink-0 min-w-[15%] justify-end">
                                        <span class="text-xs text-gray-400 font-medium whitespace-nowrap" x-text="formatTimeAgo(notification.created_at)"></span>
                                        <div x-show="!notification.read_at" class="w-2 h-2 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium"
                                          :class="getNotificationBadgeClass(notification.data.type)"
                                          x-text="getNotificationTypeLabel(notification.data.type)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Footer -->
        <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
            <div class="flex justify-center items-center">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">View all notifications</a>
            </div>
            <div class="mt-2 text-center">
                <span class="text-xs text-gray-500">Real-time updates every 3s • Click bell to enable audio</span>
            </div>
        </div>

    </div>
</div>

<script>
function notificationDropdown() {
    return {
        isOpen: false,
        notifications: [],
        notificationCount: 0,
        eventSource: null,
        audioContext: null,
        latestTimestamp: '1970-01-01T00:00:00Z',
        isFirstLoad: true,
        
        // Audio queue system to handle multiple notifications
        audioQueue: [],
        isPlayingAudio: false,
        audioElement: null,
        useGeneratedSound: false,
        
        // Track notifications that have already played sound to prevent infinite replay
        soundPlayedForNotifications: new Set(),
        
        init() {
            this.loadNotifications(false); // false = don't play sound on initial load
            this.initializeAudio();
            this.startRealTimePolling(); // Enable real-time notifications for admin
            this.requestNotificationPermission();
            
            // Add this component to global scope for manual triggering
            window.adminNotificationComponent = this;
        },
        
        initializeAudio() {
            // Initialize audio element for MP3 notification sounds
            try {
                this.audioElement = new Audio();
                this.audioElement.volume = 0.7; // Set volume to 70%
                this.audioElement.preload = 'auto'; // Preload the audio
                this.useGeneratedSound = false; // Start with MP3 first
                
                // Try multiple paths for the notification sound
                const audioPath = '/audio/notification.mp3';
                this.audioElement.src = audioPath;
                
                console.log('🔊 Attempting to load MP3 from:', audioPath);
                
                // Add event listeners for debugging and completion tracking
                this.audioElement.addEventListener('canplaythrough', () => {
                    console.log('✅ MP3 notification sound loaded successfully');
                });
                
                this.audioElement.addEventListener('error', (e) => {
                    console.warn('❌ MP3 notification sound failed to load:', e);
                    console.warn('Falling back to generated sound');
                    this.useGeneratedSound = true;
                    this.initializeAudioContext();
                });
                
                this.audioElement.addEventListener('loadstart', () => {
                    console.log('🔄 Starting to load MP3...');
                });
                
                // Listen for when audio ends to process queue
                this.audioElement.addEventListener('ended', () => {
                    console.log('🔊 Audio finished playing');
                    this.isPlayingAudio = false;
                    this.processAudioQueue();
                });
                
                this.audioElement.addEventListener('pause', () => {
                    console.log('🔊 Audio paused');
                    this.isPlayingAudio = false;
                    this.processAudioQueue();
                });
                
                // Test load the audio
                this.audioElement.load();
                
            } catch (e) {
                console.warn('Audio API not supported, falling back to generated sound:', e);
                this.useGeneratedSound = true;
                this.initializeAudioContext();
            }
        },
        
        initializeAudioContext() {
            // Initialize Web Audio API for generated notification sounds (fallback)
            try {
                this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                console.warn('Web Audio API not supported');
            }
        },
        
        // Enhanced audio queue system
        queueNotificationSound() {
            console.log('🔔 Queueing notification sound...');
            this.audioQueue.push(Date.now());
            this.processAudioQueue();
        },
        
        processAudioQueue() {
            // Only process if not currently playing and queue has items
            if (!this.isPlayingAudio && this.audioQueue.length > 0) {
                console.log(`🎵 Processing audio queue (${this.audioQueue.length} items)`);
                this.audioQueue.shift(); // Remove the first item
                this.playNotificationSound();
            }
        },
        
        playNotificationSound() {
            if (this.isPlayingAudio) {
                console.log('🔊 Audio already playing, skipping...');
                return;
            }
            
            console.log('🔊 Playing notification sound...');
            this.isPlayingAudio = true;
            
            try {
                if (this.audioElement && !this.useGeneratedSound) {
                    console.log('🎵 Attempting to play MP3 audio');
                    // Stop and reset audio before playing
                    this.audioElement.pause();
                    this.audioElement.currentTime = 0;
                    
                    const playPromise = this.audioElement.play();
                    
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            console.log('✅ MP3 audio started successfully');
                        }).catch(e => {
                            console.warn('❌ Could not play MP3 notification:', e);
                            console.warn('Switching to generated sound');
                            this.useGeneratedSound = true;
                            this.isPlayingAudio = false; // Reset flag
                            this.playGeneratedSound();
                        });
                    }
                } else {
                    console.log('🎶 Using generated sound (MP3 not available)');
                    // Fallback to generated sound
                    this.playGeneratedSound();
                }
            } catch (e) {
                console.warn('❌ Error playing notification sound:', e);
                this.isPlayingAudio = false; // Reset flag
                this.playGeneratedSound();
            }
        },

        // Add a test function for debugging
        testAudio() {
            console.log('🧪 Testing audio system...');
            console.log('Audio element exists:', !!this.audioElement);
            console.log('Use generated sound:', this.useGeneratedSound);
            console.log('Audio src:', this.audioElement?.src);
            console.log('Audio ready state:', this.audioElement?.readyState);
            console.log('Is playing audio:', this.isPlayingAudio);
            console.log('Queue length:', this.audioQueue.length);
            this.queueNotificationSound();
        },
        
        playGeneratedSound() {
            if (!this.audioContext) {
                this.isPlayingAudio = false;
                this.processAudioQueue();
                return;
            }
            
            try {
                // Resume audio context if suspended (required by browser policies)
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
                
                // Create a pleasant notification sound
                const oscillator = this.audioContext.createOscillator();
                const gainNode = this.audioContext.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(this.audioContext.destination);
                
                // Configure a two-tone chime sound
                oscillator.frequency.setValueAtTime(800, this.audioContext.currentTime);
                oscillator.frequency.setValueAtTime(600, this.audioContext.currentTime + 0.15);
                
                gainNode.gain.setValueAtTime(0.4, this.audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.4);
                
                oscillator.start(this.audioContext.currentTime);
                oscillator.stop(this.audioContext.currentTime + 0.4);
                
                // Schedule the next sound in queue after this one finishes
                setTimeout(() => {
                    console.log('🎶 Generated sound finished playing');
                    this.isPlayingAudio = false;
                    this.processAudioQueue();
                }, 450); // Slightly longer than the sound duration
                
            } catch (e) {
                console.warn('Could not play generated notification sound:', e);
                this.isPlayingAudio = false;
                this.processAudioQueue();
            }
        },
        
        requestNotificationPermission() {
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }
        },
        
        startRealTimePolling() {
            console.log('Starting real-time polling every 3 seconds for admin notifications');
            
            // Initial check after 1 second
            setTimeout(() => {
                this.checkForNewNotifications();
            }, 1000);
            
            // Then check every 3 seconds
            setInterval(() => {
                console.log('Polling for admin notifications...');
                this.checkForNewNotifications();
            }, 3000);
        },
        
        async checkForNewNotifications() {
            try {
                const response = await fetch(`/admin/notifications/check-new?last_timestamp=${encodeURIComponent(this.latestTimestamp)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('Admin notification check response:', data);
                    
                    if (data.has_new && data.notifications.length > 0) {
                        console.log('New admin notifications found:', data.notifications.length);
                        
                        // Filter out notifications that have already played sound
                        const reallyNewNotifications = data.notifications.filter(notification => 
                            !this.soundPlayedForNotifications.has(notification.id)
                        );
                        
                        console.log('Really new admin notifications:', reallyNewNotifications.length);
                        
                        if (reallyNewNotifications.length > 0) {
                            // Add new notifications to the beginning of the list
                            data.notifications.reverse().forEach(notification => {
                                this.notifications.unshift(notification);
                            });
                            
                            // Update count and latest timestamp
                            this.notificationCount = data.count || this.notificationCount + reallyNewNotifications.length;
                            this.latestTimestamp = data.latest_timestamp;
                            
                            console.log('Updated admin notification count:', this.notificationCount);
                            
                            // Play sound for each new notification
                            reallyNewNotifications.forEach(notification => {
                                this.soundPlayedForNotifications.add(notification.id);
                                console.log('Playing sound for admin notification:', notification.id);
                                this.queueNotificationSound();
                            });
                            
                            // Show browser notification for the first new notification
                            if (reallyNewNotifications.length > 0) {
                                this.showBrowserNotification(reallyNewNotifications[0]);
                            }
                            
                            // Animate the bell icon
                            this.animateBellIcon();
                            
                            // Keep only the latest 20 notifications in the UI
                            if (this.notifications.length > 20) {
                                this.notifications = this.notifications.slice(0, 20);
                            }
                        }
                    }
                    
                    // Always update the count even if no new notifications
                    if (data.count !== undefined) {
                        const newCount = data.count || 0;
                        if (newCount !== this.notificationCount) {
                            console.log('Updating admin count from', this.notificationCount, 'to', newCount);
                            this.notificationCount = newCount;
                        }
                    }
                }
            } catch (error) {
                console.error('Error checking for new admin notifications:', error);
            }
        },
        
        showBrowserNotification(notification) {
            if (Notification.permission === 'granted') {
                const title = notification.data.title || 'New Notification';
                const body = notification.data.message || '';
                
                const browserNotification = new Notification(title, {
                    body: body,
                    icon: '/img/favicon/favicon-32x32.png',
                    tag: 'maxmed-notification'
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
                this.loadNotifications(true); // true = allow sound for new notifications
            }
        },
        
        closeDropdown() {
            this.isOpen = false;
        },
        
        async loadNotifications(playSound = true) {
            try {
                const response = await fetch('/admin/notifications', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const previousCount = this.notificationCount;
                    const previousNotifications = this.notifications;
                    this.notifications = data.notifications || [];
                    this.notificationCount = data.unread_count || 0;
                    
                    // Set initial latest notification timestamp
                    if (this.notifications.length > 0 && this.latestTimestamp === '1970-01-01T00:00:00Z') {
                        this.latestTimestamp = this.notifications[0].created_at;
                    }
                    
                    // Play sound for truly new notifications (not already seen)
                    if (playSound && !this.isFirstLoad) {
                        const newNotifications = this.notifications.filter(notification => 
                            !this.soundPlayedForNotifications.has(notification.id) && 
                            !notification.read_at // Only unread notifications
                        );
                        
                        if (newNotifications.length > 0) {
                            console.log(`🔔 ${newNotifications.length} new notifications detected, playing sounds`);
                            
                            // Mark these notifications as having had their sound played
                            newNotifications.forEach(notification => {
                                this.soundPlayedForNotifications.add(notification.id);
                                this.queueNotificationSound();
                            });
                            
                            // Show browser notification for the first new notification
                            this.showBrowserNotification(newNotifications[0]);
                            
                            // Animate the bell icon
                            this.animateBellIcon();
                        }
                    }
                    
                    // Mark first load as complete
                    if (this.isFirstLoad) {
                        this.isFirstLoad = false;
                        // Add all current notification IDs to the "already played" set on first load
                        this.notifications.forEach(notification => {
                            this.soundPlayedForNotifications.add(notification.id);
                        });
                    }
                    
                    // Request notification permission on first load
                    if (previousCount === 0 && 'Notification' in window && Notification.permission === 'default') {
                        this.requestNotificationPermission();
                    }
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        },
        
        async requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();
                if (permission === 'granted') {
                    console.log('Browser notifications enabled');
                } else {
                    console.log('Browser notifications disabled');
                }
            } catch (error) {
                console.warn('Could not request notification permission:', error);
            }
        },
        
        async markAsRead(notificationId, url = null) {
            try {
                const response = await fetch(`/admin/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Update the notification in the list
                    const notificationIndex = this.notifications.findIndex(n => n.id === notificationId);
                    if (notificationIndex !== -1) {
                        this.notifications[notificationIndex].read_at = new Date().toISOString();
                        this.notificationCount = Math.max(0, this.notificationCount - 1);
                    }
                    
                    // Keep the notification in the sound tracking set (don't remove it)
                    // This prevents the same notification from playing sound again if refreshed
                    
                    if (url) {
                        this.closeDropdown();
                        setTimeout(() => {
                            window.location.href = url;
                        }, 100);
                    }
                }
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        },
        
        async markAllAsRead() {
            try {
                const response = await fetch('/admin/notifications/mark-all-read', {
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
                        // Ensure all notifications are tracked as having played sound
                        this.soundPlayedForNotifications.add(notification.id);
                    });
                    this.notificationCount = 0;
                }
            } catch (error) {
                console.error('Error marking all notifications as read:', error);
            }
        },
        
        getNotificationUrl(data) {
            if (data.type === 'feedback') {
                return '/admin/feedback?type=order';
            } else if (data.type === 'system_feedback') {
                return '/admin/feedback?type=system';
            } else if (data.type === 'order') {
                return '/admin/orders';
            }
            return '/admin/dashboard';
        },
        
        getNotificationIconClass(type) {
            const classes = {
                'feedback': 'bg-gradient-to-r from-yellow-500 to-orange-500',
                'system_feedback': 'bg-gradient-to-r from-blue-500 to-indigo-600',
                'order': 'bg-gradient-to-r from-green-500 to-emerald-600'
            };
            return classes[type] || 'bg-gradient-to-r from-gray-500 to-gray-600';
        },

        getNotificationBadgeClass(type) {
            const classes = {
                'feedback': 'bg-yellow-100 text-yellow-800',
                'system_feedback': 'bg-blue-100 text-blue-800',
                'order': 'bg-green-100 text-green-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        getNotificationTypeLabel(type) {
            const labels = {
                'feedback': 'Customer Feedback',
                'system_feedback': 'System Feedback',
                'order': 'Order Update'
            };
            return labels[type] || 'Notification';
        },
        
        formatTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) {
                return 'Just now';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} day${days > 1 ? 's' : ''} ago`;
            }
        },
        
        // Cleanup when component is destroyed
        destroy() {
            if (this.eventSource) {
                this.eventSource.close();
                this.eventSource = null;
            }
            if (this.audioContext) {
                this.audioContext.close();
                this.audioContext = null;
            }
            if (this.audioElement) {
                this.audioElement.pause();
                this.audioElement = null;
            }
            // Clear audio queue and tracking sets
            this.audioQueue = [];
            this.isPlayingAudio = false;
            this.soundPlayedForNotifications.clear();
        }
    }
}
</script>

<style>
/* Custom scrollbar styles for notification dropdown - Functional and visible */
.notification-scroll {
    scrollbar-width: auto;
    scrollbar-color: #6b7280 #e5e7eb;
    position: relative;
}

.notification-scroll::-webkit-scrollbar {
    width: 14px;
    background: #f9fafb;
}

.notification-scroll::-webkit-scrollbar-track {
    background: #f9fafb;
    border-radius: 8px;
    margin: 8px 0;
    border: 1px solid #e5e7eb;
    box-shadow: inset 0 0 3px rgba(0,0,0,0.1);
}

.notification-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #6b7280 0%, #4b5563 100%);
    border-radius: 8px;
    border: 2px solid #f9fafb;
    min-height: 40px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.notification-scroll::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #4b5563 0%, #374151 100%);
    cursor: pointer;
}

.notification-scroll::-webkit-scrollbar-thumb:active {
    background: linear-gradient(180deg, #374151 0%, #1f2937 100%);
}

.notification-scroll::-webkit-scrollbar-corner {
    background: #f9fafb;
}

/* Responsive dropdown sizing for different screen sizes - Force override with !important */
@media (max-width: 1400px) {
    .notification-dropdown {
        width: 75vw !important;
        max-width: 75vw !important;
        right: 12.5vw !important;
        left: auto !important;
        min-width: 500px !important;
    }
}

@media (max-width: 1024px) {
    .notification-dropdown {
        width: 85vw !important;
        max-width: 85vw !important;
        right: 7.5vw !important;
        left: auto !important;
    }
}

@media (max-width: 768px) {
    .notification-dropdown {
        width: 90vw !important;
        max-width: 90vw !important;
        right: 5vw !important;
        left: auto !important;
    }
}

@media (max-width: 480px) {
    .notification-dropdown {
        width: 92vw !important;
        max-width: 92vw !important;
        right: 4vw !important;
        left: auto !important;
    }
}
</style> 