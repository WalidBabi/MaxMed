// Product Price Calculator
// Handles price conversions and calculations for admin product forms

document.addEventListener('DOMContentLoaded', function() {
    // Exchange rate (USD to AED) - you can update this as needed
    const USD_TO_AED_RATE = 3.67;
    
    // Markup percentage for customer prices
    const MARKUP_PERCENTAGE = 40;
    
    // Get form elements
    const procurementPriceUsdInput = document.getElementById('procurement_price_usd');
    const procurementPriceAedInput = document.getElementById('procurement_price_aed');
    const customerPriceUsdInput = document.getElementById('price');
    const customerPriceAedInput = document.getElementById('price_aed');
    
    // Function to convert USD to AED
    function convertUsdToAed(usdAmount) {
        return (usdAmount * USD_TO_AED_RATE).toFixed(2);
    }
    
    // Function to convert AED to USD
    function convertAedToUsd(aedAmount) {
        return (aedAmount / USD_TO_AED_RATE).toFixed(2);
    }
    
    // Function to calculate customer price with markup
    function calculateCustomerPrice(procurementPrice) {
        return (procurementPrice * (1 + MARKUP_PERCENTAGE / 100)).toFixed(2);
    }
    
    // Function to update AED procurement price when USD changes
    function updateAedProcurementPrice() {
        if (procurementPriceUsdInput && procurementPriceAedInput) {
            const usdValue = parseFloat(procurementPriceUsdInput.value) || 0;
            const aedValue = convertUsdToAed(usdValue);
            procurementPriceAedInput.value = aedValue;
            
            // Also update customer prices
            updateCustomerPrices();
        }
    }
    
    // Function to update USD procurement price when AED changes
    function updateUsdProcurementPrice() {
        if (procurementPriceAedInput && procurementPriceUsdInput) {
            const aedValue = parseFloat(procurementPriceAedInput.value) || 0;
            const usdValue = convertAedToUsd(aedValue);
            procurementPriceUsdInput.value = usdValue;
            
            // Also update customer prices
            updateCustomerPrices();
        }
    }
    
    // Function to update customer prices based on procurement prices
    function updateCustomerPrices() {
        if (procurementPriceUsdInput && customerPriceUsdInput) {
            const usdProcurement = parseFloat(procurementPriceUsdInput.value) || 0;
            const usdCustomerPrice = calculateCustomerPrice(usdProcurement);
            customerPriceUsdInput.value = usdCustomerPrice;
        }
        
        if (procurementPriceAedInput && customerPriceAedInput) {
            const aedProcurement = parseFloat(procurementPriceAedInput.value) || 0;
            const aedCustomerPrice = calculateCustomerPrice(aedProcurement);
            customerPriceAedInput.value = aedCustomerPrice;
        }
    }
    
    // Add event listeners for procurement price inputs
    if (procurementPriceUsdInput) {
        procurementPriceUsdInput.addEventListener('input', updateAedProcurementPrice);
    }
    
    if (procurementPriceAedInput) {
        procurementPriceAedInput.addEventListener('input', updateUsdProcurementPrice);
    }
    
    // Add event listeners for customer price inputs (manual override)
    if (customerPriceUsdInput) {
        customerPriceUsdInput.addEventListener('input', function() {
            // When customer manually changes USD price, update AED price
            const usdValue = parseFloat(customerPriceUsdInput.value) || 0;
            const aedValue = convertUsdToAed(usdValue);
            if (customerPriceAedInput) {
                customerPriceAedInput.value = aedValue;
            }
        });
    }
    
    if (customerPriceAedInput) {
        customerPriceAedInput.addEventListener('input', function() {
            // When customer manually changes AED price, update USD price
            const aedValue = parseFloat(customerPriceAedInput.value) || 0;
            const usdValue = convertAedToUsd(aedValue);
            if (customerPriceUsdInput) {
                customerPriceUsdInput.value = usdValue;
            }
        });
    }
    
    // Initialize calculations if procurement prices are already set
    if (procurementPriceUsdInput && procurementPriceUsdInput.value) {
        updateAedProcurementPrice();
    } else if (procurementPriceAedInput && procurementPriceAedInput.value) {
        updateUsdProcurementPrice();
    }
}); 