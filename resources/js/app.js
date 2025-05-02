import './bootstrap';

import Alpine from 'alpinejs';
import loadingScreen from './components/LoadingScreen';
import './navigation-loader';

// Make Alpine available globally
window.Alpine = Alpine;

// Register the loadingScreen component with Alpine
Alpine.data('loadingScreen', loadingScreen);

// Start Alpine
document.addEventListener('DOMContentLoaded', () => {
    console.log('Starting Alpine.js');
    Alpine.start();
});
