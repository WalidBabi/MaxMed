import './bootstrap';

import Alpine from 'alpinejs';

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
