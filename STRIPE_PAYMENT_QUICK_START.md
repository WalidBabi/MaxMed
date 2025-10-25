# ⚡ Stripe Payment Integration - Quick Start Guide

## 🎉 What's New?

Your MaxMed invoices now have **Stripe payment buttons** directly in the PDF! Customers can pay with one click.

---

## 🚀 Quick Setup (5 Minutes)

### Step 1: Add Stripe Keys to `.env`

Open your `.env` file and add:

```env
STRIPE_KEY=pk_test_your_test_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
```

**Get your keys from:** [https://dashboard.stripe.com/test/apikeys](https://dashboard.stripe.com/test/apikeys)

### Step 2: That's It! 🎊

The system is ready. Payment links will be automatically generated when you:
- Send an invoice email
- Download an invoice PDF

---

## 📱 Features Included

### ✅ Payment Button in PDF
- Beautiful Stripe-branded button
- Shows exact amount due
- Clickable link for instant payment

### ✅ QR Code for Mobile
- Scan with phone camera
- Direct to payment page
- Perfect for mobile users

### ✅ Automatic Payment Recording
- Payments automatically recorded
- Invoice status updated
- No manual data entry needed

### ✅ Smart Payment Amounts
- **Proforma Invoices**: Shows advance amount (50%, 100%, custom)
- **Final Invoices**: Shows remaining balance
- Automatically calculates based on payment terms

---

## 🎯 How to Use

### For New Invoices:

1. **Create Invoice** (as usual)
2. **Click "Send Email"** or **"Download PDF"**
3. **Payment link automatically generated**
4. **PDF includes payment button** + QR code

### For Existing Invoices:

- Just re-download the PDF or send email
- Payment link will be generated automatically
- Works for both proforma and final invoices

---

## 💳 Testing (Before Going Live)

### Test with Stripe Test Mode:

1. **Use test keys** in `.env` (keys starting with `pk_test_` and `sk_test_`)

2. **Create a test invoice**

3. **Download PDF** and click payment button

4. **Use test card**: 
   - Number: `4242 4242 4242 4242`
   - Expiry: Any future date
   - CVC: Any 3 digits
   - ZIP: Any 5 digits

5. **Complete payment** → Check if invoice updates

### Go Live:

1. Replace test keys with live keys in `.env`:
   ```env
   STRIPE_KEY=pk_live_your_live_key
   STRIPE_SECRET=sk_live_your_live_key
   ```

2. That's it! Real payments now work.

---

## 📋 What Happens When Customer Pays?

```
1. Customer clicks "PAY NOW" in PDF
   ↓
2. Redirected to secure Stripe checkout
   ↓
3. Enters card details and pays
   ↓
4. Payment processed by Stripe
   ↓
5. Redirected to success page
   ↓
6. Payment automatically recorded in your system
   ↓
7. Invoice status updated
   ↓
8. Order workflow continues (if proforma)
```

---

## 🎨 What the Payment Button Looks Like

```
┌─────────────────────────────────────────┐
│                                         │
│      💳 Pay Online with Stripe          │
│                                         │
│  Click the button below to securely    │
│  pay AED 5,000.00 online               │
│                                         │
│  ┌───────────────────────────────┐    │
│  │ ⚡ PAY NOW WITH STRIPE        │    │
│  └───────────────────────────────┘    │
│                                         │
│  Or copy this link:                    │
│  https://pay.stripe.com/...            │
│                                         │
│  ───────────────────────────────────   │
│                                         │
│  📱 Scan QR Code to Pay from Mobile    │
│                                         │
│      [QR CODE IMAGE]                   │
│                                         │
└─────────────────────────────────────────┘
```

---

## 🔧 Customization

### Change Button Colors

Edit `resources/views/admin/invoices/pdf.blade.php`:

```css
.payment-button-section {
    background: linear-gradient(135deg, #635BFF 0%, #4F46E5 100%);
    /* Change to your brand colors */
}
```

### Change Button Text

```html
<a href="{{ $invoice->stripe_payment_link_url }}" class="payment-button">
    ⚡ PAY NOW WITH STRIPE  <!-- Change this -->
</a>
```

---

## 🆘 Troubleshooting

### Payment Button Not Showing?

**Check:**
1. ✅ Stripe keys in `.env`
2. ✅ Invoice not fully paid
3. ✅ Downloaded/sent after installation

**Fix:** Re-download PDF or re-send email

### Payment Not Recording?

**Check:**
1. ✅ Check `storage/logs/laravel.log` for errors
2. ✅ Verify Stripe keys are correct
3. ✅ Test with Stripe test mode first

---

## 📊 Where to See Payments

### Admin Panel:
- **Invoices** → View Invoice → **Payments Tab**
- Shows all payments received
- Transaction references from Stripe

### Stripe Dashboard:
- [https://dashboard.stripe.com/payments](https://dashboard.stripe.com/payments)
- Real-time payment tracking
- Customer details
- Refund management

---

## 🎁 Bonus Features

### What's Included:

1. ✅ **Auto-generated payment links**
2. ✅ **Beautiful payment button in PDF**
3. ✅ **QR code for mobile payments**
4. ✅ **Automatic payment recording**
5. ✅ **Invoice status updates**
6. ✅ **Payment success page**
7. ✅ **Works with all payment terms**
8. ✅ **Handles advance payments**
9. ✅ **Shows remaining balance**
10. ✅ **Mobile-friendly checkout**

---

## 📞 Need Help?

### Resources:
- **Full Guide**: See `STRIPE_PAYMENT_INTEGRATION_GUIDE.md`
- **Stripe Docs**: [https://stripe.com/docs](https://stripe.com/docs)
- **Logs**: `storage/logs/laravel.log`

### Common Issues:

**"Payment link is null"**
- Invoice is fully paid
- Stripe keys not configured
- Check logs for errors

**"Button not clickable in PDF"**
- Some PDF viewers don't support links
- Use the text link as fallback
- Try a different PDF viewer (Adobe, Chrome, etc.)

---

## 🎊 You're Ready!

1. ✅ Stripe keys configured
2. ✅ Migration run
3. ✅ Payment button in PDFs
4. ✅ QR code included
5. ✅ Auto payment recording

**Start sending invoices with payment buttons now!** 🚀

---

**Made with ❤️ for MaxMed**

