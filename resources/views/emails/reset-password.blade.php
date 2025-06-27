<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - MaxMed</title>
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
                                            🔐 Reset Your Password
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
                                    You are receiving this email because we received a password reset request for your {{ $userType ?? 'customer' }} account with MaxMed Scientific & Laboratory Equipment.
                                </p>
                            </div>

                            <!-- Password Reset Alert -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #FEF3C7; border-left: 4px solid #F59E0B; margin: 0 0 30px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #D97706; margin-bottom: 8px; font-size: 16px;">
                                            🔑 Password Reset Request
                                        </div>
                                        <p style="margin: 0; color: #92400E; line-height: 1.6;">
                                            Click the button below to reset your password. This password reset link will expire in 60 minutes.
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
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">Request Time</td>
                                                <td style="padding: 12px 0; color: #1A202C; font-weight: 500; font-size: 15px;">{{ now()->format('F j, Y \a\t g:i A') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Reset Password Button -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/reset-password/' . $token . '?email=' . urlencode($user->email)) }}" target="_blank" style="background-color: #171e60; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Alternative Link -->
                            <div style="margin: 30px 0; padding: 20px; background-color: #F7FAFC; border-radius: 8px; border: 1px solid #E2E8F0;">
                                <p style="margin: 0 0 10px 0; color: #4A5568; font-size: 14px; line-height: 1.6;">
                                    <strong>Can't click the button?</strong> Copy and paste this link into your browser:
                                </p>
                                <p style="margin: 0; color: #171e60; font-size: 12px; word-break: break-all; font-family: 'Courier New', monospace;">
                                    {{ url('/reset-password/' . $token . '?email=' . urlencode($user->email)) }}
                                </p>
                            </div>

                            <!-- Security Notice -->
                            <div style="margin: 30px 0; padding: 20px; background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%); border-left: 4px solid #EF4444; border-radius: 8px;">
                                <div style="font-weight: 600; color: #DC2626; margin-bottom: 8px; font-size: 16px;">
                                    🔒 Security Notice
                                </div>
                                <ul style="margin: 0; padding-left: 20px; color: #991B1B; line-height: 1.6;">
                                    <li style="margin-bottom: 5px;">This password reset link will expire in <strong>60 minutes</strong></li>
                                    <li style="margin-bottom: 5px;">If you didn't request a password reset, please ignore this email</li>
                                    <li style="margin-bottom: 5px;">Your password will remain unchanged until you click the link above</li>
                                    <li style="margin-bottom: 0;">For security, this link can only be used once</li>
                                </ul>
                            </div>

                            <!-- What to do next -->
                            <div style="margin: 30px 0;">
                                <h3 style="margin: 0 0 15px 0; font-size: 20px; font-weight: 600; color: #1A202C; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">
                                    📋 What to do next?
                                </h3>
                                
                                <div style="background-color: #f8fafc; padding: 25px; border-radius: 8px; border-left: 4px solid #171e60;">
                                    <ol style="margin: 0; padding-left: 20px; color: #2D3748; line-height: 1.7;">
                                        <li style="margin-bottom: 10px;"><strong>Click Reset Button:</strong> Use the button above to access the password reset page</li>
                                        <li style="margin-bottom: 10px;"><strong>Enter New Password:</strong> Choose a strong, secure password</li>
                                        <li style="margin-bottom: 10px;"><strong>Confirm Password:</strong> Re-enter your new password to confirm</li>
                                        <li style="margin-bottom: 10px;"><strong>Save Changes:</strong> Click submit to update your password</li>
                                        <li style="margin-bottom: 0;"><strong>Login:</strong> Use your new password to access your account</li>
                                    </ol>
                                </div>
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
                                This is an automated password reset message. Please do not reply directly to this message.<br>
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