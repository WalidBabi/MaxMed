// Add this JavaScript to update the hidden form quantity
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const formQuantityInput = document.getElementById('form-quantity');
    
    if (quantityInput && formQuantityInput) {
        // Initial sync
        formQuantityInput.value = quantityInput.value;
        
        // Update when quantity changes
        quantityInput.addEventListener('change', function() {
            formQuantityInput.value = this.value;
        });
        
        // Handle increase/decrease buttons
        document.getElementById('increase-qty')?.addEventListener('click', function() {
            formQuantityInput.value = quantityInput.value;
        });
        
        document.getElementById('decrease-qty')?.addEventListener('click', function() {
            formQuantityInput.value = quantityInput.value;
        });
    }
}); 