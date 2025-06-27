<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Quotation Submitted</title>
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
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">New Quotation Submitted</h1>
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
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">New Quotation Received</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">A supplier has submitted a quotation for your product inquiry. Please review the details below and take appropriate action.</p>
                            </div>

                            <!-- Quotation Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <h3 style="margin: 0; color: #171e60; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Quotation Details</h3>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Inquiry Reference</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    @if($inquiry instanceof \App\Models\SupplierInquiry)
                                                        {{ $inquiry->reference_number ?? 'INQ-' . str_pad($inquiry->id, 6, '0', STR_PAD_LEFT) }}
                                                    @else
                                                        QR-{{ str_pad($inquiry->id, 6, '0', STR_PAD_LEFT) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Product</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    {{ $inquiry->product ? $inquiry->product->name : ($inquiry->product_name ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Supplier</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->supplier->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Unit Price</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 600; font-size: 15px;">
                                                    {{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}
                                                </td>
                                            </tr>
                                            @if($quotation->shipping_cost)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Shipping Cost</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if($quotation->size)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Size/Specifications</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->size }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Quotation Number</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 600; font-size: 15px;">{{ $quotation->quotation_number }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($quotation->notes)
                            <!-- Supplier Notes Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px; background-color: #F0FFF4; border-radius: 8px; border-left: 3px solid #48BB78;">
                                        <p style="margin: 0; font-size: 16px; color: #2D3748;"><span style="font-weight: 600; color: #48BB78;">Supplier Notes:</span> {{ $quotation->notes }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Total Amount - Outlook Compatible -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <!--[if gte mso 9]>
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60;">
                                            <tr>
                                                <td style="padding: 30px; text-align: center;">
                                        <![endif]-->
                                                    <div style="color: #FFFFFF;">
                                                        <div style="font-size: 16px; margin-bottom: 5px; opacity: 0.9; font-weight: 300; text-transform: uppercase; letter-spacing: 1px;">Quoted Amount</div>
                                                        <div style="font-size: 36px; font-weight: 700; margin: 0; letter-spacing: -1px;">{{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}</div>
                                                        @if($quotation->shipping_cost)
                                                        <div style="font-size: 14px; margin-top: 5px; opacity: 0.8;">+ {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }} shipping</div>
                                                        @endif
                                                    </div>
                                        <!--[if gte mso 9]>
                                                </td>
                                            </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Button -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                            <tr>
                                                <td style="background-color: #171e60; border-radius: 8px; text-align: center;">
                                                    <a href="{{ $url }}" style="display: inline-block; padding: 15px 40px; color: #FFFFFF; text-decoration: none; font-weight: 600; font-size: 18px; letter-spacing: 0.5px;">
                                                        Review Quotation →
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Messages -->
                            <div style="margin: 30px 0;">
                                <p style="margin: 0 0 15px 0; color: #4A5568; font-size: 16px; line-height: 1.7;">Please review the quotation details and take appropriate action. Contact the supplier if you need any clarifications or modifications.</p>
                                <p style="margin: 0; color: #2D3748; font-size: 16px; font-weight: 500;">Prompt response helps maintain good supplier relationships! 🤝</p>
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
                                This is an automated notification from MaxMed's quotation management system.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 