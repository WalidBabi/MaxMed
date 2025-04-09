<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            border-bottom: 3px solid #007bff;
            margin-bottom: 20px;
        }
        h2 {
            color: #007bff;
            margin-top: 0;
        }
        h3 {
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-top: 25px;
        }
        .section {
            margin-bottom: 20px;
            padding: 0 10px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 14px;
            color: #666;
        }
        .highlight {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Quotation Request</h2>
    </div>
    
    <div class="section">
        <h3>Product Details</h3>
        <p><span class="highlight">Product Name:</span> {{ $product->name }}</p>
        <p><span class="highlight">Product ID:</span> {{ $product->id }}</p>
    </div>

    <div class="section">
        <h3>Request Details</h3>
        <p><span class="highlight">Quantity:</span> {{ $quantity }}</p>

        @if($requirements)
        <p><span class="highlight">Specific Requirements:</span> {{ $requirements }}</p>
        @endif
        
        @if($notes)
        <p><span class="highlight">Additional Notes:</span> {{ $notes }}</p>
        @endif
    </div>

    <div class="section">
        <h3>Customer Information</h3>
        @if(isset($user) && $user)
            <p><span class="highlight">Name:</span> {{ $user->name }}</p>
            <p><span class="highlight">Email:</span> {{ $user->email }}</p>
        @else
            <p><span class="highlight">Customer:</span> Guest User</p>
        @endif
    </div>

    <div class="footer">
        <p>Please review and respond to the customer with a quotation as soon as possible.</p>
        <p>This is an automated notification from your quotation system.</p>
    </div>
</body>
</html> 