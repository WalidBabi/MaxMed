# Product Price Calculator - Admin Implementation

## Overview
This implementation adds automatic price conversion and calculation functionality to the admin product creation and editing forms.

## Features Implemented

### 1. Field Label Updates
- **Price (USD)** → **Customer Price (USD)**
- **Price (AED)** → **Customer Price (AED)**

### 2. Automatic Price Conversions
- When admin enters a value in **Procurement Price (USD)**, it automatically:
  - Converts to AED using exchange rate (3.67 USD = 1 AED)
  - Populates the **Procurement Price (AED)** field
  - Calculates customer prices with 40% markup

- When admin enters a value in **Procurement Price (AED)**, it automatically:
  - Converts to USD using exchange rate
  - Populates the **Procurement Price (USD)** field
  - Calculates customer prices with 40% markup

### 3. Customer Price Calculation
- Customer prices are automatically calculated as: `Procurement Price × 1.4` (40% markup)
- When procurement prices change, customer prices update automatically
- Manual changes to customer prices will sync between USD and AED

## Technical Implementation

### Files Modified
1. **`public/js/product-price-calculator.js`** - New JavaScript file for price calculations
2. **`resources/views/admin/products/create.blade.php`** - Updated labels and added script include
3. **`resources/views/admin/products/edit.blade.php`** - Updated labels and added script include

### JavaScript Features
- **Exchange Rate**: 3.67 USD = 1 AED (configurable in the script)
- **Markup Percentage**: 40% (configurable in the script)
- **Real-time Updates**: All calculations happen as the user types
- **Bidirectional Sync**: Changes in either currency update the other
- **Manual Override**: Users can manually adjust customer prices

### Usage Instructions

#### For Admins:
1. **Enter Procurement Price**: Start by entering either USD or AED procurement price
2. **Automatic Conversion**: The other currency will be calculated automatically
3. **Customer Price Calculation**: Customer prices will be calculated with 40% markup
4. **Manual Adjustments**: You can manually adjust customer prices if needed
5. **Sync**: Changes to customer prices in one currency will sync to the other

#### Example Workflow:
1. Enter **Procurement Price (USD)**: $100.00
2. **Procurement Price (AED)** automatically becomes: AED 367.00
3. **Customer Price (USD)** automatically becomes: $140.00 (100 × 1.4)
4. **Customer Price (AED)** automatically becomes: AED 513.80 (367 × 1.4)

## Configuration

### Exchange Rate
To update the exchange rate, modify the `USD_TO_AED_RATE` constant in `product-price-calculator.js`:
```javascript
const USD_TO_AED_RATE = 3.67; // Update this value as needed
```

### Markup Percentage
To change the markup percentage, modify the `MARKUP_PERCENTAGE` constant:
```javascript
const MARKUP_PERCENTAGE = 40; // Update this value as needed
```

## Browser Compatibility
- Works in all modern browsers
- Requires JavaScript to be enabled
- Gracefully degrades if JavaScript is disabled (forms still work, just without automatic calculations)

## Error Handling
- Invalid input values are treated as 0
- All calculations use `parseFloat()` with fallback to 0
- Results are formatted to 2 decimal places

## Future Enhancements
- Real-time exchange rate fetching from API
- Configurable markup percentages per product category
- Bulk price updates for multiple products
- Price history tracking 