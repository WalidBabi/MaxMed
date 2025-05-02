// Navigation loader - backward compatibility only
document.addEventListener('DOMContentLoaded', () => {
    console.log('Navigation loader initialized - compatibility mode only');
    
    // This file now only serves as a compatibility layer for any code that might
    // be using the before-navigate/after-navigate events from previous implementation
    
    // Forward any old-style events to the new microLoader
    window.addEventListener('before-navigate', () => {
        console.log('Legacy before-navigate event caught, forwarding to microLoader');
        if (window.microLoader) {
            window.microLoader.show();
        }
    });
    
    window.addEventListener('after-navigate', () => {
        console.log('Legacy after-navigate event caught, forwarding to microLoader');
        if (window.microLoader) {
            window.microLoader.hide();
        }
    });
    
    // Don't listen for navigation events here since app.blade.php already handles that
    // This prevents double triggering of the loading screen
}); 