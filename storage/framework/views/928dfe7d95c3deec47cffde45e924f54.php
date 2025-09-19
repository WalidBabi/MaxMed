<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($isNewLead ? 'New Lead Assigned' : 'Lead Reassigned'); ?> - <?php echo e($lead->full_name); ?></title>
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
        
        /* Priority badge styles */
        .priority-high { background-color: #F56565; }
        .priority-medium { background-color: #ED8936; }
        .priority-low { background-color: #48BB78; }
        
        /* Status badge styles */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #FFFFFF;
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
                                            <img src="<?php echo e(asset('Images/logo.png')); ?>" alt="MaxMed Logo" width="380" height="97" class="email-logo" style="width: 380px; height: 97px; max-width: 100%; display: block; margin: 0 auto;">
                                        </div>
                                        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 600; color: #171e60; letter-spacing: -0.5px;">
                                            <?php echo e($isNewLead ? '👥 New Lead Assigned' : '🔄 Lead Reassigned'); ?>

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
                                <h2 style="margin: 0 0 12px 0; font-size: 24px; font-weight: 600; color: #1A202C;">Hello <?php echo e($assignedUser->name); ?>,</h2>
                                <p style="margin: 0; font-size: 18px; color: #4A5568; line-height: 1.7;">
                                    <?php if($isNewLead): ?>
                                        A new lead has been assigned to you. Please review the details below and follow up as appropriate.
                                    <?php else: ?>
                                        A lead has been reassigned to you<?php echo e($reassignedBy ? ' by ' . $reassignedBy->name : ''); ?>. Please review the details below and continue the follow-up process.
                                    <?php endif; ?>
                                </p>
                            </div>

                            <!-- Lead Details Card -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background-color: #EDF2F7; background: linear-gradient(135deg, #EDF2F7 0%, #E2E8F0 100%); margin: 30px 0; border-radius: 8px; border-left: 4px solid #3182CE; overflow: hidden;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <div style="margin-bottom: 20px;">
                                            <div style="font-size: 28px; font-weight: 700; color: #3182CE; margin-bottom: 8px; letter-spacing: -0.5px;"><?php echo e($lead->full_name); ?></div>
                                            <div style="font-size: 16px; color: #4A5568; margin-bottom: 5px;"><?php echo e($lead->company_name); ?></div>
                                            <div style="font-size: 14px; color: #718096;">
                                                <?php
                                                    $priorityColors = [
                                                        'high' => '#F56565',
                                                        'medium' => '#ED8936', 
                                                        'low' => '#48BB78'
                                                    ];
                                                    $priorityColor = $priorityColors[$lead->priority] ?? '#718096';
                                                ?>
                                                <span class="status-badge" style="background-color: <?php echo e($priorityColor); ?>;"><?php echo e(ucfirst($lead->priority)); ?> Priority</span>
                                                <span style="margin: 0 8px;">•</span>
                                                <span style="text-transform: capitalize;"><?php echo e(str_replace('_', ' ', $lead->status)); ?></span>
                                            </div>
                                        </div>
                                        
                                        <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">📧 Email</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    <a href="mailto:<?php echo e($lead->email); ?>" style="color: #3182CE; text-decoration: none;"><?php echo e($lead->email); ?></a>
                                                </td>
                                            </tr>
                                            <?php if($lead->mobile || $lead->phone): ?>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">📱 Phone</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">
                                                    <a href="tel:<?php echo e($lead->mobile ?: $lead->phone); ?>" style="color: #3182CE; text-decoration: none;"><?php echo e($lead->mobile ?: $lead->phone); ?></a>
                                                </td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">📊 Source</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;"><?php echo e(ucfirst(str_replace('_', ' ', $lead->source))); ?></td>
                                            </tr>
                                            <?php if($lead->estimated_value): ?>
                                            <tr>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; font-weight: 600; color: #4A5568; font-size: 15px;">💰 Est. Value</td>
                                                <td style="padding: 12px 0; border-bottom: 1px solid #CBD5E0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;">$<?php echo e(number_format($lead->estimated_value, 2)); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($lead->expected_close_date): ?>
                                            <tr>
                                                <td style="padding: 12px 0; font-weight: 600; color: #4A5568; font-size: 15px;">📅 Expected Close</td>
                                                <td style="padding: 12px 0; color: #1A202C; text-align: right; font-weight: 500; font-size: 15px;"><?php echo e($lead->expected_close_date->format('M j, Y')); ?></td>
                                            </tr>
                                            <?php endif; ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <?php if($lead->notes): ?>
                            <!-- Notes Section -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #EBF8FF 0%, #BEE3F8 100%); border-left: 4px solid #3182CE; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #2B6CB0; margin-bottom: 8px; font-size: 16px;">📝 Lead Notes</div>
                                        <p style="margin: 0; color: #2D3748; line-height: 1.6;"><?php echo e($lead->notes); ?></p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <?php if(!$isNewLead && ($previousAssignee || $reassignedBy)): ?>
                            <!-- Assignment History -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%); border-left: 4px solid #F59E0B; margin: 25px 0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-weight: 600; color: #92400E; margin-bottom: 8px; font-size: 16px;">🔄 Assignment History</div>
                                        <p style="margin: 0 0 8px 0; color: #451A03; line-height: 1.6;">
                                            <strong>Previous Assignee:</strong> <?php echo e($previousAssignee ? $previousAssignee->name : 'Unassigned'); ?>

                                        </p>
                                        <?php if($reassignedBy): ?>
                                        <p style="margin: 0 0 8px 0; color: #451A03; line-height: 1.6;">
                                            <strong>Reassigned By:</strong> <?php echo e($reassignedBy->name); ?>

                                        </p>
                                        <?php endif; ?>
                                        <p style="margin: 0; color: #451A03; line-height: 1.6;">
                                            <strong>Reassigned On:</strong> <?php echo e(now()->format('M j, Y \a\t g:i A')); ?>

                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <!-- Action Button - Outlook Compatible -->
                            <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; margin: 30px 0;">
                                <tr>
                                    <td align="center">
                                        <!--[if mso]>
                                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="<?php echo e(url('/crm/leads/' . $lead->id)); ?>" style="height:50px;v-text-anchor:middle;width:250px;" arcsize="20%" strokecolor="#3182CE" fillcolor="#3182CE">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:'Segoe UI',sans-serif;font-size:18px;font-weight:600;">View Lead Details</center>
                                        </v:roundrect>
                                        <![endif]-->
                                        <!--[if !mso]><!-->
                                        <a href="<?php echo e(url('/crm/leads/' . $lead->id)); ?>" style="display: inline-block; padding: 15px 30px; background-color: #3182CE; background: linear-gradient(135deg, #3182CE 0%, #2C5AA0 100%); color: #FFFFFF; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 18px; text-align: center; min-width: 200px; box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);">
                                            👁️ View Lead Details
                                        </a>
                                        <!--<![endif]-->
                                    </td>
                                </tr>
                            </table>

                            <!-- Message -->
                            <div style="margin: 30px 0;">
                                <p style="margin: 0 0 15px 0; color: #4A5568; font-size: 16px; line-height: 1.7;">
                                    <?php if($isNewLead): ?>
                                        🚀 Please review this new lead and initiate contact as soon as possible to ensure the best conversion rate.
                                    <?php else: ?>
                                        🔄 Please continue the follow-up process for this reassigned lead and maintain the momentum in the sales cycle.
                                    <?php endif; ?>
                                </p>
                                <p style="margin: 0; color: #2D3748; font-size: 16px; font-weight: 500;">If you have any questions about this lead, please don't hesitate to contact your manager or the CRM team. 💬</p>
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
                                    📧 <a href="mailto:sales@maxmedme.com" style="color: #3182CE; text-decoration: none;">sales@maxmedme.com</a> | 
                                    🌐 <a href="http://www.maxmedme.com" style="color: #3182CE; text-decoration: none;">www.maxmedme.com</a>
                                </div>
                            </div>
                            <div style="font-size: 12px; opacity: 0.7; border-top: 1px solid #2D3748; padding-top: 15px; margin-top: 15px;">
                                This is an automated email from the MaxMed CRM system. Please do not reply directly to this message.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/emails/lead-assignment.blade.php ENDPATH**/ ?>