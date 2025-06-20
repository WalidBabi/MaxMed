// Notification trigger for immediate feedback after form submissions
window.triggerNotificationCheck = function() {
    console.log('Manual notification check triggered'); // Debug log
    
    // Try to trigger CRM notification check
    const crmNotificationComponents = document.querySelectorAll('[x-data*="crmNotificationDropdown"]');
    console.log('Found CRM notification components:', crmNotificationComponents.length); // Debug log
    
    crmNotificationComponents.forEach(element => {
        const alpineData = element._x_dataStack && element._x_dataStack[0];
        if (alpineData && alpineData.checkForNewNotifications) {
            console.log('Triggering CRM notification check'); // Debug log
            alpineData.checkForNewNotifications();
        }
    });
    
    // Try to trigger supplier notification check
    const supplierNotificationComponents = document.querySelectorAll('[x-data*="supplierNotificationDropdown"]');
    console.log('Found supplier notification components:', supplierNotificationComponents.length); // Debug log
    
    supplierNotificationComponents.forEach(element => {
        const alpineData = element._x_dataStack && element._x_dataStack[0];
        if (alpineData && alpineData.checkForNewNotifications) {
            console.log('Triggering supplier notification check'); // Debug log
            alpineData.checkForNewNotifications();
        }
    });
    
    // Try to trigger admin notification check too
    const adminNotificationComponents = document.querySelectorAll('[x-data*="notificationDropdown"]');
    console.log('Found admin notification components:', adminNotificationComponents.length); // Debug log
    
    adminNotificationComponents.forEach(element => {
        const alpineData = element._x_dataStack && element._x_dataStack[0];
        if (alpineData && alpineData.checkForNewNotifications) {
            console.log('Triggering admin notification check'); // Debug log
            alpineData.checkForNewNotifications();
        }
    });
    
    // Also try using the global admin component if available
    if (typeof window.adminNotificationComponent !== 'undefined') {
        console.log('Triggering admin notification check via global component'); // Debug log
        window.adminNotificationComponent.checkForNewNotifications();
    }
};

// Also expose a function to play notification sound manually
window.playNotificationSound = function() {
    console.log('Manual sound test triggered'); // Debug log
    
    // Create a simple audio element and play the notification sound
    const audio = new Audio('/audio/notification.mp3');
    audio.volume = 0.6;
    
    const playPromise = audio.play();
    
    if (playPromise !== undefined) {
        playPromise.then(() => {
            console.log('Manual sound test played successfully'); // Debug log
        }).catch(error => {
            console.warn('Could not play notification sound:', error);
        });
    }
    
    // Also try to trigger the notification components to play their sounds
    const crmComponents = document.querySelectorAll('[x-data*="crmNotificationDropdown"]');
    crmComponents.forEach(element => {
        const alpineData = element._x_dataStack && element._x_dataStack[0];
        if (alpineData && alpineData.queueNotificationSound) {
            console.log('Triggering CRM component sound'); // Debug log
            alpineData.queueNotificationSound();
        }
    });
};

// Auto-trigger notification check on page load for immediate feedback
document.addEventListener('DOMContentLoaded', function() {
    // Wait 2 seconds after page load to check for new notifications
    setTimeout(window.triggerNotificationCheck, 2000);
}); 