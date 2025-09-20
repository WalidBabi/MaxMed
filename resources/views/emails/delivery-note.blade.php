<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note {{ $delivery->delivery_number }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
                <o:AllowPNG/>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset styles */
        body, table, td, p, a, li, blockquote {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        img {
            -ms-interpolation-mode: bicubic;
        }
        
        /* Base styles that work in Outlook */
        body {
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #2D3748;
            background-color: #F7FAFC;
        }
        
        table {
            border-collapse: collapse;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }
        
        .email-wrapper {
            width: 100% !important;
            background-color: #F7FAFC;
        }
        
        .email-container {
            width: 650px;
            margin: 0 auto;
            background-color: #FFFFFF;
        }
        
        /* Logo styles */
        .email-logo {
            width: 380px;
            height: 97px;
            max-width: 100%;
            display: block;
            margin: 0 auto;
        }
        
        /* Media queries for mobile (will be ignored by Outlook) */
        @media only screen and (max-width: 650px) {
            .email-container {
                width: 100% !important;
                margin: 0 !important;
            }
            
            .mobile-padding {
                padding: 20px !important;
            }
            .mobile-text-center {
                text-align: center !important;
            }
        }
    </style>
</head>
<body>
    <table role="presentation" class="email-wrapper" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" class="email-container" cellpadding="0" cellspacing="0" border="0">
                    <!-- Header with Logo -->
                    <tr>
                        <td style="padding: 40px 30px 30px 30px; text-align: center; background: linear-gradient(135deg, #171e60 0%, #0a5694 100%);">
                            <img src="{{ asset('Images/logo.png') }}" alt="MaxMed UAE" class="email-logo" style="width: 380px; height: 97px; max-width: 100%; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 30px;">
                            <h1 style="color: #171e60; font-size: 28px; margin: 0 0 20px 0; text-align: center; font-weight: 600;">
                                Delivery Confirmation
                            </h1>
                            
                            <div style="background-color: #F0FDF4; border-left: 4px solid #22C55E; padding: 20px; margin: 20px 0; border-radius: 6px;">
                                <h2 style="color: #166534; font-size: 18px; margin: 0 0 10px 0; font-weight: 600;">
                                    ✅ Your Order Has Been Delivered Successfully
                                </h2>
                                <p style="color: #166534; margin: 0; font-size: 16px;">
                                    Delivery Note: <strong>{{ $delivery->delivery_number }}</strong>
                                </p>
                            </div>

                            <p style="margin: 20px 0; font-size: 16px; line-height: 1.6;">
                                Dear Valued Customer,
                            </p>

                            <p style="margin: 20px 0; font-size: 16px; line-height: 1.6;">
                                We are pleased to confirm that your order has been successfully delivered and signed for on 
                                <strong>{{ formatDubaiDate($delivery->delivered_at, 'd M Y \a\t g:i A') }}</strong>.
                            </p>

                            @if($customMessage)
                                <div style="background-color: #F8FAFC; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                    <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5568;">
                                        {{ $customMessage }}
                                    </p>
                                </div>
                            @endif

                            <!-- Delivery Details -->
                            <div style="background-color: #F8FAFC; border-radius: 8px; padding: 25px; margin: 30px 0;">
                                <h3 style="color: #2D3748; font-size: 18px; margin: 0 0 20px 0; font-weight: 600;">
                                    📦 Delivery Details
                                </h3>
                                
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: 600; color: #4A5568; width: 40%;">
                                            Delivery Number:
                                        </td>
                                        <td style="padding: 8px 0; color: #2D3748;">
                                            {{ $delivery->delivery_number }}
                                        </td>
                                    </tr>
                                    @if($delivery->tracking_number)
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: 600; color: #4A5568;">
                                            Tracking Number:
                                        </td>
                                        <td style="padding: 8px 0; color: #2D3748;">
                                            {{ $delivery->tracking_number }}
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: 600; color: #4A5568;">
                                            Delivery Date:
                                        </td>
                                        <td style="padding: 8px 0; color: #2D3748;">
                                            {{ formatDubaiDate($delivery->delivered_at, 'd M Y \a\t g:i A') }}
                                        </td>
                                    </tr>
                                    @if($delivery->carrier)
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: 600; color: #4A5568;">
                                            Carrier:
                                        </td>
                                        <td style="padding: 8px 0; color: #2D3748;">
                                            {{ $delivery->carrier }}
                                        </td>
                                    </tr>
                                    @endif
                                    @if($delivery->order)
                                    <tr>
                                        <td style="padding: 8px 0; font-weight: 600; color: #4A5568;">
                                            Order Number:
                                        </td>
                                        <td style="padding: 8px 0; color: #2D3748;">
                                            {{ $delivery->order->order_number }}
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <!-- Delivery Address -->
                            @if($delivery->shipping_address)
                            <div style="background-color: #F8FAFC; border-radius: 8px; padding: 25px; margin: 30px 0;">
                                <h3 style="color: #2D3748; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
                                    📍 Delivery Address
                                </h3>
                                <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #4A5568;">
                                    {{ $delivery->shipping_address }}
                                </p>
                            </div>
                            @endif

                            <!-- Signature Confirmation -->
                            @if($delivery->signed_at)
                            <div style="background-color: #F0FDF4; border-radius: 8px; padding: 25px; margin: 30px 0;">
                                <h3 style="color: #166534; font-size: 18px; margin: 0 0 15px 0; font-weight: 600;">
                                    ✍️ Signature Confirmation
                                </h3>
                                <p style="margin: 0 0 10px 0; font-size: 16px; line-height: 1.6; color: #166534;">
                                    <strong>Signed at:</strong> {{ formatDubaiDate($delivery->signed_at, 'd M Y \a\t g:i A') }}
                                </p>
                                @if($delivery->delivery_conditions && is_array($delivery->delivery_conditions))
                                <p style="margin: 10px 0 0 0; font-size: 16px; line-height: 1.6; color: #166534;">
                                    <strong>Delivery Conditions:</strong> {{ implode(', ', $delivery->delivery_conditions) }}
                                </p>
                                @endif
                            </div>
                            @endif

                            <!-- Call to Action -->
                            <div style="text-align: center; margin: 40px 0;">
                                <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6;">
                                    Please find the detailed delivery note attached to this email for your records.
                                </p>
                                
                                <a href="tel:+971554602500" 
                                   style="display: inline-block; background: linear-gradient(135deg, #171e60 0%, #0a5694 100%); color: #FFFFFF; text-decoration: none; padding: 15px 30px; border-radius: 8px; font-weight: 600; font-size: 16px; margin: 10px;">
                                    📞 Contact Support
                                </a>
                            </div>

                            <!-- Thank You Message -->
                            <div style="background-color: #171e60; color: #FFFFFF; padding: 30px; border-radius: 8px; text-align: center; margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600;">
                                    Thank You for Choosing MaxMed UAE! 🙏
                                </h3>
                                <p style="margin: 0; font-size: 16px; line-height: 1.6; opacity: 0.9;">
                                    We appreciate your business and trust in our laboratory equipment solutions. 
                                    If you have any questions or need support, please don't hesitate to contact us.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #F8FAFC; padding: 30px; text-align: center; border-top: 1px solid #E2E8F0;">
                            <div style="margin-bottom: 20px;">
                                <h4 style="color: #2D3748; font-size: 16px; margin: 0 0 10px 0; font-weight: 600;">
                                    Contact Information
                                </h4>
                                <p style="margin: 5px 0; font-size: 14px; color: #4A5568;">
                                    📞 <a href="tel:+971554602500" style="color: #171e60; text-decoration: none;">+971 55 460 2500</a>
                                </p>
                                <p style="margin: 5px 0; font-size: 14px; color: #4A5568;">
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: #171e60; text-decoration: none;">sales@maxmedme.com</a>
                                </p>
                                <p style="margin: 5px 0; font-size: 14px; color: #4A5568;">
                                    🌐 <a href="https://maxmedme.com" style="color: #171e60; text-decoration: none;">www.maxmedme.com</a>
                                </p>
                            </div>
                            
                            <p style="margin: 20px 0 0 0; font-size: 12px; color: #718096; line-height: 1.4;">
                                This email was sent automatically after your delivery confirmation. 
                                Please keep this delivery note for your records.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
