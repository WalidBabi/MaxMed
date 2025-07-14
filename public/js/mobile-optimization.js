// Mobile Optimization Script for MaxMed UAE
// Based on Search Console data showing poor mobile CTR (1.76% vs Desktop 2.24%)

(function() {
    'use strict';

    // Mobile Performance Optimizations
    const mobileOptimizations = {
        init() {
            this.detectMobile();
            this.optimizeTouchTargets();
            this.optimizeImages();
            this.optimizeForms();
            this.optimizeNavigation();
            this.optimizeCTAs();
            this.optimizeLoading();
            this.trackMobileInteractions();
        },

        // Detect mobile devices
        detectMobile() {
            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
            if (isMobile) {
                document.body.classList.add('mobile-device');
                this.applyMobileOptimizations();
            }
        },

        // Apply mobile-specific optimizations
        applyMobileOptimizations() {
            // Optimize viewport
            const viewport = document.querySelector('meta[name="viewport"]');
            if (viewport) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=5, shrink-to-fit=no, viewport-fit=cover');
            }

            // Add mobile-specific classes
            document.body.classList.add('mobile-optimized');
        },

        // Optimize touch targets for better mobile interaction
        optimizeTouchTargets() {
            const touchTargets = document.querySelectorAll('.btn, .nav-link, .card, .product-card');
            touchTargets.forEach(target => {
                if (target.offsetHeight < 44 || target.offsetWidth < 44) {
                    target.style.minHeight = '44px';
                    target.style.minWidth = '44px';
                    target.style.padding = '12px 16px';
                }
            });
        },

        // Optimize images for mobile
        optimizeImages() {
            const images = document.querySelectorAll('img');
            images.forEach(img => {
                // Add lazy loading
                if (!img.hasAttribute('loading')) {
                    img.setAttribute('loading', 'lazy');
                }

                // Optimize for mobile
                if (window.innerWidth <= 768) {
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                }

                // Add error handling
                img.addEventListener('error', function() {
                    this.src = '/Images/placeholder.png';
                });
            });
        },

        // Optimize forms for mobile
        optimizeForms() {
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], select, textarea');
            inputs.forEach(input => {
                // Prevent zoom on iOS
                input.style.fontSize = '16px';
                
                // Add better focus states
                input.addEventListener('focus', function() {
                    this.style.borderColor = '#171e60';
                    this.style.boxShadow = '0 0 0 0.2rem rgba(23, 30, 96, 0.25)';
                });

                input.addEventListener('blur', function() {
                    this.style.borderColor = '#e0e0e0';
                    this.style.boxShadow = 'none';
                });
            });
        },

        // Optimize navigation for mobile
        optimizeNavigation() {
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                // Add mobile menu toggle
                const mobileMenuToggle = document.createElement('button');
                mobileMenuToggle.className = 'navbar-toggler d-md-none';
                mobileMenuToggle.setAttribute('type', 'button');
                mobileMenuToggle.setAttribute('aria-label', 'Toggle navigation');
                mobileMenuToggle.innerHTML = '<span class="navbar-toggler-icon"></span>';

                // Add to navbar
                navbar.appendChild(mobileMenuToggle);

                // Handle mobile menu
                mobileMenuToggle.addEventListener('click', function() {
                    const navbarCollapse = navbar.querySelector('.navbar-collapse');
                    if (navbarCollapse) {
                        navbarCollapse.classList.toggle('show');
                    }
                });
            }
        },

        // Optimize CTAs for better mobile conversion
        optimizeCTAs() {
            const ctas = document.querySelectorAll('.btn-primary, .btn-outline-primary');
            ctas.forEach(cta => {
                // Add mobile-specific styling
                if (window.innerWidth <= 768) {
                    cta.style.padding = '12px 24px';
                    cta.style.fontSize = '16px';
                    cta.style.fontWeight = '600';
                    cta.style.borderRadius = '8px';
                }

                // Add click tracking
                cta.addEventListener('click', function(e) {
                    this.trackCTAClick(e);
                });
            });
        },

        // Track CTA clicks for analytics
        trackCTAClick(event) {
            const cta = event.currentTarget;
            const ctaText = cta.textContent.trim();
            const ctaUrl = cta.href || '';

            // Send to analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', 'cta_click', {
                    'event_category': 'mobile_optimization',
                    'event_label': ctaText,
                    'value': 1
                });
            }

            // Track in localStorage for A/B testing
            const ctaClicks = JSON.parse(localStorage.getItem('mobile_cta_clicks') || '{}');
            ctaClicks[ctaText] = (ctaClicks[ctaText] || 0) + 1;
            localStorage.setItem('mobile_cta_clicks', JSON.stringify(ctaClicks));
        },

        // Optimize loading for mobile
        optimizeLoading() {
            // Preload critical resources
            const criticalResources = [
                '/css/mobile.css',
                '/Images/banner2.jpeg'
            ];

            criticalResources.forEach(resource => {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.href = resource;
                link.as = resource.endsWith('.css') ? 'style' : 'image';
                document.head.appendChild(link);
            });

            // Optimize font loading
            const fontLink = document.querySelector('link[href*="fonts.bunny.net"]');
            if (fontLink) {
                fontLink.setAttribute('media', 'print');
                fontLink.setAttribute('onload', "this.media='all'");
            }
        },

        // Track mobile interactions for optimization
        trackMobileInteractions() {
            // Track scroll depth
            let maxScroll = 0;
            window.addEventListener('scroll', function() {
                const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
                if (scrollPercent > maxScroll) {
                    maxScroll = scrollPercent;
                    if (maxScroll % 25 === 0) { // Track every 25%
                        this.trackScrollDepth(maxScroll);
                    }
                }
            }.bind(this));

            // Track time on page
            let startTime = Date.now();
            window.addEventListener('beforeunload', function() {
                const timeOnPage = Math.round((Date.now() - startTime) / 1000);
                this.trackTimeOnPage(timeOnPage);
            }.bind(this));
        },

        // Track scroll depth
        trackScrollDepth(depth) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'scroll_depth', {
                    'event_category': 'mobile_engagement',
                    'event_label': depth + '%',
                    'value': depth
                });
            }
        },

        // Track time on page
        trackTimeOnPage(seconds) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'time_on_page', {
                    'event_category': 'mobile_engagement',
                    'event_label': seconds + 's',
                    'value': seconds
                });
            }
        }
    };

    // Mobile-specific enhancements
    const mobileEnhancements = {
        init() {
            this.enhanceProductCards();
            this.enhanceSearch();
            this.enhanceContactInfo();
            this.enhanceNavigation();
        },

        // Enhance product cards for mobile
        enhanceProductCards() {
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                // Add touch feedback
                card.addEventListener('touchstart', function() {
                    this.style.transform = 'scale(0.98)';
                });

                card.addEventListener('touchend', function() {
                    this.style.transform = 'scale(1)';
                });

                // Add swipe gestures
                let startX = 0;
                let startY = 0;

                card.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                    startY = e.touches[0].clientY;
                });

                card.addEventListener('touchend', function(e) {
                    const endX = e.changedTouches[0].clientX;
                    const endY = e.changedTouches[0].clientY;
                    const diffX = startX - endX;
                    const diffY = startY - endY;

                    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
                        // Horizontal swipe detected
                        if (diffX > 0) {
                            // Swipe left - show more info
                            this.showProductDetails();
                        } else {
                            // Swipe right - add to cart
                            this.addToCart();
                        }
                    }
                });
            });
        },

        // Show product details
        showProductDetails() {
            // Implementation for showing product details
            console.log('Show product details');
        },

        // Add to cart
        addToCart() {
            // Implementation for adding to cart
            console.log('Add to cart');
        },

        // Enhance search for mobile
        enhanceSearch() {
            const searchForm = document.querySelector('.search-form');
            if (searchForm) {
                const searchInput = searchForm.querySelector('input[type="text"]');
                if (searchInput) {
                    // Add search suggestions
                    searchInput.addEventListener('input', function() {
                        this.showSearchSuggestions(this.value);
                    });

                    // Add voice search capability
                    if ('webkitSpeechRecognition' in window) {
                        const voiceButton = document.createElement('button');
                        voiceButton.type = 'button';
                        voiceButton.className = 'btn btn-outline-secondary';
                        voiceButton.innerHTML = '🎤';
                        voiceButton.style.marginLeft = '8px';
                        
                        voiceButton.addEventListener('click', function() {
                            this.startVoiceSearch();
                        }.bind(this));

                        searchForm.appendChild(voiceButton);
                    }
                }
            }
        },

        // Show search suggestions
        showSearchSuggestions(query) {
            // Implementation for search suggestions
            console.log('Search suggestions for:', query);
        },

        // Start voice search
        startVoiceSearch() {
            const recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                const searchInput = document.querySelector('.search-form input[type="text"]');
                if (searchInput) {
                    searchInput.value = transcript;
                    searchInput.form.submit();
                }
            };

            recognition.start();
        },

        // Enhance contact info for mobile
        enhanceContactInfo() {
            const phoneNumbers = document.querySelectorAll('a[href^="tel:"]');
            phoneNumbers.forEach(phone => {
                // Add click tracking
                phone.addEventListener('click', function() {
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'phone_click', {
                            'event_category': 'mobile_contact',
                            'event_label': this.href,
                            'value': 1
                        });
                    }
                });

                // Add visual feedback
                phone.addEventListener('touchstart', function() {
                    this.style.backgroundColor = '#f0f0f0';
                });

                phone.addEventListener('touchend', function() {
                    this.style.backgroundColor = '';
                });
            });
        },

        // Enhance navigation for mobile
        enhanceNavigation() {
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                // Add loading state
                link.addEventListener('click', function(e) {
                    if (!link.hasAttribute('data-no-transition')) {
                        link.style.opacity = '0.7';
                        link.style.pointerEvents = 'none';
                        
                        setTimeout(() => {
                            link.style.opacity = '';
                            link.style.pointerEvents = '';
                        }, 1000);
                    }
                });
            });
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            mobileOptimizations.init();
            mobileEnhancements.init();
        });
    } else {
        mobileOptimizations.init();
        mobileEnhancements.init();
    }

    // Export for global access
    window.mobileOptimizations = mobileOptimizations;
    window.mobileEnhancements = mobileEnhancements;

})(); 