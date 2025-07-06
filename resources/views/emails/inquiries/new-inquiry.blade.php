<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product Inquiry - {{ $inquiry->reference_number }}</title>
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
            }
            .mobile-padding {
                padding: 20px !important;
            }
            .mobile-text-center {
                text-align: center !important;
            }
            .email-logo {
                width: 280px !important;
                height: 71px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 16px; line-height: 1.6; color: #2D3748; background-color: #F7FAFC;">
    <table class="email-wrapper" cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #F7FAFC;">
        <tr>
            <td align="center" style="padding: 30px 20px;">
                <table class="email-container" cellpadding="0" cellspacing="0" border="0" style="width: 650px; background-color: #FFFFFF; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08);">
                    
                    <!-- Modern Header Section -->
                    <tr>
                        <td style="background-color: #FFFFFF; padding: 40px 30px; text-align: center; border-bottom: 2px solid #171e60;">
                            <!--[if gte mso 9]>
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #FFFFFF;">
                                <tr>
                                    <td style="padding: 40px 30px; text-align: center;">
                            <![endif]-->
                                        <!-- MaxMed Logo -->
                                        <div style="margin-bottom: 30px;">
                                            <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" width="380" height="97" class="email-logo" style="width: 380px; height: 97px; max-width: 100%; display: block; margin: 0 auto;">
                                        </div>
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">New Product Inquiry</h1>
                                        <p style="margin: 0; font-size: 16px; color: #0a5694; font-weight: 400;">MaxMed Scientific & Laboratory Equipment</p>
                            <!--[if gte mso 9]>
                                    </td>
                                </tr>
                            </table>
                            <![endif]-->
                        </td>
                    </tr>
                    
                    <!-- Content Section -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 30px;">
                            
                            <!-- Greeting -->
                            <div style="margin-bottom: 30px;">
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">Dear Supplier,</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">We have received a new product inquiry that may match your expertise. Your assistance in providing relevant products would be greatly appreciated.</p>
                            </div>

                            <!-- Inquiry Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <h3 style="margin: 0; color: #171e60; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Inquiry Details</h3>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Inquiry Number</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $inquiry->reference_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Created Date</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ formatDubaiDate($inquiry->created_at, 'M j, Y \a\t g:i A') }}</td>
                                            </tr>

                                            @if($inquiry->customer_email)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Customer Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $inquiry->customer_email }}</td>
                                            </tr>
                                            @endif
                                            @if($inquiry->company)
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Company</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $inquiry->company }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($inquiry->product_name)
                            <!-- Product Information -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px; background-color: #F7FAFC; border-radius: 8px; border-left: 3px solid #48BB78;">
                                        <p style="margin: 0; font-size: 16px; color: #2D3748;"><span style="font-weight: 600; color: #48BB78;">Product Requested:</span> {{ $inquiry->product_name }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            @if($inquiry->description)
                            <!-- Description Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px; background-color: #F0FFF4; border-radius: 8px; border-left: 3px solid #48BB78;">
                                        <h4 style="margin: 0 0 10px 0; color: #2F855A; font-size: 16px; font-weight: 600;">Description:</h4>
                                        <div style="color: #2F855A; font-size: 16px; line-height: 1.6;">{{ $inquiry->description }}</div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            @if($inquiry->quantity)
                            <!-- Quantity Information -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EBF8FF; margin: 25px 0; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="font-size: 16px; color: #2B6CB0; font-weight: 600;">
                                            📦 Requested Quantity: {{ $inquiry->quantity }} {{ $inquiry->unit ?? 'units' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Call to Action -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <!--[if gte mso 9]>
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60;">
                                            <tr>
                                                <td style="padding: 30px; text-align: center;">
                                        <![endif]-->
                                                    <div style="color: #FFFFFF; margin-bottom: 20px;">
                                                        <div style="font-size: 18px; margin-bottom: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Ready to Respond?</div>
                                                        <div style="font-size: 14px; opacity: 0.9;">Click below to view full details and submit your proposal</div>
                                                    </div>
                                                    
                                                    <!-- CTA Button -->
                                                    <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                                        <tr>
                                                            <td style="background-color: #48BB78; border-radius: 8px; padding: 16px 32px;">
                                                                <a href="{{ route('supplier.inquiries.show', $inquiry->id) }}" style="display: inline-block; color: #FFFFFF; text-decoration: none; font-weight: 600; font-size: 16px; letter-spacing: 0.5px;">
                                                                    VIEW INQUIRY & RESPOND
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </table>
                                        <!--[if gte mso 9]>
                                                </td>
                                            </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>

                            @if($inquiry->deadline)
                            <!-- Deadline Notice -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #FFFBEB; border: 1px solid #F59E0B; margin: 25px 0; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px; text-align: center;">
                                        <div style="color: #D97706; font-weight: 600; font-size: 15px;">
                                            ⏰ Response deadline: {{ formatDubaiDate($inquiry->deadline, 'M j, Y \a\t g:i A') }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Instructions -->
                            <div style="margin: 30px 0;">
                                <p style="margin: 0 0 15px 0; color: #4A5568; font-size: 16px; line-height: 1.7;">Please review the inquiry details and submit your best proposal. If you have any questions or need clarification, don't hesitate to contact our team.</p>
                                <p style="margin: 0; color: #2D3748; font-size: 16px; font-weight: 500;">Thank you for your continued partnership! 🤝</p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Modern Footer Section -->
                    <tr>
                        <td style="background-color: #1A202C; padding: 30px; text-align: center; color: #A0AEC0;">
                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 600; color: #FFFFFF; margin-bottom: 8px; font-size: 18px;">MaxMed Scientific and Laboratory Equipment Trading Co. LLC</div>
                                <div style="font-size: 14px; margin-bottom: 5px;">📍 Dubai 448945, United Arab Emirates</div>
                                <div style="font-size: 14px;">
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: #667eea; text-decoration: none;">sales@maxmedme.com</a> | 
                                    🌐 <a href="http://www.maxmedme.com" style="color: #667eea; text-decoration: none;">www.maxmedme.com</a>
                                </div>
                            </div>
                            <div style="font-size: 12px; opacity: 0.7; border-top: 1px solid #2D3748; padding-top: 15px; margin-top: 15px;">
                                This is an automated email. Please do not reply directly to this message.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 