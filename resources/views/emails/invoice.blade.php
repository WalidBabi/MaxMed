<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice' }} {{ $invoice->invoice_number }}</title>
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
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">
                                            {{ $invoice->type === 'proforma' ? '📋 Proforma Invoice' : '📄 Invoice' }}
                                        </h1>
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
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">Hello {{ $invoice->customer_name }},</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">
                                    @if($invoice->type === 'proforma')
                                        We've prepared your proforma invoice for the requested products/services. Please review the details below.
                                    @else
                                        Your final invoice is ready! Please find the details below for your delivered products/services.
                                    @endif
                                </p>
                                
                                @if($customMessage)
                                <!-- Custom Message -->
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #EBF8FF 0%, #BEE3F8 100%); border-left: 4px solid #3182CE; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                    <tr>
                                        <td style="padding: 20px;">
                                            <div style="font-weight: 600; color: #2B6CB0; margin-bottom: 8px; font-size: 16px;">💬 Additional Message</div>
                                            <p style="margin: 0; color: #2D3748; line-height: 1.6;">{{ $customMessage }}</p>
                                        </td>
                                    </tr>
                                </table>
                                @endif
                            </div>

                            <!-- Invoice Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }}; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 28px; font-weight: 700; color: {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }}; margin-bottom: 8px; letter-spacing: -0.5px;">{{ $invoice->invoice_number }}</div>
                                            <div style="font-size: 14px; color: #718096; text-transform: uppercase; letter-spacing: 1px; font-weight: 500;">{{ $invoice->type === 'proforma' ? 'Proforma Invoice' : 'Final Invoice' }}</div>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Invoice Date</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ formatDubaiDate($invoice->invoice_date, 'M j, Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Due Date</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ formatDubaiDate($invoice->due_date, 'M j, Y') }}</td>
                                            </tr>
                                            @if($invoice->reference_number)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Reference</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $invoice->reference_number }}</td>
                                            </tr>
                                            @endif
                                            @if($invoice->po_number)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">PO Number</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $invoice->po_number }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Payment Terms</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $invoice::PAYMENT_TERMS[$invoice->payment_terms] ?? ucfirst($invoice->payment_terms) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Total Amount - Outlook Compatible -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }}; background: linear-gradient(135deg, {{ $invoice->type === 'proforma' ? '#38B2AC 0%, #319795 100%' : '#48BB78 0%, #38A169 100%' }}); margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <!--[if gte mso 9]>
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }};">
                                            <tr>
                                                <td style="padding: 30px; text-align: center;">
                                        <![endif]-->
                                                    <div style="color: #FFFFFF;">
                                                        <div style="font-size: 16px; margin-bottom: 5px; opacity: 0.9; font-weight: 300; text-transform: uppercase; letter-spacing: 1px;">Total Amount</div>
                                                        <div style="font-size: 36px; font-weight: 700; margin: 0; letter-spacing: -1px;">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}</div>
                                                    </div>
                                        <!--[if gte mso 9]>
                                                </td>
                                            </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>

                            @if($invoice->type === 'proforma' && ($invoice->requires_advance_payment || $invoice->payment_terms === 'on_delivery'))
                            <!-- Payment Terms -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border: 1px solid #F59E0B; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        @if($invoice->payment_terms !== 'on_delivery')
                                        <div style="display: inline-block; background-color: #F59E0B; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">💳 PAYMENT REQUIRED</div>
                                        @else
                                        <div style="display: inline-block; background-color: #3B82F6; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">📦 DELIVERY INFORMATION</div>
                                        @endif
                                        <h4 style="color: #92400E; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Payment Information</h4>
                                        <p style="margin: 0 0 15px 0; color: #451A03; line-height: 1.6;">
                                            @if($invoice->payment_terms === 'advance_50')
                                                This proforma invoice requires <strong>50% advance payment</strong> ({{ number_format($invoice->getAdvanceAmount(), 2) }} {{ $invoice->currency }}) before we proceed with your order.
                                            @elseif($invoice->payment_terms === 'advance_100')
                                                This proforma invoice requires <strong>full payment</strong> ({{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency }}) before we proceed with your order.
                                            @elseif($invoice->payment_terms === 'on_delivery')
                                                This proforma invoice is for <strong>payment on delivery</strong>. No advance payment is required.
                                            @else
                                                This proforma invoice requires <strong>{{ $invoice->advance_percentage }}% advance payment</strong> ({{ number_format($invoice->getAdvanceAmount(), 2) }} {{ $invoice->currency }}) before we proceed with your order.
                                            @endif
                                        </p>
                                        <p style="margin: 0; color: #451A03; font-size: 14px; opacity: 0.8;">
                                            @if($invoice->payment_terms === 'on_delivery')
                                                ✅ We will confirm your order and arrange delivery. Payment will be collected upon delivery.
                                            @else
                                                ✅ Once payment is received, we will confirm your order and provide you with a delivery timeline.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            @if($invoice->type === 'final')
                            <!-- Final Invoice -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #F0FFF4 0%, #C6F6D5 100%); border: 1px solid #48BB78; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="display: inline-block; background-color: #48BB78; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">✅ Order Ready</div>
                                        <h4 style="color: #22543D; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Final Invoice</h4>
                                        <p style="margin: 0 0 15px 0; color: #1A365D; line-height: 1.6;">Your order has been processed and is ready for delivery. Please arrange payment for the remaining amount if applicable.</p>
                                        @if($invoice->parentInvoice && $invoice->parentInvoice->paid_amount > 0)
                                            <p style="margin: 0; color: #1A365D; font-size: 14px;">💰 Previous advance payment received: <strong>{{ number_format($invoice->parentInvoice->paid_amount, 2) }} {{ $invoice->currency }}</strong></p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Message -->
                            <div style="margin: 30px 0;">
                                <p style="margin: 0 0 15px 0; color: #4A5568; font-size: 16px; line-height: 1.7;">📎 The detailed invoice is attached as a PDF document.</p>
                                <p style="margin: 0; color: #2D3748; font-size: 16px; font-weight: 500;">If you have any questions about this invoice, please don't hesitate to contact us. We're here to help! 💬</p>
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
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }}; text-decoration: none;">sales@maxmedme.com</a> | 
                                    🌐 <a href="http://www.maxmedme.com" style="color: {{ $invoice->type === 'proforma' ? '#38B2AC' : '#48BB78' }}; text-decoration: none;">www.maxmedme.com</a>
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