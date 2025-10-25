# Stripe Payment Integration for Invoices

## Overview

Your MaxMed application now has integrated Stripe Payment Links in both **Proforma Invoices** and **Final Invoices**. Customers can click a button directly in the PDF to pay online securely.

---

## Features Implemented

### ✅ 1. Automatic Payment Link Generation
- Payment links are automatically generated when:
  - An invoice is sent via email
  - A PDF is generated
  - Manually triggered via admin panel

### ✅ 2. Beautiful Payment Button in PDF
- Professional Stripe-branded payment button
- Shows remaining amount due
- Clickable link in PDF for easy payment
- Fallback text link for copying

### ✅ 3. Smart Payment Logic
- **Proforma Invoices**: 
  - Calculates advance payment amount based on payment terms
  - 50% advance, 100% advance, or custom percentage
- **Final Invoices**: 
  - Shows remaining balance after any previous payments

### ✅ 4. Secure Payment Processing
- Uses Stripe Payment Links (PCI compliant)
- Automatic payment recording in your system
- Updates invoice payment status automatically

---

## How It Works

### For Proforma Invoices:

1. **Create Invoice** → System generates invoice
2. **Send Email** or **Download PDF** → Payment link is automatically created
3. **Customer Clicks "PAY NOW"** → Redirected to secure Stripe checkout
4. **Payment Complete** → Invoice updated, payment recorded
5. **Order Creation** → Workflow continues based on payment terms

### For Final Invoices:

1. **Convert to Final Invoice** → Final invoice created
2. **Payment Link Generated** → Shows remaining balance
3. **Customer Pays** → System records payment
4. **Invoice Marked as Paid** → Workflow complete

---

## Configuration

### Step 1: Set Up Stripe API Keys

Add your Stripe keys to `.env`:

```env
STRIPE_KEY=pk_live_your_publishable_key_here
STRIPE_SECRET=sk_live_your_secret_key_here
```

For testing, use test keys:
```env
STRIPE_KEY=pk_test_your_test_key
STRIPE_SECRET=sk_test_your_test_key
```

### Step 2: Get Stripe API Keys

1. Log in to [Stripe Dashboard](https://dashboard.stripe.com/)
2. Go to **Developers → API Keys**
3. Copy your **Publishable key** and **Secret key**
4. Add them to your `.env` file

---

## Usage

### Automatic Payment Links

Payment links are generated automatically in these scenarios:

1. **When sending an invoice via email**:
   - Go to Invoices → View Invoice
   - Click "Send Email"
   - Payment link automatically included in PDF

2. **When downloading PDF**:
   - Go to Invoices → View Invoice
   - Click "Download PDF"
   - Payment link automatically generated and shown in PDF

### Manual Payment Link Generation

You can also manually generate/regenerate payment links:

```javascript
// Via AJAX request
POST /admin/invoices/{invoice}/generate-payment-link
```

Or use the button in the admin panel (if you add it to the UI).

---

## Payment Button in PDF

The payment button appears in the PDF with:

- **Stripe branding** (purple gradient)
- **Clear call-to-action**: "PAY NOW WITH STRIPE"
- **Amount to pay**: Shows exact amount due
- **Clickable link**: Direct to Stripe checkout
- **Fallback text link**: For copy-pasting

### Example Display:

```
┌────────────────────────────────────┐
│  💳 Pay Online with Stripe         │
│                                    │
│  Click below to securely pay       │
│  AED 5,000.00 online              │
│                                    │
│  [⚡ PAY NOW WITH STRIPE]          │
│                                    │
│  Or copy this link:                │
│  https://pay.stripe.com/...        │
└────────────────────────────────────┘
```

---

## Payment Flow

### Customer Experience:

1. **Receives Invoice PDF** via email
2. **Opens PDF** and sees payment button
3. **Clicks "PAY NOW"** → Redirected to Stripe
4. **Enters payment details** on secure Stripe page
5. **Payment processed** → Redirected to success page
6. **Confirmation shown** with invoice details

### System Flow:

```
Invoice Created
    ↓
Payment Link Generated (Stripe API)
    ↓
PDF Generated with Payment Button
    ↓
Customer Clicks Pay Button
    ↓
Stripe Checkout Page
    ↓
Payment Successful
    ↓
Webhook/Callback to Your System
    ↓
Payment Recorded in Database
    ↓
Invoice Status Updated
    ↓
Order Workflow Continues
```

---

## Database Changes

### New Fields in `invoices` Table:

- `stripe_payment_link_id` (string, nullable): Stripe Payment Link ID
- `stripe_payment_link_url` (string, nullable): Full URL to payment page

These are automatically populated when payment links are generated.

---

## API Endpoints

### Public Routes (No Auth Required):

- `GET /invoice/{invoice}/payment/success`: Payment success callback
- `GET /invoice/{invoice}/payment/view`: View invoice (public access)

### Admin Routes (Auth Required):

- `POST /admin/invoices/{invoice}/generate-payment-link`: Generate payment link

---

## Code Structure

### Services:
- **`app/Services/InvoiceStripeService.php`**: Handles all Stripe Payment Link operations
  - `getOrCreatePaymentLink()`: Create or retrieve payment link
  - `calculatePaymentAmount()`: Calculate amount based on payment terms
  - `deactivatePaymentLink()`: Deactivate expired links

### Controllers:
- **`app/Http/Controllers/InvoicePaymentController.php`**: Handles payment callbacks
  - `paymentSuccess()`: Process successful payments
  - `viewInvoice()`: Public invoice viewing

- **`app/Http/Controllers/Admin/InvoiceController.php`**: Updated with payment link generation
  - `generatePaymentLink()`: Manual link generation
  - `sendEmail()`: Auto-generates link before sending
  - `generatePdf()`: Auto-generates link before PDF creation

### Views:
- **`resources/views/admin/invoices/pdf.blade.php`**: Invoice PDF template with payment button
- **`resources/views/invoice-payment-success.blade.php`**: Payment success page

---

## Testing

### Test Mode Setup:

1. Use Stripe test keys in `.env`
2. Create a test invoice
3. Generate PDF or send email
4. Click payment button
5. Use test card: `4242 4242 4242 4242`
6. Expiry: Any future date
7. CVC: Any 3 digits
8. ZIP: Any 5 digits

### Test Card Numbers:

- **Success**: 4242 4242 4242 4242
- **Declined**: 4000 0000 0000 0002
- **Requires Auth**: 4000 0025 0000 3155

---

## Customization

### Change Payment Button Style:

Edit `resources/views/admin/invoices/pdf.blade.php`:

```css
.payment-button-section {
    background: linear-gradient(135deg, #635BFF 0%, #4F46E5 100%);
    /* Change colors here */
}
```

### Change Button Text:

```html
<a href="{{ $invoice->stripe_payment_link_url }}" class="payment-button">
    ⚡ PAY NOW WITH STRIPE  <!-- Change this text -->
</a>
```

### Add Company Logo to Payment Page:

In `app/Services/InvoiceStripeService.php`, add to `PaymentLink::create()`:

```php
'custom_text' => [
    'submit' => [
        'message' => 'Payment for MaxMed Invoice'
    ]
]
```

---

## Troubleshooting

### Payment Link Not Appearing in PDF?

1. Check if Stripe keys are set in `.env`
2. Verify invoice is not fully paid
3. Check logs: `storage/logs/laravel.log`
4. Manually trigger: `POST /admin/invoices/{invoice}/generate-payment-link`

### Payment Not Being Recorded?

1. Check Stripe webhook configuration
2. Verify payment success callback is accessible
3. Check logs for errors during callback
4. Ensure `Payment` model is working correctly

### Button Not Clickable in PDF?

- PDF viewers vary in link support
- Ensure customers use modern PDF readers
- Fallback text link is always available for copying

---

## Security Considerations

1. **API Keys**: Never commit `.env` file to git
2. **Payment Verification**: Always verify payment status with Stripe API
3. **Invoice Access**: Payment success page uses invoice ID (consider adding token-based access)
4. **HTTPS**: Ensure your site uses HTTPS for payment callbacks

---

## Future Enhancements

Potential improvements you can add:

1. **QR Code**: Add QR code to PDF for mobile payments
2. **Email Notifications**: Send email confirmation after payment
3. **Partial Payments**: Allow customers to pay partial amounts
4. **Multiple Payment Methods**: Add support for bank transfers, etc.
5. **Payment Reminders**: Auto-send payment reminders for overdue invoices
6. **Stripe Webhooks**: Implement webhook handling for real-time updates

---

## Support

For issues or questions:

- **Stripe Documentation**: [https://stripe.com/docs](https://stripe.com/docs)
- **Stripe Support**: Available in your Stripe dashboard
- **Check Logs**: `storage/logs/laravel.log` for errors

---

## Summary

✅ Payment links automatically generated for all invoices
✅ Beautiful payment button in PDF
✅ Secure Stripe checkout process
✅ Automatic payment recording and invoice updates
✅ Works for both Proforma and Final invoices
✅ Supports advance payments and payment terms
✅ Mobile-friendly payment experience

Your customers can now pay invoices with just one click! 🎉

