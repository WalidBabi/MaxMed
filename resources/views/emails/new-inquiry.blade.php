<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product Inquiry</title>
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
                        <td style="background-color: #059669; background: linear-gradient(135deg, #059669 0%, #10B981 100%); padding: 50px 30px; text-align: center;">
                            <!--[if gte mso 9]>
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #059669;">
                                <tr>
                                    <td style="padding: 50px 30px; text-align: center;">
                            <![endif]-->
                                        <h1 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 600; color: #FFFFFF; letter-spacing: -0.5px;">
                                            🔍 New Product Inquiry
                                        </h1>
                                        <p style="margin: 0; font-size: 18px; color: rgba(255,255,255,0.9); font-weight: 300;">MaxMed Scientific & Laboratory Equipment</p>
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
                            
                            <!-- Alert Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #ECFDF5 0%, #D1FAE5 100%); border-left: 4px solid #10B981; margin: 0 0 30px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #047857; margin-bottom: 8px; font-size: 16px;">🎯 New Business Opportunity</div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6;">A new product inquiry has been assigned to you. This is a qualified lead that requires your professional response and quotation.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Inquiry Information Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #059669; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #059669; margin-bottom: 8px; letter-spacing: -0.5px;">📋 Inquiry Details</div>
                                        </div>
    
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px; width: 120px;">Reference #</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 600; font-size: 15px;">{{ $inquiry->reference_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Product Name</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 600; font-size: 15px;">
                                                    @if($inquiry->product)
                                                        {{ $inquiry->product->name }}
                                                    @else
                                                        {{ $inquiry->product_name }}
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($inquiry->product && $inquiry->product->category)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Category</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $inquiry->product->category->name }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Inquiry Date</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $inquiry->created_at->format('M j, Y \a\t g:i A T') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Request Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #FEF3C7; background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #F59E0B; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #D97706; margin-bottom: 8px; letter-spacing: -0.5px;">📦 Product Requirements</div>
                                        </div>

                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px; width: 150px;">Quantity Required</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 700; font-size: 18px;">{{ number_format($inquiry->quantity) }} units</td>
                                            </tr>
                                            @if($inquiry->requirements)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px;">Special Requirements</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $inquiry->requirements }}</td>
                                            </tr>
                                            @endif
                                            @if($inquiry->notes)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px;">Additional Notes</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $inquiry->notes }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #92400E; font-size: 15px;">Response Deadline</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    @if($inquiry->expires_at)
                                                        {{ $inquiry->expires_at->format('M j, Y \a\t g:i A T') }}
                                                    @else
                                                        <span style="color: #DC2626; font-weight: 600;">🚨 Please respond within 7 days</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Action Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 40px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                            <tr>
                                                <td style="background: linear-gradient(135deg, #059669 0%, #10B981 100%); border-radius: 8px; padding: 0;">
                                                    <a href="{{ $actionUrl }}" style="display: inline-block; padding: 16px 32px; color: #FFFFFF; text-decoration: none; font-weight: 600; font-size: 16px; border-radius: 8px;">
                                                        📝 View Inquiry & Submit Quotation
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Instructions Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #F8FAFC; border-radius: 8px; margin: 30px 0; border-left: 4px solid #6366F1;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="font-size: 18px; font-weight: 700; color: #6366F1; margin-bottom: 15px;">📋 Next Steps</div>
                                        <ol style="margin: 0; padding-left: 20px; color: #4A5568; line-height: 1.8;">
                                            <li style="margin-bottom: 8px;">Click the button above to view the complete inquiry details</li>
                                            <li style="margin-bottom: 8px;">Review the product specifications and requirements</li>
                                            <li style="margin-bottom: 8px;">Prepare your quotation with competitive pricing</li>
                                            <li style="margin-bottom: 8px;">Submit your quotation through the supplier portal</li>
                                            <li style="margin-bottom: 8px;">If the product is not available, mark it as "Not Available" with a reason</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Footer -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin-top: 40px;">
                                <tr>
                                    <td style="text-align: center; padding: 20px; border-top: 1px solid #E2E8F0;">
                                        <p style="margin: 0 0 10px 0; color: #718096; font-size: 14px;">
                                            <strong>MaxMed Scientific & Laboratory Equipment</strong><br>
                                            Your trusted partner in laboratory solutions
                                        </p>
                                        <p style="margin: 0; color: #A0AEC0; font-size: 12px;">
                                            This is an automated notification. Please do not reply to this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 