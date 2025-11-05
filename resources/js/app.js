import './bootstrap';

import Alpine from 'alpinejs';
import { registerAjaxUtilities } from './utils/ajaxForm';
import { progressiveTableLoader } from './utils/progressiveLoader';

window.Alpine = Alpine;

// Prevent FOUC (Flash of Unstyled Content) for Alpine components
document.documentElement.classList.add('no-fouc');

// Initialize Alpine with better timing
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebars immediately to prevent flashing
    document.querySelectorAll('.sidebar, .sidebar-column, .crm-sidebar, .supplier-sidebar, .sidebar-container').forEach(el => {
        el.style.opacity = '1';
        el.style.visibility = 'visible';
        el.classList.add('sidebar-initialized');
    });
    
    // Initialize navigation immediately
    const navbar = document.querySelector('nav');
    if (navbar) {
        navbar.style.opacity = '1';
        navbar.style.visibility = 'visible';
        navbar.classList.add('initialized');
    }
    
    // Ensure navigation state is cleared
    document.body.classList.remove('navigating');

    // Register global AJAX utilities for progressive enhancement forms/actions
    registerAjaxUtilities();

    // Initialize progressive loaders for tables (loads content from fastest to slowest)
    progressiveTableLoader.initAll();

    // Optimize initial page load: progressive rendering of heavy content
    (function optimizeInitialLoad() {
        // Target tables in main-content or anywhere in the page
        const tables = document.querySelectorAll('#main-content table tbody, main table tbody, .main-content table tbody');
        
        tables.forEach(tbody => {
            const rows = tbody.querySelectorAll('tr');
            if (rows.length === 0) return;
            
            // Store original rows
            const originalRows = Array.from(rows);
            
            // Clear tbody immediately
            tbody.innerHTML = '';
            
            // Show skeleton while processing
            const skeleton = document.createElement('tr');
            skeleton.className = 'skeleton-row';
            skeleton.innerHTML = `
                <td colspan="100" class="px-6 py-6">
                    <div class="space-y-3">
                        ${Array.from({ length: Math.min(5, rows.length) }).map(() => `
                            <div class="flex space-x-4 animate-pulse">
                                <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                <div class="h-4 bg-gray-200 rounded w-24"></div>
                            </div>
                        `).join('')}
                    </div>
                </td>
            `;
            tbody.appendChild(skeleton);
            
            // Progressive rendering in smaller batches for more visible progress
            const renderRowsProgressively = () => {
                // Remove skeleton
                tbody.innerHTML = '';
                
                const batchSize = 5; // Smaller batches for more visible progress
                let currentIndex = 0;
                
                const renderBatch = () => {
                    const endIndex = Math.min(currentIndex + batchSize, originalRows.length);
                    const batch = originalRows.slice(currentIndex, endIndex);
                    
                    batch.forEach((row, idx) => {
                        row.style.opacity = '0';
                        row.style.transform = 'translateY(-10px)';
                        tbody.appendChild(row);
                        
                        // Stagger the fade-in for each row
                        setTimeout(() => {
                            row.style.transition = 'opacity 0.3s ease-in, transform 0.3s ease-out';
                            row.style.opacity = '1';
                            row.style.transform = 'translateY(0)';
                        }, idx * 30); // 30ms delay between each row
                    });
                    
                    currentIndex = endIndex;
                    
                    // Continue rendering if more rows exist
                    if (currentIndex < originalRows.length) {
                        setTimeout(renderBatch, 150); // 150ms between batches
                    }
                };
                
                // Start rendering after a short delay to let structure appear first
                setTimeout(renderBatch, 200);
            };
            
            // Start progressive rendering
            renderRowsProgressively();
        });
    })();

    // Partial navigation for sidebar links (PJAX-style)
    (function enablePartialNavigation() {
        const contentSelector = '#main-content';
        const sidebarSelectors = ['.sidebar-container', '.supplier-sidebar', '.crm-sidebar'];
        
        // Ensure smooth transitions for main content
        const contentEl = document.querySelector(contentSelector);
        if (contentEl) {
            contentEl.style.opacity = '1';
            contentEl.style.willChange = 'opacity';
        }
        const isSameOrigin = (href) => {
            try { const u = new URL(href, window.location.origin); return u.origin === window.location.origin; } catch { return false; }
        };

        // No loading indicator - just keep content visible during transitions
        const setLoading = (loading) => {
            const el = document.querySelector(contentSelector);
            if (!el) return;
            
            if (loading) {
                // Just disable pointer events during load, no visual indicator
                el.style.pointerEvents = 'none';
            } else {
                // Re-enable interactions
                el.style.pointerEvents = '';
            }
        };

        const highlightActive = (url) => {
            // Find sidebar container that contains the URL
            const containers = sidebarSelectors.flatMap(sel => Array.from(document.querySelectorAll(sel)));
            if (containers.length === 0) return;
            
            // Clear all active states first
            containers.forEach(container => {
                const links = container.querySelectorAll('a[href]');
                links.forEach(a => a.classList.remove('sidebar-active'));
            });
            
            // Set active state on matching link
            try {
                const u = new URL(url, window.location.origin);
                containers.forEach(container => {
                    const links = container.querySelectorAll('a[href]');
                    const active = Array.from(links).find(a => {
                        try {
                            const aUrl = new URL(a.href, window.location.origin);
                            return aUrl.pathname === u.pathname;
                        } catch { return false; }
                    });
                    if (active) active.classList.add('sidebar-active');
                });
            } catch {}
        };

        const executeScripts = (scopeEl) => {
            const scripts = Array.from(scopeEl.querySelectorAll('script'));
            scripts.forEach(oldScript => {
                // Skip if already executed marker
                if (oldScript.dataset.executed === 'true') return;
                const newScript = document.createElement('script');
                // Copy attributes
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                if (oldScript.src) {
                    newScript.async = false;
                    newScript.src = oldScript.src;
                } else {
                    newScript.textContent = oldScript.textContent || '';
                }
                oldScript.parentNode?.replaceChild(newScript, oldScript);
                newScript.dataset.executed = 'true';
            });
        };

        const swapContent = (doc) => {
            const newContent = doc.querySelector(contentSelector);
            const current = document.querySelector(contentSelector);
            if (!newContent || !current) return false;
            
            // Swap content immediately (sidebar is never touched)
            current.innerHTML = newContent.innerHTML;
            
            // Progressive rendering of tables for navigation
            const tables = current.querySelectorAll('table tbody');
            tables.forEach(tbody => {
                const rows = tbody.querySelectorAll('tr');
                if (rows.length === 0) return;
                
                // Store original rows
                const originalRows = Array.from(rows);
                
                // Clear tbody immediately
                tbody.innerHTML = '';
                
                // Show skeleton
                const skeleton = document.createElement('tr');
                skeleton.className = 'skeleton-row';
                skeleton.innerHTML = `
                    <td colspan="100" class="px-6 py-6">
                        <div class="space-y-3">
                            ${Array.from({ length: Math.min(5, rows.length) }).map(() => `
                                <div class="flex space-x-4 animate-pulse">
                                    <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                    <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                    <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                    <div class="h-4 bg-gray-200 rounded flex-1"></div>
                                    <div class="h-4 bg-gray-200 rounded w-24"></div>
                                </div>
                            `).join('')}
                        </div>
                    </td>
                `;
                tbody.appendChild(skeleton);
                
                // Progressive rendering
                const renderRowsProgressively = () => {
                    tbody.innerHTML = '';
                    
                    const batchSize = 5;
                    let currentIndex = 0;
                    
                    const renderBatch = () => {
                        const endIndex = Math.min(currentIndex + batchSize, originalRows.length);
                        const batch = originalRows.slice(currentIndex, endIndex);
                        
                        batch.forEach((row, idx) => {
                            row.style.opacity = '0';
                            row.style.transform = 'translateY(-10px)';
                            tbody.appendChild(row);
                            
                            setTimeout(() => {
                                row.style.transition = 'opacity 0.3s ease-in, transform 0.3s ease-out';
                                row.style.opacity = '1';
                                row.style.transform = 'translateY(0)';
                            }, idx * 30);
                        });
                        
                        currentIndex = endIndex;
                        
                        if (currentIndex < originalRows.length) {
                            setTimeout(renderBatch, 150);
                        }
                    };
                    
                    setTimeout(renderBatch, 200);
                };
                
                renderRowsProgressively();
            });
            
            // Execute scripts and re-bind utilities asynchronously
            requestAnimationFrame(() => {
                executeScripts(current);
                try { 
                    registerAjaxUtilities();
                    progressiveTableLoader.initAll();
                } catch {}
            });
            
            return true;
        };

        const navigateTo = async (url, push = true, isPortalSwitch = false) => {
            // Optimistically update active link for instant feedback (sidebar only)
            if (!isPortalSwitch) {
                highlightActive(url);
            }
            
            // Disable interactions during navigation
            setLoading(true);
            
            try {
                const resp = await fetch(url, { 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest', 
                        'Accept': 'text/html',
                        'Cache-Control': 'no-cache'
                    } 
                });
                
                if (!resp.ok) {
                    throw new Error(`HTTP ${resp.status}`);
                }
                
                const html = await resp.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // For portal switches, swap both sidebar and content
                if (isPortalSwitch) {
                    const newSidebar = doc.querySelector('.sidebar-container');
                    const currentSidebar = document.querySelector('.sidebar-container');
                    
                    if (newSidebar && currentSidebar) {
                        // Swap sidebar smoothly
                        currentSidebar.style.transition = 'opacity 0.2s ease-out';
                        currentSidebar.style.opacity = '0';
                        
                        setTimeout(() => {
                            currentSidebar.innerHTML = newSidebar.innerHTML;
                            currentSidebar.style.opacity = '1';
                            
                            // Swap content
                            if (!swapContent(doc)) {
                                window.location.href = url;
                                return;
                            }
                            
                            // Update title and URL
                            const newTitle = doc.querySelector('title')?.textContent || doc.title;
                            if (newTitle) document.title = newTitle;
                            
                            if (push) {
                                history.pushState({ url }, '', url);
                            }
                            
                            setLoading(false);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }, 200);
                    } else {
                        // Fallback: swap content only
                        if (!swapContent(doc)) {
                            window.location.href = url;
                            return;
                        }
                        
                        const newTitle = doc.querySelector('title')?.textContent || doc.title;
                        if (newTitle) document.title = newTitle;
                        
                        if (push) {
                            history.pushState({ url }, '', url);
                        }
                        
                        requestAnimationFrame(() => {
                            setLoading(false);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
                    }
                } else {
                    // Regular navigation within same portal
                    if (!swapContent(doc)) {
                        window.location.href = url;
                        return;
                    }
                    
                    // Update title and URL
                    const newTitle = doc.querySelector('title')?.textContent || doc.title;
                    if (newTitle) document.title = newTitle;
                    
                    if (push) {
                        history.pushState({ url }, '', url);
                    }
                    
                    requestAnimationFrame(() => {
                        setLoading(false);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    });
                }
            } catch (e) {
                console.error('Partial navigation failed:', e);
                window.location.href = url;
            }
        };

        // Intercept clicks in sidebars (admin + supplier + crm)
        document.addEventListener('click', (e) => {
            const anchor = e.target.closest('a[href]');
            if (!anchor) return;

            // Only handle if the anchor is inside any sidebar container
            const isInsideSidebar = sidebarSelectors.some(sel => {
                const nodes = Array.from(document.querySelectorAll(sel));
                return nodes.some(node => node.contains(anchor));
            });
            if (!isInsideSidebar) return;

            if (anchor.target === '_blank' || anchor.hasAttribute('download')) return;
            const href = anchor.getAttribute('href');
            if (!href || !isSameOrigin(href)) return;
            
            // If same path and query (no-op), do nothing
            try {
                const next = new URL(anchor.href, window.location.origin);
                const curr = new URL(window.location.href);
                if (next.pathname === curr.pathname && next.search === curr.search && next.hash === '') {
                    e.preventDefault();
                    return;
                }
            } catch {}
            // Ignore anchors and JS links
            if (href.startsWith('#') || href.startsWith('javascript:')) return;
            e.preventDefault();
            
            // Check if switching between portals (different layouts)
            const currentPath = window.location.pathname;
            const targetPath = new URL(anchor.href, window.location.origin).pathname;
            
            const isPortalSwitch = (
                (currentPath.startsWith('/admin') && !targetPath.startsWith('/admin')) ||
                (currentPath.startsWith('/crm') && !targetPath.startsWith('/crm')) ||
                (currentPath.startsWith('/supplier') && !targetPath.startsWith('/supplier')) ||
                (!currentPath.startsWith('/admin') && !currentPath.startsWith('/crm') && !currentPath.startsWith('/supplier') && 
                 (targetPath.startsWith('/admin') || targetPath.startsWith('/crm') || targetPath.startsWith('/supplier')))
            );
            
            // Navigate with full layout swap for portal switches
            navigateTo(anchor.href, true, isPortalSwitch);
        });

        // Handle back/forward
        window.addEventListener('popstate', (ev) => {
            const url = (ev.state && ev.state.url) ? ev.state.url : window.location.href;
            navigateTo(url, false);
        });

        // Initial highlight
        highlightActive(window.location.href);
    })();
});

// Start Alpine after DOM is ready
Alpine.start();

// Add event listener for Alpine initialization
document.addEventListener('alpine:initialized', () => {
    document.body.classList.add('alpine-ready');
    
    // Ensure all Alpine components are properly initialized
    document.querySelectorAll('[x-data]').forEach(el => {
        if (el._x_dataStack) {
            el.classList.add('alpine-initialized');
        }
    });
    
    // Show all x-cloak elements after Alpine is ready
    setTimeout(() => {
        document.querySelectorAll('[x-cloak]').forEach(el => {
            el.style.display = '';
        });
    }, 50);
});
