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
                        <td style="background-color: #4F46E5; background: linear-gradient(135deg, #4F46E5 0%, #3B82F6 100%); padding: 50px 30px; text-align: center;">
                            <!--[if gte mso 9]>
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #4F46E5;">
                                <tr>
                                    <td style="padding: 50px 30px; text-align: center;">
                            <![endif]-->
                                        <h1 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 600; color: #FFFFFF; letter-spacing: -0.5px;">
                                            📧 New Contact Submission
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
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #EBF8FF 0%, #BEE3F8 100%); border-left: 4px solid #3182CE; margin: 0 0 30px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #2B6CB0; margin-bottom: 8px; font-size: 16px;">🔔 New Customer Inquiry</div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6;">A potential customer has submitted a contact form through your website. Please review the details below and respond promptly to maintain excellent customer service.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Customer Information Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #4F46E5; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #4F46E5; margin-bottom: 8px; letter-spacing: -0.5px;">👤 Customer Details</div>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px; width: 120px;">Name</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $data['name'] }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    <a href="mailto:{{ $data['email'] }}" style="color: #4F46E5; text-decoration: none;">{{ $data['email'] }}</a>
                                                </td>
                                            </tr>
                                            @if(isset($data['phone']) && $data['phone'])
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Phone</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    <a href="tel:{{ $data['phone'] }}" style="color: #4F46E5; text-decoration: none;">{{ $data['phone'] }}</a>
                                                </td>
                                            </tr>
                                            @endif
                                            @if(isset($data['company']) && $data['company'])
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Company</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $data['company'] }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Subject</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 600; font-size: 15px;">{{ $data['subject'] }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Message Content -->
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">💬 Customer Message</h3>
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
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border: 1px solid #F59E0B; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="display: inline-block; background-color: #F59E0B; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">⚡ High Priority Lead</div>
                                        <h4 style="color: #92400E; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Priority Indicators Detected</h4>
                                        <ul style="margin: 0; padding-left: 20px; color: #451A03; line-height: 1.6;">
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
                                        <p style="margin: 15px 0 0 0; color: #451A03; font-size: 14px; font-weight: 600;">
                                            ⏰ Recommended Response Time: <span style="color: #DC2626;">Within 2 hours</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Action Steps -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #F0FFF4 0%, #C6F6D5 100%); border: 1px solid #48BB78; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="display: inline-block; background-color: #48BB78; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">📋 Next Steps</div>
                                        <h4 style="color: #22543D; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Recommended Actions</h4>
                                        <ol style="margin: 0; padding-left: 20px; color: #1A365D; line-height: 1.6;">
                                            <li style="margin-bottom: 8px;"><strong>Review Contact Details:</strong> Verify customer information and add to CRM if applicable</li>
                                            @if($isPotentialSale)
                                                <li style="margin-bottom: 8px;"><strong>Prepare Quotation:</strong> Gather product information and pricing for immediate response</li>
                                            @endif
                                            @if($hasPhone)
                                                <li style="margin-bottom: 8px;"><strong>Phone Follow-up:</strong> Consider calling {{ $data['phone'] }} for immediate assistance</li>
                                            @else
                                                <li style="margin-bottom: 8px;"><strong>Email Response:</strong> Reply professionally to {{ $data['email'] }}</li>
                                            @endif
                                            <li style="margin-bottom: 8px;"><strong>CRM Update:</strong> Log this interaction and set follow-up reminders</li>
                                            <li style="margin-bottom: 0;"><strong>Monitor Response:</strong> Track customer engagement and follow up if needed</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Submission Details -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #F7FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
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
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: #4F46E5; text-decoration: none;">sales@maxmedme.com</a> | 
                                    🌐 <a href="http://www.maxmedme.com" style="color: #4F46E5; text-decoration: none;">www.maxmedme.com</a>
                                </div>
                            </div>
                            <div style="font-size: 12px; opacity: 0.7; border-top: 1px solid #2D3748; padding-top: 15px; margin-top: 15px;">
                                This is an automated notification from your website contact form system.<br>
                                Please respond to the customer inquiry promptly to maintain excellent service standards.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 