// Prevent back button on sensitive pages
(function() {
    // Only apply back button protection to sensitive pages
    const sensitivePaths = [
        '/checkout',
        '/payment',
        '/profile/edit',
        '/admin/settings'
    ];
    
    const currentPath = window.location.pathname;
    
    // Only apply protection if we're on a sensitive page
    if (sensitivePaths.some(path => currentPath.startsWith(path))) {
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, '', window.location.href);
        };
    }
})();

// Prevent form resubmission
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            // Disable submit button after first click
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
            }
            
            // Add timestamp to prevent cached submissions
            const timestampInput = document.createElement('input');
            timestampInput.type = 'hidden';
            timestampInput.name = 'submission_time';
            timestampInput.value = Date.now();
            form.appendChild(timestampInput);
        });
    });
}); 