<!DOCTYPE html>
<html>
<body>
    <h2>New Quotation Request</h2>
    
    <h3>Product Details:</h3>
    <p>Product Name: {{ $product->name }}</p>
    <p>Product ID: {{ $product->id }}</p>

    <h3>Request Details:</h3>
    <p>Quantity: {{ $quantity }}</p>
    <p>Delivery Timeline: {{ $delivery_timeline }}</p>
    
    @if($requirements)
    <p>Specific Requirements: {{ $requirements }}</p>
    @endif
    
    @if($notes)
    <p>Additional Notes: {{ $notes }}</p>
    @endif

    <h3>Customer Information:</h3>
    <p>Name: {{ $user->name }}</p>
    <p>Email: {{ $user->email }}</p>

    <p>Please review and respond to the customer with a quotation.</p>
</body>
</html> 