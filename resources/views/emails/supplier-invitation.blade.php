<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Invitation</title>
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
                <table class="email-container" cellpadding="0" cellspacing="0" border="0" style="width: 650px; margin: 0 auto; background-color: #FFFFFF; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    
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
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">🤝 Supplier Invitation</h1>
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
                            
                            <!-- Welcome Section -->
                            <div style="margin-bottom: 30px;">
                                <h2 style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #1A202C;">
                                    Welcome {{ $name }}!
                                </h2>
                                <p style="margin: 0 0 20px 0; font-size: 16px; color: #4A5568; line-height: 1.7;">
                                    You have been invited by {{ $inviterName }} to join MaxMed as a trusted supplier partner. Your expertise in 
                                    @if($companyName)
                                        <strong>{{ $companyName }}</strong> makes you an ideal candidate for our supplier network.
                                    @else
                                        your field makes you an ideal candidate for our supplier network.
                                    @endif
                                </p>
                            </div>

                            <!-- Custom Message -->
                            @if($customMessage)
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="font-weight: 600; color: #171e60; margin-bottom: 8px; font-size: 16px;">💬 Personal Message</div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6; white-space: pre-line;">{{ $customMessage }}</p>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- Partnership Benefits -->
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">
                                    🎯 Partnership Benefits
                                </h3>
                                
                                <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                    <tr>
                                        <td style="width: 40px; vertical-align: top; padding: 8px 0;">
                                            <div style="width: 32px; height: 32px; background-color: #171e60; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">1</div>
                                        </td>
                                        <td style="padding: 8px 0 8px 15px; vertical-align: top;">
                                            <h4 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #1A202C;">Access to Quality Leads</h4>
                                            <p style="margin: 0; color: #4A5568; font-size: 14px; line-height: 1.5;">Receive qualified inquiries from laboratories, research centers, and healthcare facilities across the UAE and region.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px; vertical-align: top; padding: 8px 0;">
                                            <div style="width: 32px; height: 32px; background-color: #171e60; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">2</div>
                                        </td>
                                        <td style="padding: 8px 0 8px 15px; vertical-align: top;">
                                            <h4 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #1A202C;">Streamlined Order Management</h4>
                                            <p style="margin: 0; color: #4A5568; font-size: 14px; line-height: 1.5;">Manage quotations, orders, and deliveries through our comprehensive supplier portal.</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40px; vertical-align: top; padding: 8px 0;">
                                            <div style="width: 32px; height: 32px; background-color: #171e60; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">3</div>
                                        </td>
                                        <td style="padding: 8px 0 8px 15px; vertical-align: top;">
                                            <h4 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #1A202C;">Market Expansion</h4>
                                            <p style="margin: 0; color: #4A5568; font-size: 14px; line-height: 1.5;">Expand your reach in the scientific and laboratory equipment market with our established customer base.</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Call to Action -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #171e60; margin: 30px 0; border-radius: 12px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <h3 style="margin: 0 0 15px 0; font-size: 22px; font-weight: 600; color: #FFFFFF;">Ready to Get Started?</h3>
                                        <p style="margin: 0 0 25px 0; color: #FFFFFF; font-size: 16px; line-height: 1.6; opacity: 0.9;">
                                            Click the button below to set up your supplier account and complete your profile. This invitation expires in 7 days.
                                        </p>
                                        
                                        <!-- CTA Button -->
                                        <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                            <tr>
                                                <td style="background-color: #FFFFFF; border-radius: 8px; text-align: center;">
                                                    <a href="{{ route('supplier.invitation.onboarding', ['token' => $token]) }}" 
                                                       style="display: inline-block; padding: 16px 32px; color: #171e60; text-decoration: none; font-weight: 600; font-size: 16px; letter-spacing: 0.5px;">
                                                        🚀 Complete Onboarding
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        
                                        <p style="margin: 20px 0 0 0; color: #FFFFFF; font-size: 12px; opacity: 0.8;">
                                            If the button doesn't work, copy and paste this link into your browser:<br>
                                            <span style="word-break: break-all; color: #FFFFFF;">{{ route('supplier.invitation.onboarding', ['token' => $token]) }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Next Steps -->
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">
                                    📋 What Happens Next?
                                </h3>
                                
                                <div style="background-color: #f8fafc; padding: 25px; border-radius: 8px; border-left: 4px solid #171e60;">
                                    <ol style="margin: 0; padding-left: 20px; color: #2D3748; line-height: 1.7;">
                                        <li style="margin-bottom: 10px;"><strong>Account Setup:</strong> Complete your onboarding using the link above</li>
                                        <li style="margin-bottom: 10px;"><strong>Password Setup:</strong> Check your email for password reset instructions to set your secure password</li>
                                        <li style="margin-bottom: 10px;"><strong>Email Verification:</strong> Verify your email address to activate your account</li>
                                        <li style="margin-bottom: 10px;"><strong>Profile Completion:</strong> Add your company information, certifications, and product catalog</li>
                                        <li style="margin-bottom: 10px;"><strong>Verification:</strong> Our team will review and verify your supplier profile</li>
                                        <li style="margin-bottom: 10px;"><strong>Category Assignment:</strong> Get assigned to relevant product categories based on your expertise</li>
                                        <li style="margin-bottom: 0;"><strong>Start Receiving Orders:</strong> Begin receiving quotation requests and orders from our customers</li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 15px;">
                                            <div style="font-size: 18px; font-weight: 600; color: #171e60; margin-bottom: 8px; letter-spacing: -0.5px;">📞 Need Help?</div>
                                        </div>
                                        
                                        <p style="margin: 0 0 15px 0; color: #2D3748; line-height: 1.6;">
                                            If you have any questions about the registration process or our supplier program, please don't hesitate to contact us:
                                        </p>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 8px 0; font-weight: 600; color: #4A5568; font-size: 15px; width: 100px;">Email:</td>
                                                <td style="padding: 8px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    <a href="mailto:wbabi@localhost.com" style="color: #171e60; text-decoration: none;">wbabi@maxmedme.com</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Phone:</td>
                                                <td style="padding: 8px 0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    <a href="tel:+971-4-123-4567" style="color: #171e60; text-decoration: none;">+971-554602500</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Contact:</td>
                                                <td style="padding: 8px 0; color: #1A202C; font-weight: 500; font-size: 15px;">Walid Babi</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    
                    <!-- Footer Section -->
                    <tr>
                        <td style="background-color: #F7FAFC; padding: 30px 30px 40px 30px; text-align: center; border-top: 1px solid #E2E8F0;">
                            <p style="margin: 0 0 10px 0; color: #718096; font-size: 14px;">
                                This invitation was sent by {{ $inviterName ?: 'MaxMed Team' }} on behalf of MaxMed Scientific & Laboratory Equipment
                            </p>
                            <p style="margin: 0 0 15px 0; color: #718096; font-size: 12px;">
                                © {{ date('Y') }} MaxMed Scientific & Laboratory Equipment. All rights reserved.
                            </p>
                            <p style="margin: 0; color: #A0AEC0; font-size: 11px; line-height: 1.4;">
                                This is an automated email. Please do not reply directly to this message.<br>
                                If you received this email in error, please contact us at <a href="mailto:wbabi@maxmedme.com" style="color: #171e60;">wbabi@maxmedme.com</a>
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 