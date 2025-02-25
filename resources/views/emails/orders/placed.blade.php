@component('mail::message')
# New Order Placed

Order Number: {{ $order->order_number }}

## Order Details:
- Total Amount: ${{ number_format($order->total_amount, 2) }}
- Status: {{ ucfirst($order->status) }}
- Shipping Address: {{ $order->shipping_address }}
- City: {{ $order->shipping_city }}
- State: {{ $order->shipping_state }}
- Zip Code: {{ $order->shipping_zipcode }}
- Phone: {{ $order->shipping_phone }}

## Items:
@foreach($order->items as $item)
- {{ $item->product->name }} ({{ $item->quantity }}x) - ${{ number_format($item->price * $item->quantity, 2) }}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent 