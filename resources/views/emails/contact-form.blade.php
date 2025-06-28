<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Form Submission</title>
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
                    
                    <!-- Modern Header Section with Logo -->
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
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">📧 New Contact Submission</h1>
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
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">New Customer Inquiry</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">A potential customer has submitted a contact form through your website. Please review the details below and respond promptly to maintain excellent customer service.</p>
                            </div>

                            <!-- Customer Information Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <h3 style="margin: 0; color: #171e60; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Customer Details</h3>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Name</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $data['name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    <a href="mailto:{{ $data['email'] }}" style="color: #171e60; text-decoration: none;">{{ $data['email'] }}</a>
                                                </td>
                                            </tr>
                                            @if(isset($data['phone']) && $data['phone'])
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Phone</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    <a href="tel:{{ $data['phone'] }}" style="color: #171e60; text-decoration: none;">{{ $data['phone'] }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($data['company']) && $data['company'])
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Company</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">{{ $data['company'] }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Subject</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 600; font-size: 15px;">{{ $data['subject'] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message Content -->
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">Customer Message</h3>
                                <div style="background-color: #F7FAFC; padding: 25px; border-radius: 8px; border-left: 4px solid #E2E8F0;">
                                    <p style="margin: 0; color: #2D3748; font-size: 16px; line-height: 1.7; white-space: pre-line;">{{ $data['message'] }}</p>
                                </div>
                            </div>

                            <!-- Priority Assessment -->
                            @php
                                $isPotentialSale = stripos($data['subject'], 'quote') !== false || 
                                                 stripos($data['subject'], 'quotation') !== false || 
                                                 stripos($data['subject'], 'price') !== false || 
                                                 stripos($data['subject'], 'purchase') !== false || 
                                                 stripos($data['subject'], 'buy') !== false ||
                                                 stripos($data['subject'], 'sales') !== false ||
                                                 stripos($data['message'], 'quote') !== false || 
                                                 stripos($data['message'], 'quotation') !== false || 
                                                 stripos($data['message'], 'price') !== false || 
                                                 stripos($data['message'], 'purchase') !== false || 
                                                 stripos($data['message'], 'buy') !== false;
                                                 
                                $hasCompany = isset($data['company']) && !empty($data['company']);
                                $hasPhone = isset($data['phone']) && !empty($data['phone']);
                            @endphp

                            @if($isPotentialSale || $hasCompany || $hasPhone)
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #fef3f2; margin: 25px 0; border-radius: 8px; border-left: 4px solid #dc2626; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 15px;">
                                            <h3 style="margin: 0; color: #dc2626; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Priority Indicators</h3>
                                        </div>
                                        <ul style="margin: 0; padding-left: 20px; color: #7F1D1D; line-height: 1.6;">
                                            @if($isPotentialSale)
                                                <li style="margin-bottom: 5px;">🎯 <strong>Potential Sales Inquiry</strong> - Keywords related to quotation/purchase detected</li>
                                            @endif
                                            @if($hasCompany)
                                                <li style="margin-bottom: 5px;">🏢 <strong>Business Customer</strong> - Company information provided: {{ $data['company'] }}</li>
                                            @endif
                                            @if($hasPhone)
                                                <li style="margin-bottom: 5px;">📞 <strong>Phone Contact Available</strong> - Ready for immediate follow-up call</li>
                                            @endif
                                        </ul>
                                        <p style="margin: 15px 0 0 0; color: #7F1D1D; font-size: 14px; font-weight: 600;">
                                            ⏰ Recommended Response Time: <span style="color: #DC2626;">Within 2 hours</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Action Steps -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #F0FFF4; margin: 25px 0; border-radius: 8px; border-left: 4px solid #48BB78; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 15px;">
                                            <h3 style="margin: 0; color: #48BB78; font-size: 18px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Recommended Actions</h3>
                                        </div>
                                        <ol style="margin: 0; padding-left: 20px; color: #15803D; line-height: 1.6;">
                                            <li style="margin-bottom: 8px;"><strong>Review Inquiry:</strong> Analyze the customer's message and requirements</li>
                                            <li style="margin-bottom: 8px;"><strong>Assess Priority:</strong> Determine if this is a sales lead or general inquiry</li>
                                            <li style="margin-bottom: 8px;"><strong>Contact Customer:</strong> 
                                                @if($hasPhone)
                                                    Call {{ $data['phone'] }} for immediate discussion
                                                @else
                                                    Email {{ $data['email'] }} with detailed response
                                                @endif
                                            </li>
                                            <li style="margin-bottom: 8px;"><strong>CRM Update:</strong> Log this contact in CRM and create follow-up tasks</li>
                                            <li style="margin-bottom: 8px;"><strong>Prepare Response:</strong> Draft professional response addressing all points</li>
                                            <li style="margin-bottom: 0;"><strong>Follow Up:</strong> Schedule follow-up within 24-48 hours if no response</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Submission Details -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #F7FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Submission ID:</strong> #{{ nowDubai('YmdHis') }}</p>
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Submission Time:</strong> {{ nowDubai('M j, Y \a\t g:i A T') }}</p>
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Source:</strong> Website Contact Form</p>
                                <p style="margin: 0; color: #4A5568; font-size: 14px;"><strong>IP Address:</strong> {{ request()->ip() }}</p>
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
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: #059669; text-decoration: none;">sales@maxmedme.com</a> | 
                                    🌐 <a href="http://www.maxmedme.com" style="color: #059669; text-decoration: none;">www.maxmedme.com</a>
                                </div>
                            </div>
                            <div style="font-size: 12px; opacity: 0.7; border-top: 1px solid #2D3748; padding-top: 15px; margin-top: 15px;">
                                This is an automated notification from your contact form system.<br>
                                Please respond to this customer inquiry promptly to maintain excellent service standards.<br>
                                <strong>Remember:</strong> Fast response times significantly increase customer satisfaction and conversion rates.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 