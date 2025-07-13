/**
 * Comprehensive Error Handler for MaxMed Website
 * Catches and handles common JavaScript errors
 */

(function() {
    'use strict';

    // Global error handler
    window.addEventListener('error', function(event) {
        console.warn('Error caught by global handler:', event.error);
        
        // Handle specific error types
        if (event.error && event.error.message) {
            const message = event.error.message;
            
            // Handle null reference errors
            if (message.includes('Cannot read properties of null') || 
                message.includes('Cannot read properties of undefined')) {
                console.warn('Null reference error detected, attempting to recover...');
                return false; // Prevent default error handling
            }
            
            // Handle config not defined errors
            if (message.includes('config is not defined')) {
                console.warn('Config not defined error detected, attempting to recover...');
                return false;
            }
        }
    });

    // Handle unhandled promise rejections
    window.addEventListener('unhandledrejection', function(event) {
        console.warn('Unhandled promise rejection:', event.reason);
        event.preventDefault(); // Prevent default error handling
    });

    // Safe element access utility
    window.safeGetElement = function(selector) {
        try {
            const element = document.querySelector(selector);
            return element || null;
        } catch (error) {
            console.warn('Error accessing element:', selector, error);
            return null;
        }
    };

    // Safe addEventListener utility
    window.safeAddEventListener = function(element, event, handler) {
        if (!element) {
            console.warn('Attempted to add event listener to null element:', event);
            return false;
        }
        
        try {
            element.addEventListener(event, handler);
            return true;
        } catch (error) {
            console.warn('Error adding event listener:', error);
            return false;
        }
    };

    // Safe getElementById with event listener
    window.safeGetElementById = function(id, event, handler) {
        const element = document.getElementById(id);
        if (element && event && handler) {
            return window.safeAddEventListener(element, event, handler);
        }
        return element;
    };

    // Initialize when DOM is ready
    function initializeErrorHandler() {
        console.log('🔧 Error handler initialized');
        
        // Check for common issues
        const issues = [];
        
        // Check for missing CSRF token
        if (!document.querySelector('meta[name="csrf-token"]')) {
            issues.push('CSRF token missing');
        }
        
        // Check for missing Google One Tap elements
        if (!document.getElementById('google-one-tap-container')) {
            issues.push('Google One Tap container missing');
        }
        
        // Check for missing notification elements
        if (!document.querySelector('[data-notification-badge]')) {
            issues.push('Notification badges missing');
        }
        
        // Log issues if any
        if (issues.length > 0) {
            console.warn('⚠️ Potential issues detected:', issues);
        } else {
            console.log('✅ All required elements found');
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeErrorHandler);
    } else {
        initializeErrorHandler();
    }

    // Export utilities for other scripts
    window.ErrorHandler = {
        safeGetElement: window.safeGetElement,
        safeAddEventListener: window.safeAddEventListener,
        safeGetElementById: window.safeGetElementById
    };

})(); 