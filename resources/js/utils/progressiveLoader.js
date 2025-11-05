/**
 * Progressive Loading Utility
 * Loads page content from fastest to slowest (critical content first, then tables)
 */

// Priority levels for loading
const LOAD_PRIORITY = {
    IMMEDIATE: 0,    // Critical content (header, navigation)
    HIGH: 1,         // Above-fold content (tables visible)
    MEDIUM: 2,       // Below-fold content (tables not visible)
    LOW: 3          // Deferred content (charts, analytics)
};

// Create skeleton loader for tables
const createTableSkeleton = (rows = 5, cols = 5) => {
    const skeleton = document.createElement('div');
    skeleton.className = 'table-skeleton';
    skeleton.innerHTML = `
        <div class="animate-pulse">
            <div class="space-y-3">
                ${Array.from({ length: rows }).map(() => `
                    <div class="flex space-x-4">
                        ${Array.from({ length: cols }).map(() => `
                            <div class="h-4 bg-gray-200 rounded flex-1"></div>
                        `).join('')}
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    return skeleton;
};

// Intersection Observer for lazy loading
let observer = null;

const initLazyLoader = () => {
    if (!('IntersectionObserver' in window)) {
        // Fallback: load all immediately if IntersectionObserver not supported
        document.querySelectorAll('[data-lazy-load]').forEach(el => loadLazyContent(el));
        return;
    }

    observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                loadLazyContent(target);
                observer.unobserve(target);
            }
        });
    }, {
        rootMargin: '100px', // Start loading 100px before element is visible
        threshold: 0.01
    });

    // Observe all lazy-load elements
    document.querySelectorAll('[data-lazy-load]').forEach(el => observer.observe(el));
};

// Load lazy content
const loadLazyContent = async (element) => {
    const url = element.dataset.lazyLoad;
    const priority = parseInt(element.dataset.loadPriority || LOAD_PRIORITY.MEDIUM);
    
    if (!url) return;

    try {
        // Show loading state
        const originalContent = element.innerHTML;
        element.classList.add('loading');
        
        // Fetch content
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Find the target content in the response
        const targetId = element.dataset.targetId || 'main-content';
        const newContent = doc.querySelector(`#${targetId}`) || doc.querySelector('[data-lazy-content]');
        
        if (newContent) {
            element.innerHTML = newContent.innerHTML;
            element.classList.remove('loading');
            element.classList.add('loaded');
            
            // Execute scripts in the new content
            executeScripts(element);
            
            // Dispatch event for components that need to reinitialize
            element.dispatchEvent(new CustomEvent('content-loaded', { detail: { element } }));
        } else {
            element.innerHTML = originalContent;
            element.classList.remove('loading');
        }
    } catch (error) {
        console.error('Failed to load lazy content:', error);
        element.classList.remove('loading');
        element.classList.add('error');
    }
};

// Execute scripts in loaded content
const executeScripts = (container) => {
    const scripts = Array.from(container.querySelectorAll('script'));
    scripts.forEach(oldScript => {
        if (oldScript.dataset.executed === 'true') return;
        
        const newScript = document.createElement('script');
        Array.from(oldScript.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });
        
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

// Progressive table loader
export const progressiveTableLoader = {
    /**
     * Initialize progressive loading for a table
     * @param {string} tableSelector - CSS selector for the table
     * @param {string} dataUrl - URL to fetch table data
     * @param {object} options - Configuration options
     */
    init: (tableSelector, dataUrl, options = {}) => {
        const {
            rows = 5,
            cols = 5,
            priority = LOAD_PRIORITY.HIGH,
            showSkeleton = true
        } = options;

        const tableContainer = document.querySelector(tableSelector);
        if (!tableContainer) return;

        // Store original content
        const originalContent = tableContainer.innerHTML;

        // Show skeleton immediately
        if (showSkeleton) {
            const skeleton = createTableSkeleton(rows, cols);
            tableContainer.innerHTML = '';
            tableContainer.appendChild(skeleton);
        }

        // Load data based on priority
        const loadData = async () => {
            try {
                const response = await fetch(dataUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);
                
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Find table in response
                const newTable = doc.querySelector(tableSelector) || doc.querySelector('table');
                
                if (newTable) {
                    tableContainer.innerHTML = newTable.outerHTML;
                    tableContainer.classList.add('loaded');
                    
                    // Execute scripts
                    executeScripts(tableContainer);
                    
                    // Dispatch event
                    tableContainer.dispatchEvent(new CustomEvent('table-loaded', { 
                        detail: { table: tableContainer } 
                    }));
                } else {
                    tableContainer.innerHTML = originalContent;
                }
            } catch (error) {
                console.error('Failed to load table data:', error);
                tableContainer.innerHTML = originalContent;
                tableContainer.classList.add('error');
            }
        };

        // Load based on priority
        if (priority === LOAD_PRIORITY.IMMEDIATE) {
            loadData();
        } else if (priority === LOAD_PRIORITY.HIGH) {
            // Load after a short delay (allow page structure to render first)
            requestAnimationFrame(() => {
                setTimeout(loadData, 50);
            });
        } else if (priority === LOAD_PRIORITY.MEDIUM) {
            // Use Intersection Observer for lazy loading
            tableContainer.setAttribute('data-lazy-load', dataUrl);
            tableContainer.setAttribute('data-load-priority', priority);
            tableContainer.setAttribute('data-target-id', tableSelector.replace('#', ''));
        } else {
            // Low priority - load after everything else
            setTimeout(loadData, 500);
        }
    },

    /**
     * Initialize all progressive loaders on the page
     */
    initAll: () => {
        // Initialize lazy loader
        initLazyLoader();

        // Auto-initialize tables with data-progressive attribute
        document.querySelectorAll('[data-progressive-table]').forEach(table => {
            const url = table.dataset.progressiveTable;
            const priority = parseInt(table.dataset.loadPriority || LOAD_PRIORITY.HIGH);
            const rows = parseInt(table.dataset.skeletonRows || 5);
            const cols = parseInt(table.dataset.skeletonCols || 5);

            progressiveTableLoader.init(`#${table.id}`, url, {
                priority,
                rows,
                cols,
                showSkeleton: true
            });
        });
    }
};

// Priority-based content loader
export const priorityLoader = {
    /**
     * Load content in priority order
     */
    load: async (items) => {
        // Sort by priority
        const sorted = items.sort((a, b) => (a.priority || LOAD_PRIORITY.MEDIUM) - (b.priority || LOAD_PRIORITY.MEDIUM));

        // Load in parallel within priority groups
        const priorityGroups = {};
        sorted.forEach(item => {
            const priority = item.priority || LOAD_PRIORITY.MEDIUM;
            if (!priorityGroups[priority]) priorityGroups[priority] = [];
            priorityGroups[priority].push(item);
        });

        // Load each priority group sequentially
        for (const priority in priorityGroups) {
            const group = priorityGroups[priority];
            await Promise.all(group.map(item => item.load()));
        }
    }
};

// Export priority levels
export { LOAD_PRIORITY };

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        progressiveTableLoader.initAll();
    });
} else {
    progressiveTableLoader.initAll();
}

