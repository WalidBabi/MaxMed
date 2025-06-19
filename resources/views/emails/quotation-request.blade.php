<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Quotation Request</title>
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
                                            📝 New Quotation Request
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
                                        <div style="font-weight: 600; color: #047857; margin-bottom: 8px; font-size: 16px;">🎯 New Sales Opportunity</div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6;">A customer has submitted a quotation request for your products. This is a qualified sales lead that requires immediate attention and professional response.</p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Product Information Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #059669; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #059669; margin-bottom: 8px; letter-spacing: -0.5px;">📦 Product Information</div>
    </div>
    
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px; width: 120px;">Product Name</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 600; font-size: 15px;">{{ $product->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Product SKU</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $product->sku ?? $product->id }}</td>
                                            </tr>
                                            @if($product->category)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Category</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $product->category->name }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Product Link</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    <a href="{{ route('product.show', $product) }}" style="color: #059669; text-decoration: none;">View Product Page</a>
                                                </td>
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
                                            <div style="font-size: 20px; font-weight: 700; color: #D97706; margin-bottom: 8px; letter-spacing: -0.5px;">📋 Request Details</div>
    </div>

                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px; width: 150px;">Quantity Required</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 700; font-size: 18px;">{{ number_format($quantity) }} units</td>
                                            </tr>
                                            @if($size)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px;">Size/Specifications</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $size }}</td>
                                            </tr>
                                            @endif
                                            @if($deliveryTimeline)
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; font-weight: 600; color: #92400E; font-size: 15px;">Delivery Timeline</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #F3E8FF; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    @switch($deliveryTimeline)
                                                        @case('urgent')
                                                            <span style="color: #DC2626; font-weight: 600;">🚨 Urgent (1-2 weeks)</span>
                                                            @break
                                                        @case('standard')
                                                            <span style="color: #059669;">⏰ Standard (3-4 weeks)</span>
                                                            @break
                                                        @case('flexible')
                                                            <span style="color: #6366F1;">📅 Flexible (1-2 months)</span>
                                                            @break
                                                        @default
                                                            {{ $deliveryTimeline }}
                                                    @endswitch
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #92400E; font-size: 15px;">Request Date</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ nowDubai('M j, Y \a\t g:i A T') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Customer Information -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #6366F1; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #6366F1; margin-bottom: 8px; letter-spacing: -0.5px;">👤 Customer Information</div>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            @if($contactName || $contactEmail)
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px; width: 120px;">Name</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $contactName ?? ($user ? $user->name : 'Guest User') }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Email</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                        <a href="mailto:{{ $contactEmail ?? ($user ? $user->email : 'Not provided') }}" style="color: #6366F1; text-decoration: none;">{{ $contactEmail ?? ($user ? $user->email : 'Not provided') }}</a>
                                                    </td>
                                                </tr>
                                                @if($contactPhone)
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Phone</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                        <a href="tel:{{ $contactPhone }}" style="color: #6366F1; text-decoration: none;">{{ $contactPhone }}</a>
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($contactCompany)
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Company</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $contactCompany }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Customer Type</td>
                                                    <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                        @if($contactCompany)
                                                            <span style="color: #059669; font-weight: 600;">🏢 Business Customer</span>
                                                        @else
                                                            <span style="color: #F59E0B; font-weight: 600;">👤 Individual Customer</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif(isset($user) && $user)
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Name</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $user->name }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Email</td>
                                                    <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                        <a href="mailto:{{ $user->email }}" style="color: #6366F1; text-decoration: none;">{{ $user->email }}</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Customer Type</td>
                                                    <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                        <span style="color: #3B82F6; font-weight: 600;">👨‍💼 Registered Customer</span>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Customer</td>
                                                    <td style="padding: 12px 0; color: #DC2626; font-weight: 600; font-size: 15px;">⚠️ Guest User - Limited Contact Information</td>
                                                </tr>
        @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Additional Requirements -->
                            @if($requirements || $notes)
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">📝 Additional Requirements & Notes</h3>

        @if($requirements)
                                <div style="background-color: #F0F9FF; padding: 20px; border-radius: 8px; border-left: 4px solid #0EA5E9; margin-bottom: 15px;">
                                    <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #0369A1;">🎯 Specific Requirements</h4>
                                    <p style="margin: 0; color: #2D3748; font-size: 15px; line-height: 1.7; white-space: pre-line;">{{ $requirements }}</p>
                                </div>
        @endif
        
        @if($notes)
                                <div style="background-color: #F7FAFC; padding: 20px; border-radius: 8px; border-left: 4px solid #6B7280;">
                                    <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #374151;">💭 Additional Notes</h4>
                                    <p style="margin: 0; color: #2D3748; font-size: 15px; line-height: 1.7; white-space: pre-line;">{{ $notes }}</p>
                                </div>
        @endif
    </div>
                            @endif

                            <!-- Priority Assessment -->
                            @php
                                $isUrgent = $deliveryTimeline === 'urgent';
                                $isHighVolume = $quantity >= 10;
                                $hasDetailedRequirements = !empty($requirements) || !empty($notes);
                                $hasCompanyInfo = !empty($contactCompany);
                                $isRegisteredCustomer = isset($user) && $user;
                            @endphp

                            @if($isUrgent || $isHighVolume || $hasCompanyInfo || $isRegisteredCustomer)
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%); border: 1px solid #EF4444; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="display: inline-block; background-color: #EF4444; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">🔥 High Priority Request</div>
                                        <h4 style="color: #991B1B; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Priority Indicators Detected</h4>
                                        <ul style="margin: 0; padding-left: 20px; color: #7F1D1D; line-height: 1.6;">
                                            @if($isUrgent)
                                                <li style="margin-bottom: 5px;">🚨 <strong>Urgent Timeline Required</strong> - Customer needs delivery in 1-2 weeks</li>
                                            @endif
                                            @if($isHighVolume)
                                                <li style="margin-bottom: 5px;">💰 <strong>High-Value Order</strong> - Large quantity request ({{ number_format($quantity) }} units)</li>
                                            @endif
                                            @if($hasCompanyInfo)
                                                <li style="margin-bottom: 5px;">🏢 <strong>Business Customer</strong> - Company: {{ $contactCompany }}</li>
                                            @endif
                                            @if($isRegisteredCustomer)
                                                <li style="margin-bottom: 5px;">👨‍💼 <strong>Existing Customer</strong> - Registered user with account history</li>
                                            @endif
                                            @if($hasDetailedRequirements)
                                                <li style="margin-bottom: 5px;">📋 <strong>Detailed Requirements</strong> - Customer provided specific technical requirements</li>
                                            @endif
                                        </ul>
                                        <p style="margin: 15px 0 0 0; color: #7F1D1D; font-size: 14px; font-weight: 600;">
                                            ⏰ Recommended Response Time: 
                                            @if($isUrgent)
                                                <span style="color: #DC2626;">Within 1 hour</span>
                                            @elseif($isHighVolume || $hasCompanyInfo)
                                                <span style="color: #DC2626;">Within 2 hours</span>
                                            @else
                                                <span style="color: #DC2626;">Within 4 hours</span>
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Action Steps -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%); border: 1px solid #22C55E; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="display: inline-block; background-color: #22C55E; color: #FFFFFF; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">✅ Action Required</div>
                                        <h4 style="color: #14532D; margin: 0 0 12px 0; font-size: 18px; font-weight: 600;">Recommended Next Steps</h4>
                                        <ol style="margin: 0; padding-left: 20px; color: #15803D; line-height: 1.6;">
                                            <li style="margin-bottom: 8px;"><strong>Review Product Availability:</strong> Check stock levels and supplier availability for {{ $product->name }}</li>
                                            <li style="margin-bottom: 8px;"><strong>Calculate Pricing:</strong> Prepare competitive quote for {{ number_format($quantity) }} units
                                                @if($deliveryTimeline === 'urgent')
                                                    <span style="color: #DC2626; font-weight: 600;"> (Factor in expedited shipping costs)</span>
                                                @endif
                                            </li>
                                            <li style="margin-bottom: 8px;"><strong>Contact Customer:</strong> 
                                                @if($contactPhone)
                                                    Call {{ $contactPhone }} for immediate discussion
        @else
                                                    Email {{ $contactEmail ?? ($user ? $user->email : 'customer') }} with detailed quotation
        @endif
                                            </li>
                                            <li style="margin-bottom: 8px;"><strong>CRM Update:</strong> Log this opportunity in CRM and create follow-up tasks</li>
                                            <li style="margin-bottom: 8px;"><strong>Prepare Documentation:</strong> Ready technical specifications and delivery terms</li>
                                            <li style="margin-bottom: 0;"><strong>Monitor Response:</strong> Track customer engagement and follow up within 24-48 hours if no response</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <!-- Submission Details -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #F7FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Request ID:</strong> #{{ $product->id }}-{{ nowDubai('YmdHis') }}</p>
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Submission Time:</strong> {{ nowDubai('M j, Y \a\t g:i A T') }}</p>
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px;"><strong>Source:</strong> Website Quotation Form</p>
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
                                This is an automated notification from your quotation system.<br>
                                Please respond to this sales opportunity promptly to maximize conversion potential.<br>
                                <strong>Remember:</strong> Fast response times significantly increase quotation-to-sale conversion rates.
    </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 