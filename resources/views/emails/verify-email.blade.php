<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email Address - MaxMed</title>
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
                                            🔐 Verify Your Email Address
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
                            
                            <!-- Welcome Section -->
                            <div style="margin-bottom: 30px;">
                                <h2 style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #1A202C;">
                                    Hello {{ $user->name }}!
                                </h2>
                                <p style="margin: 0 0 20px 0; font-size: 16px; color: #4A5568; line-height: 1.7;">
                                    Thank you for registering as a {{ $userType ?? 'customer' }} with MaxMed Scientific & Laboratory Equipment. 
                                    To complete your registration and access all features, please verify your email address by clicking the button below.
                                </p>
                            </div>

                            <!-- Verification Alert -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #DBEAFE; border-left: 4px solid #3B82F6; margin: 0 0 30px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #1D4ED8; margin-bottom: 8px; font-size: 16px;">
                                            🎯 Email Verification Required
                                        </div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6;">
                                            Please verify your email address to activate your account and access all MaxMed features. This verification link will expire in 60 minutes.
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Account Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #f8fafc; margin: 30px 0; border-radius: 8px; border-left: 4px solid #171e60; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 20px; font-weight: 700; color: #171e60; margin-bottom: 8px; letter-spacing: -0.5px;">👤 Account Information</div>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px; width: 120px;">Name</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 600; font-size: 15px;">{{ $user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">Account Type</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; font-weight: 500; font-size: 15px;">
                                                    @if(($userType ?? 'customer') === 'supplier')
                                                        <span style="color: #D97706; font-weight: 600;">🏢 Supplier</span>
                                                    @else
                                                        <span style="color: #059669; font-weight: 600;">👤 Customer</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Registration Date</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ $user->created_at->format('F j, Y \a\t g:i A') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Verification Button -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td style="text-align: center; padding: 20px; background: linear-gradient(135deg, #22C55E 0%, #16A34A 100%); border-radius: 8px;">
                                        <a href="{{ $verificationUrl }}" style="display: inline-block; background-color: #FFFFFF; color: #16A34A; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 18px; letter-spacing: 0.5px; border: 2px solid #16A34A;">
                                            ✅ Verify Email Address
                                        </a>
                                        <div style="margin-top: 15px; color: #FFFFFF; font-size: 14px; opacity: 0.9;">
                                            Click the button above to verify your email address
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alternative Link -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #F7FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px; line-height: 1.6;">
                                    <strong>Can't click the button?</strong> Copy and paste this link into your browser:
                                </p>
                                <p style="margin: 0; color: #171e60; font-size: 12px; word-break: break-all; font-family: 'Courier New', monospace;">
                                    {{ $verificationUrl }}
                                </p>
                            </div>

                            <!-- Security Notice -->
                            <div style="margin: 30px 0; padding: 20px; background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-left: 4px solid #F59E0B; border-radius: 8px;">
                                <div style="font-weight: 600; color: #D97706; margin-bottom: 8px; font-size: 16px;">
                                    🔒 Security Notice
                                </div>
                                <ul style="margin: 0; padding-left: 20px; color: #92400E; line-height: 1.6;">
                                    <li style="margin-bottom: 5px;">This verification link will expire in <strong>60 minutes</strong></li>
                                    <li style="margin-bottom: 5px;">If you didn't create an account, please ignore this email</li>
                                    <li style="margin-bottom: 5px;">Your account data will be automatically removed if not verified within 7 days</li>
                                    <li style="margin-bottom: 0;">Need help? Contact our support team</li>
                                </ul>
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
                                This is an automated email verification message. Please do not reply directly to this message.<br>
                                If you have any questions, please contact our support team.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 