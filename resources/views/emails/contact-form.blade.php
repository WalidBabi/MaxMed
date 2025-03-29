<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #171e60;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        <div class="content">
            <div class="field">
                <p class="label">Name:</p>
                <p>{{ $data['name'] }}</p>
            </div>
            
            <div class="field">
                <p class="label">Email:</p>
                <p>{{ $data['email'] }}</p>
            </div>
            
            <div class="field">
                <p class="label">Subject:</p>
                <p>{{ $data['subject'] }}</p>
            </div>
            
            <div class="field">
                <p class="label">Message:</p>
                <p>{{ $data['message'] }}</p>
            </div>
        </div>
    </div>
</body>
</html> 