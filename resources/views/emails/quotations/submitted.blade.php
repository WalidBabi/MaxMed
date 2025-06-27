@component('mail::message')
# New Quotation Submitted

A new quotation has been submitted by a supplier.

**Quotation Details:**
- Reference Number: {{ $quotation->quotation_number }}
- Product: {{ $quotation->product->name ?? $quotation->product_name }}
- Unit Price: {{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}
@if($quotation->shipping_cost)
- Shipping Cost: {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }}
@endif
@if($quotation->size)
- Size/Variant: {{ $quotation->size }}
@endif

**Supplier Information:**
- Name: {{ $quotation->supplier->name }}
- Email: {{ $quotation->supplier->email }}

@if($quotation->notes)
**Additional Notes:**
{{ $quotation->notes }}
@endif

@component('mail::button', ['url' => route('admin.inquiries.show', $quotation->supplier_inquiry_id ?? $quotation->quotation_request_id)])
View Quotation Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent 