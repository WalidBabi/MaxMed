<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Quotation Submitted - Admin Notification</title>
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
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #dc2626; letter-spacing: -0.5px;">⚠️ Admin Alert: New Quotation</h1>
                                        <p style="margin: 0; font-size: 16px; color: #0a5694; font-weight: 400;">Action Required - Review & Approve</p>
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
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #dc2626;">🔔 New Supplier Quotation</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">A supplier has submitted a new quotation that requires your review and approval. Please review the details below and take appropriate action.</p>
                            </div>

                            <!-- Quotation Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #fef3f2; margin: 30px 0; border-radius: 8px; border-left: 4px solid #dc2626; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <h3 style="margin: 0; color: #dc2626; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">📋 Quotation Details</h3>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Inquiry Reference</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    @if($inquiry instanceof \App\Models\SupplierInquiry)
                                                        {{ $inquiry->reference_number ?? 'INQ-' . str_pad($inquiry->id, 6, '0', STR_PAD_LEFT) }}
                                                    @else
                                                        QR-{{ str_pad($inquiry->id, 6, '0', STR_PAD_LEFT) }}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Product</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    {{ $inquiry->product ? $inquiry->product->name : ($inquiry->product_name ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Supplier</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->supplier->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Supplier Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->supplier->email }}</td>
                                            </tr>
                                            @php
                                                // Check if this is a PDF-only quotation
                                                $hasAttachments = $quotation->attachments && is_array($quotation->attachments) && count($quotation->attachments) > 0;
                                                $hasProductInfo = ($quotation->product_id && $quotation->product && $quotation->product->name) || 
                                                                 ($quotation->inquiry && $quotation->inquiry->product_name) || 
                                                                 ($quotation->inquiry && $quotation->inquiry->product_description);
                                                $isPdfOnly = $hasAttachments && !$hasProductInfo && $quotation->unit_price == 0;
                                            @endphp
                                            
                                            @if(!$isPdfOnly)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Unit Price</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 600; font-size: 15px;">
                                                    {{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if($quotation->shipping_cost && !$isPdfOnly)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Shipping Cost</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    {{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if($quotation->size && !$isPdfOnly)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Size/Specifications</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->size }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; font-weight: 600; color: #4A5568; font-size: 15px;">Quotation Number</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #fecaca; color: #1A202C; text-align: right; font-weight: 600; font-size: 15px;">{{ $quotation->quotation_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Submitted At</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $quotation->created_at->format('M d, Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if($isPdfOnly)
                            <!-- PDF-Only Quotation Notice -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px; background-color: #fef3c7; border-radius: 8px; border-left: 3px solid #f59e0b;">
                                        <p style="margin: 0; font-size: 16px; color: #92400e;"><span style="font-weight: 600; color: #d97706;">📄 PDF-Only Quotation:</span> This quotation contains uploaded documents with pricing details. Please review the attached files for complete pricing information.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Uploaded Files Section -->
                            @if($quotation->attachments && is_array($quotation->attachments) && count($quotation->attachments) > 0)
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f0f9ff; margin: 25px 0; border-radius: 8px; border-left: 3px solid #0ea5e9; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h4 style="margin: 0 0 15px 0; color: #0ea5e9; font-size: 16px; font-weight: 600;">📎 Uploaded Quotation Files</h4>
                                        <div style="margin: 0;">
                                            @foreach($quotation->attachments as $attachment)
                                                @if(isset($attachment['name']))
                                                <div style="margin-bottom: 8px; padding: 8px; background-color: #ffffff; border-radius: 4px; border: 1px solid #e0e7ff;">
                                                    <p style="margin: 0; font-size: 14px; color: #2D3748;">
                                                        <span style="font-weight: 600; color: #0ea5e9;">📄</span> 
                                                        {{ $attachment['name'] }}
                                                    </p>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <p style="margin: 10px 0 0 0; font-size: 13px; color: #64748b;">These files contain the complete quotation details and pricing information.</p>
                                    </td>
                                </tr>
                            </table>
                            @endif
                            @endif

                            @if($quotation->notes)
                            <!-- Supplier Notes Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 20px; background-color: #eff6ff; border-radius: 8px; border-left: 3px solid #3b82f6;">
                                        <p style="margin: 0; font-size: 16px; color: #2D3748;"><span style="font-weight: 600; color: #3b82f6;">💬 Supplier Notes:</span> {{ $quotation->notes }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            @if($isPdfOnly)
                            <!-- PDF-Only Total Amount - Outlook Compatible -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f59e0b; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <!--[if gte mso 9]>
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f59e0b;">
                                            <tr>
                                                <td style="padding: 30px; text-align: center;">
                                        <![endif]-->
                                                    <div style="color: #FFFFFF;">
                                                        <div style="font-size: 16px; margin-bottom: 5px; opacity: 0.9; font-weight: 300; text-transform: uppercase; letter-spacing: 1px;">📄 PDF Quotation Submitted</div>
                                                        <div style="font-size: 24px; font-weight: 700; margin: 0; letter-spacing: -1px;">Pricing in Attached Files</div>
                                                        <div style="font-size: 14px; margin-top: 5px; opacity: 0.8;">Please review uploaded documents for complete pricing details</div>
                                                    </div>
                                        <!--[if gte mso 9]>
                                                </td>
                                            </tr>
                                        </table>
                                        <![endif]-->
                                    </td>
                                </tr>
                            </table>
                            @else
                            <!-- Regular Total Amount - Outlook Compatible -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #dc2626; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <!--[if gte mso 9]>
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #dc2626;">
                                            <tr>
                                                <td style="padding: 30px; text-align: center;">
                                        <![endif]-->
                                                    <div style="color: #FFFFFF;">
                                                        <div style="font-size: 16px; margin-bottom: 5px; opacity: 0.9; font-weight: 300; text-transform: uppercase; letter-spacing: 1px;">💰 Total Quoted Amount</div>
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
                            @endif

                            <!-- Action Buttons -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td style="text-align: center;">
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                            <tr>
                                                <td style="background-color: #dc2626; border-radius: 8px; text-align: center; padding-right: 10px;">
                                                    <a href="{{ $url }}" style="display: inline-block; padding: 15px 30px; color: #FFFFFF; text-decoration: none; font-weight: 600; font-size: 18px; letter-spacing: 0.5px;">
                                                        🔍 Review & Manage
                                                    </a>
                                                </td>
                                                <td style="background-color: #16a34a; border-radius: 8px; text-align: center; padding-left: 10px;">
                                                    <a href="{{ route('admin.quotations.index') }}" style="display: inline-block; padding: 15px 30px; color: #FFFFFF; text-decoration: none; font-weight: 600; font-size: 18px; letter-spacing: 0.5px;">
                                                        📊 View All Quotations
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Priority Messages -->
                            <div style="margin: 30px 0;">
                                <div style="padding: 20px; background-color: #fef3c7; border-radius: 8px; border-left: 3px solid #f59e0b; margin-bottom: 20px;">
                                    <h4 style="margin: 0 0 10px 0; color: #92400e; font-size: 16px; font-weight: 600;">⚡ Action Required</h4>
                                    <p style="margin: 0; color: #92400e; font-size: 15px; line-height: 1.6;">This quotation needs your immediate attention. Please review and approve/reject to maintain supplier relationships and keep the procurement process moving smoothly.</p>
                                </div>
                                
                                <p style="margin: 0; color: #4A5568; font-size: 16px; line-height: 1.7;">Quick response helps maintain good supplier relationships and ensures timely project completion. 🚀</p>
                            </div>
                            
                            <!-- Inquiry Details (if customer requested) -->
                            @if($inquiry->user ?? false)
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f0f9ff; margin: 25px 0; border-radius: 8px; border-left: 3px solid #0ea5e9; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h4 style="margin: 0 0 15px 0; color: #0ea5e9; font-size: 16px; font-weight: 600;">👤 Customer Details</h4>
                                        <p style="margin: 0 0 5px 0; color: #2D3748; font-size: 14px;"><strong>Name:</strong> {{ $inquiry->user->name ?? 'N/A' }}</p>
                                        <p style="margin: 0 0 5px 0; color: #2D3748; font-size: 14px;"><strong>Email:</strong> {{ $inquiry->user->email ?? 'N/A' }}</p>
                                        @if($inquiry->requirements ?? false)
                                        <p style="margin: 10px 0 0 0; color: #2D3748; font-size: 14px;"><strong>Requirements:</strong> {{ $inquiry->requirements }}</p>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            @endif
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
                                🔔 This is an automated admin notification from MaxMed's quotation management system.<br>
                                Please review and take action promptly to maintain service quality.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 