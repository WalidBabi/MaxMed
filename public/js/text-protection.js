/**
 * Text Protection Script for MaxMed Customer Website
 * Prevents text selection, copying, and other content protection features
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Configuration object
    const config = {
        disableRightClick: true,
        disableTextSelection: true,
        disableKeyboardShortcuts: true,
        disableDragAndDrop: true,
        disablePrintScreen: false, // Set to true if you want to disable print screen
        showWarningMessage: true,
        warningDuration: 3000
    };

    // Disable right-click context menu
    if (config.disableRightClick) {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (config.showWarningMessage) {
                showWarningMessage('Right-click is disabled on this website.');
            }
            
            return false;
        }, false);
    }

    // Disable text selection with mouse
    if (config.disableTextSelection) {
        document.addEventListener('selectstart', function(e) {
            // Allow selection in input fields and textareas
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'TEXTAREA' || 
                e.target.contentEditable === 'true' ||
                e.target.closest('.allow-select')) {
                return true;
            }
            
            e.preventDefault();
            return false;
        }, false);

        // Disable text selection on mobile
        document.addEventListener('touchstart', function(e) {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, false);
    }

    // Disable keyboard shortcuts
    if (config.disableKeyboardShortcuts) {
        document.addEventListener('keydown', function(e) {
            // Allow normal behavior in input fields
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'TEXTAREA' || 
                e.target.contentEditable === 'true' ||
                e.target.closest('.allow-select')) {
                return true;
            }

            // Disable Ctrl/Cmd combinations
            if (e.ctrlKey || e.metaKey) {
                // Common shortcuts to disable
                const disabledKeys = [
                    'a', 'c', 'v', 'x', 's', 'p', 'f', 'u', 'i', 'j'
                ];
                
                if (disabledKeys.includes(e.key.toLowerCase())) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    if (config.showWarningMessage) {
                        let action = '';
                        switch(e.key.toLowerCase()) {
                            case 'a': action = 'Select All'; break;
                            case 'c': action = 'Copy'; break;
                            case 'v': action = 'Paste'; break;
                            case 'x': action = 'Cut'; break;
                            case 's': action = 'Save'; break;
                            case 'p': action = 'Print'; break;
                            case 'f': action = 'Find'; break;
                            case 'u': action = 'View Source'; break;
                            case 'i': action = 'Developer Tools'; break;
                            case 'j': action = 'Developer Console'; break;
                        }
                        showWarningMessage(`${action} is disabled on this website.`);
                    }
                    
                    return false;
                }
            }

            // Disable F12 (Developer Tools)
            if (e.keyCode === 123) {
                e.preventDefault();
                e.stopPropagation();
                
                if (config.showWarningMessage) {
                    showWarningMessage('Developer tools are disabled on this website.');
                }
                
                return false;
            }

            // Disable Print Screen (optional)
            if (config.disablePrintScreen && e.keyCode === 44) {
                e.preventDefault();
                if (config.showWarningMessage) {
                    showWarningMessage('Print Screen is disabled on this website.');
                }
                return false;
            }
        }, false);
    }

    // Disable drag and drop
    if (config.disableDragAndDrop) {
        document.addEventListener('dragstart', function(e) {
            // Allow drag for input fields and allowed elements
            if (e.target.tagName === 'INPUT' || 
                e.target.tagName === 'TEXTAREA' || 
                e.target.closest('.allow-drag')) {
                return true;
            }
            
            e.preventDefault();
            return false;
        }, false);

        document.addEventListener('drop', function(e) {
            e.preventDefault();
            return false;
        }, false);
    }

    // Show warning message function
    function showWarningMessage(message) {
        // Remove existing warning if present
        const existingWarning = document.getElementById('text-protection-warning');
        if (existingWarning) {
            existingWarning.remove();
        }

        // Create warning element
        const warning = document.createElement('div');
        warning.id = 'text-protection-warning';
        warning.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff6b6b;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 500;
            max-width: 300px;
            word-wrap: break-word;
            animation: slideInRight 0.3s ease-out;
        `;
        warning.textContent = message;

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        document.body.appendChild(warning);

        // Auto remove after specified duration
        setTimeout(() => {
            if (warning && warning.parentNode) {
                warning.style.animation = 'slideOutRight 0.3s ease-in';
                setTimeout(() => {
                    if (warning && warning.parentNode) {
                        warning.remove();
                    }
                }, 300);
            }
        }, config.warningDuration);
    }

    // Advanced protection: Clear clipboard if something gets copied
    document.addEventListener('copy', function(e) {
        // Allow copying from input fields
        if (e.target.tagName === 'INPUT' || 
            e.target.tagName === 'TEXTAREA' || 
            e.target.contentEditable === 'true' ||
            e.target.closest('.allow-select')) {
            return true;
        }

        e.preventDefault();
        e.clipboardData.setData('text/plain', '');
        
        if (config.showWarningMessage) {
            showWarningMessage('Copying content is not allowed on this website.');
        }
        
        return false;
    });

    // Disable image dragging
    document.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG') {
            e.preventDefault();
            return false;
        }
    });

    // Console warning for developers
    console.warn('%cContent Protection Active', 'color: #ff6b6b; font-size: 16px; font-weight: bold;');
    console.warn('This website has content protection enabled. Unauthorized copying or extraction of content is prohibited.');

    // Advanced: Detect if developer tools are open (optional - can be intrusive)
    let devtools = {
        open: false,
        orientation: null
    };

    const threshold = 160;

    function detectDevTools() {
        if (window.outerHeight - window.innerHeight > threshold || 
            window.outerWidth - window.innerWidth > threshold) {
            if (!devtools.open) {
                devtools.open = true;
                console.clear();
                console.warn('%cDeveloper Tools Detected', 'color: #ff6b6b; font-size: 20px; font-weight: bold;');
                console.warn('Content on this website is protected. Please respect our terms of use.');
            }
        } else {
            devtools.open = false;
        }
    }

    // Check every 500ms (can be adjusted or disabled if too intrusive)
    // setInterval(detectDevTools, 500);

    console.log('%cMaxMed UAE - Text Protection Loaded', 'color: #0a5694; font-weight: bold;');
});

// Additional mobile-specific protections
if ('ontouchstart' in window) {
    // Disable long press on mobile
    document.addEventListener('touchstart', function(e) {
        if (e.touches.length > 1) {
            e.preventDefault();
        }
    });

    // Disable iOS callout
    document.addEventListener('touchstart', function(e) {
        if (!e.target.closest('input, textarea, .allow-select')) {
            e.preventDefault();
        }
    });

    // Disable text selection on mobile Safari
    document.addEventListener('gesturestart', function(e) {
        e.preventDefault();
    });
}

// Export configuration for external use
window.MaxMedTextProtection = {
    config: config,
    updateConfig: function(newConfig) {
        Object.assign(config, newConfig);
    }
}; 