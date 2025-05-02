// Microbiology-themed loading screen component
export default () => {
    return {
        show: false, // Initially hidden
        init() {
            console.log('LoadingScreen initialized');
            
            // Make the component available globally for direct manipulation
            window.loadingScreenComponent = this;
            
            // Simple function to show the loading screen
            window.showLoadingScreen = () => {
                console.log('showLoadingScreen called');
                this.show = true;
            };
            
            // Simple function to hide the loading screen
            window.hideLoadingScreen = () => {
                console.log('hideLoadingScreen called');
                this.show = false;
            };
            
            // Listen for navigation events
            window.addEventListener('before-navigate', () => {
                console.log('Navigation started - showing loader');
                this.show = true;
            });
            
            window.addEventListener('after-navigate', () => {
                console.log('Navigation ended - hiding loader');
                this.show = false;
            });
            
            // Also listen for Turbo Drive events if present
            document.addEventListener('turbo:before-visit', () => {
                console.log('Turbo navigation started');
                this.show = true;
            });
            
            document.addEventListener('turbo:load', () => {
                console.log('Turbo navigation ended');
                this.show = false;
            });
            
            // Test the loading screen automatically after initialization
            setTimeout(() => {
                console.log('Auto-testing loading screen...');
                this.show = true;
                
                setTimeout(() => {
                    this.show = false;
                }, 2000);
            }, 2000);
        }
    };
}; 