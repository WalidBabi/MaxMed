<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
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
            padding: 20px 0;
        }
        
        .email-container {
            max-width: 640px !important;
            margin: 0 auto;
            background-color: #FFFFFF;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        .email-logo {
            max-width: 100% !important;
            height: auto !important;
        }
        
        @media only screen and (max-width: 640px) {
            .mobile-padding {
                padding: 20px !important;
            }
            .email-container {
                width: 100% !important;
                margin: 0 10px !important;
                max-width: none !important;
            }
            .mobile-text-center {
                text-align: center !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #F7FAFC; margin: 0; padding: 0;">
            <tr>
                <td style="text-align: center; padding: 20px 10px;">
                    
                    <!-- Main Email Container -->
                    <table cellpadding="0" cellspacing="0" border="0" class="email-container" style="width: 640px; margin: 0 auto; background-color: #FFFFFF; border-radius: 12px; overflow: hidden;">
                        
                        <!-- Header Section -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #171e60 0%, #0a5694 100%); padding: 40px 30px 50px; text-align: center; color: #FFFFFF;">
                                <!--[if gte mso 9]>
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60;">
                                    <tr>
                                        <td style="padding: 40px 30px 50px; text-align: center; color: #FFFFFF;">
                                <![endif]-->
                                            <!-- MaxMed Logo -->
                                            <div style="margin-bottom: 30px;">
                                                <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" width="380" height="97" class="email-logo" style="width: 380px; height: 97px; max-width: 100%; display: block; margin: 0 auto;">
                                            </div>
                                            <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #FFFFFF; letter-spacing: -0.5px;">Purchase Order {{ $purchaseOrder->po_number }}</h1>
                                            <p style="margin: 0; font-size: 16px; color: #E2E8F0; font-weight: 400;">MaxMed Scientific & Laboratory Equipment</p>
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
                                    <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">Hello {{ $supplierName }},</h2>
                                    <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">We are pleased to send you this purchase order for your products and services. Please review the details and confirm receipt.</p>
                                </div>

                                <!-- Purchase Order Details Card -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                    <tr>
                                        <td style="padding: 25px;">
                                            <div style="margin-bottom: 20px;">
                                                <h3 style="margin: 0; color: #171e60; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Purchase Order Details</h3>
                                            </div>
                                            
                                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">PO Number</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $purchaseOrder->po_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">PO Date</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ formatDubaiDate($purchaseOrder->po_date, 'M j, Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Requested Delivery</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ formatDubaiDate($purchaseOrder->requested_delivery_date, 'M j, Y') }}</td>
                                                </tr>
                                                @if($purchaseOrder->payment_terms)
                                                <tr>
                                                    <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Payment Terms</td>
                                                    <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $purchaseOrder->payment_terms }}</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </td>
                                    </tr>
                                </table>

                                @if($purchaseOrder->notes)
                                <!-- Notes Section -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                    <tr>
                                        <td style="padding: 20px; background-color: #F7FAFC; border-radius: 8px; border-left: 3px solid #48BB78;">
                                            <p style="margin: 0; font-size: 16px; color: #2D3748;"><span style="font-weight: 600; color: #48BB78;">Notes:</span> {{ $purchaseOrder->notes }}</p>
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                <!-- Items Summary -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EBF8FF; margin: 25px 0; border-radius: 8px;">
                                    <tr>
                                        <td style="padding: 20px; text-align: center;">
                                            <div style="font-size: 16px; color: #2B6CB0; font-weight: 600;">
                                                📋 {{ $purchaseOrder->items->count() }} {{ $purchaseOrder->items->count() === 1 ? 'Item' : 'Items' }} Ordered
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Total Amount -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                    <tr>
                                        <td style="padding: 30px; text-align: center;">
                                            <div style="color: #FFFFFF;">
                                                <div style="font-size: 16px; margin-bottom: 5px; opacity: 0.9; font-weight: 300; text-transform: uppercase; letter-spacing: 1px;">Total Amount</div>
                                                <div style="font-size: 36px; font-weight: 700; margin: 0; letter-spacing: -1px;">AED {{ number_format($purchaseOrder->total_amount, 2) }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Important Information -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #FFF5F5; margin: 30px 0; border-radius: 8px; border-left: 3px solid #F56565;">
                                    <tr>
                                        <td style="padding: 20px;">
                                            <h4 style="margin: 0 0 15px 0; color: #C53030; font-size: 16px; font-weight: 600;">📋 Next Steps:</h4>
                                            <ul style="margin: 0; padding-left: 20px; color: #2D3748; font-size: 15px; line-height: 1.6;">
                                                <li style="margin-bottom: 8px;">Please confirm receipt of this purchase order</li>
                                                <li style="margin-bottom: 8px;">Review all items and specifications carefully</li>
                                                <li style="margin-bottom: 8px;">Provide delivery timeline confirmation</li>
                                                <li>Contact us if you have any questions or concerns</li>
                                            </ul>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Contact Information -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #F7FAFC; margin: 30px 0; border-radius: 8px;">
                                    <tr>
                                        <td style="padding: 25px; text-align: center;">
                                            <h4 style="margin: 0 0 15px 0; color: #171e60; font-size: 18px; font-weight: 600;">Need Assistance?</h4>
                                            <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 15px;">Our procurement team is here to help you with any questions.</p>
                                            <div style="margin-top: 15px;">
                                                <a href="mailto:sales@maxmedme.com" style="color: #171e60; text-decoration: none; font-weight: 600; font-size: 16px;">📧 sales@maxmedme.com</a>
                                            </div>
                                            <div style="margin-top: 8px;">
                                                <span style="color: #4A5568; font-size: 15px;">📞 +971 4 XXX XXXX</span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Thank You Message -->
                                <div style="text-align: center; margin: 40px 0 20px 0;">
                                    <h3 style="margin: 0 0 12px 0; color: #171e60; font-size: 20px; font-weight: 600;">Thank you for your partnership!</h3>
                                    <p style="margin: 0; color: #4A5568; font-size: 16px;">We value our business relationship and look forward to working with you.</p>
                                </div>

                            </td>
                        </tr>
                        
                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #2D3748; padding: 30px; text-align: center;">
                                <div style="margin-bottom: 15px;">
                                    <img src="{{ asset('Images/logo.png') }}" alt="MaxMed Logo" width="120" height="30" style="width: 120px; height: 30px; max-width: 100%; opacity: 0.8;">
                                </div>
                                <p style="margin: 0 0 8px 0; color: #A0AEC0; font-size: 14px;">MaxMed Scientific & Laboratory Equipment</p>
                                <p style="margin: 0 0 15px 0; color: #A0AEC0; font-size: 13px;">Leading supplier of laboratory and scientific equipment in the UAE</p>
                                
                                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #4A5568;">
                                    <p style="margin: 0; color: #718096; font-size: 12px;">
                                        This is an automated message. Please do not reply to this email.<br>
                                        For questions, contact us at sales@maxmedme.com
                                    </p>
                                </div>
                            </td>
                        </tr>
                        
                    </table>
                    
                </td>
            </tr>
        </table>
    </div>
</body>
</html> 