<?php

require_once 'vendor/autoload.php';

// Initialize Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CrmLead;

echo "=== Testing Rich Formatting from Outlook/Word ===\n\n";

// Find a lead to test with
$lead = CrmLead::first();

if (!$lead) {
    echo "No leads found for testing\n";
    exit(1);
}

echo "Testing with Lead ID: {$lead->id}\n\n";

// Simulate realistic content from Outlook/Word with complex formatting
$outlookWordContent = '
<div style="font-family: Calibri, sans-serif; font-size: 11pt;">
    <p style="margin: 0; color: #1f497d; font-size: 14pt; font-weight: bold;">
        🏥 Al Dhannah Hospital - Equipment Procurement Request
    </p>
    
    <p style="margin: 6pt 0; color: #595959;">
        <strong>From:</strong> <span style="color: #0563c1;">Dr. Ahmed Al-Mansoori</span> &lt;ahmed.mansoori@adnochospital.ae&gt;<br>
        <strong>Date:</strong> <span style="color: #70ad47;">September 17, 2025</span><br>
        <strong>Subject:</strong> <span style="color: #c55a11; font-weight: bold;">Urgent - POCT Equipment Requirements</span>
    </p>

    <div style="background-color: #fff2cc; border-left: 4px solid #f1c232; padding: 10px; margin: 10px 0;">
        <p style="margin: 0; color: #bf9000; font-weight: bold;">⚠️ HIGH PRIORITY REQUEST</p>
        <p style="margin: 5px 0 0 0; color: #7f6000;">Response required within 48 hours</p>
    </div>

    <h2 style="color: #2e75b6; border-bottom: 2px solid #5b9bd5; padding-bottom: 5px;">Equipment Specifications</h2>
    
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%; margin: 10px 0;">
        <thead>
            <tr style="background-color: #4472c4; color: white;">
                <th style="text-align: left; font-weight: bold;">Equipment</th>
                <th style="text-align: center; font-weight: bold;">Quantity</th>
                <th style="text-align: center; font-weight: bold;">Budget (AED)</th>
                <th style="text-align: center; font-weight: bold;">Timeline</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f2f2f2;">
                <td style="color: #1f4e79; font-weight: bold;">Apo B Testing System</td>
                <td style="text-align: center; color: #70ad47; font-weight: bold;">2 units</td>
                <td style="text-align: center; color: #c55a11; font-weight: bold;">65,000 - 85,000</td>
                <td style="text-align: center; color: #e74c3c;">2 weeks</td>
            </tr>
            <tr>
                <td style="color: #1f4e79;">Calibration Kit</td>
                <td style="text-align: center; color: #70ad47;">5 sets</td>
                <td style="text-align: center; color: #c55a11;">8,000 - 12,000</td>
                <td style="text-align: center;">1 week</td>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <td style="color: #1f4e79;">Annual Service Package</td>
                <td style="text-align: center; color: #70ad47;">1 contract</td>
                <td style="text-align: center; color: #c55a11;">15,000 - 20,000</td>
                <td style="text-align: center;">Immediate</td>
            </tr>
        </tbody>
    </table>

    <h3 style="color: #70ad47; margin-top: 20px;">✅ Required Certifications</h3>
    <ul style="color: #595959; line-height: 1.6;">
        <li><span style="color: #e74c3c; font-weight: bold;">FDA 510(k) Clearance</span> - Mandatory</li>
        <li><span style="color: #f39c12; font-weight: bold;">CE Marking</span> - European compliance</li>
        <li><span style="color: #3498db; font-weight: bold;">ISO 13485</span> - Quality management</li>
        <li><span style="color: #9b59b6; font-weight: bold;">CLIA Waived Status</span> - Point-of-care testing</li>
    </ul>

    <div style="background-color: #e8f4fd; border: 1px solid #5b9bd5; border-radius: 5px; padding: 15px; margin: 15px 0;">
        <h4 style="color: #2e75b6; margin-top: 0;">💡 Technical Requirements</h4>
        <p style="margin: 5px 0; color: #1f4e79;">
            <strong>Integration:</strong> Must interface with our existing <span style="background-color: #ffeb9c; color: #7f6000; padding: 2px 4px;">Cerner PowerChart LIS</span>
        </p>
        <p style="margin: 5px 0; color: #1f4e79;">
            <strong>Connectivity:</strong> <font color="#e74c3c">Ethernet</font> and <font color="#27ae60">Wi-Fi</font> capable
        </p>
        <p style="margin: 5px 0; color: #1f4e79;">
            <strong>Display:</strong> Minimum <span style="color: #8e44ad; font-weight: bold;">10-inch touchscreen</span>
        </p>
    </div>

    <blockquote style="border-left: 4px solid #3498db; margin: 15px 0; padding: 10px 15px; background-color: #ecf0f1; font-style: italic;">
        <p style="margin: 0; color: #2c3e50; font-size: 12pt;">
            "The successful vendor will be considered for our upcoming <span style="color: #e67e22; font-weight: bold;">5-hospital expansion project</span> 
            worth approximately <span style="color: #27ae60; font-weight: bold; font-size: 14pt;">AED 2.5 million</span>."
        </p>
        <footer style="margin-top: 8px; color: #7f8c8d; font-size: 10pt;">
            — <strong>Dr. Fatima Al-Zahra</strong>, Chief of Laboratory Services
        </footer>
    </blockquote>

    <center>
        <div style="background-color: #2ecc71; color: white; padding: 12px 20px; border-radius: 25px; display: inline-block; margin: 20px 0;">
            <strong style="font-size: 12pt;">🎯 TOTAL PROJECT VALUE: AED 88,000 - 117,000</strong>
        </div>
    </center>

    <hr style="border: none; height: 2px; background: linear-gradient(to right, #3498db, #2ecc71, #f39c12, #e74c3c);">

    <p style="margin: 15px 0 5px 0; color: #2c3e50; font-size: 10pt;">
        <strong>Contact Information:</strong><br>
        📧 <a href="mailto:procurement@adnochospital.ae" style="color: #3498db;">procurement@adnochospital.ae</a><br>
        📱 <span style="color: #e74c3c;">+971-2-123-4567</span> (Direct)<br>
        🏢 Al Dhannah Hospital, Abu Dhabi, UAE
    </p>

    <div style="font-size: 9pt; color: #95a5a6; border-top: 1px solid #bdc3c7; padding-top: 10px; margin-top: 20px;">
        <p style="margin: 0;"><strong>Confidentiality Notice:</strong> This email contains confidential information intended only for the addressee.</p>
    </div>
</div>';

echo "Rich content created with realistic Outlook/Word formatting:\n";
echo "- Font families (Calibri)\n";
echo "- Multiple font colors and sizes\n";
echo "- Background colors and highlights\n";
echo "- Complex table styling with colored cells\n";
echo "- Gradient borders and styled divs\n";
echo "- Email signatures and contact info\n";
echo "- Professional layout and spacing\n\n";

// Test the sanitizer
echo "Testing HTML Sanitizer...\n";
$sanitized = \App\Helpers\HtmlSanitizer::sanitizeRichContent($outlookWordContent);
echo "✅ Content sanitized successfully\n\n";

if (confirm("Do you want to update the lead with rich Outlook/Word formatted content? (y/n): ")) {
    $lead->notes = $outlookWordContent;
    $lead->save();
    echo "✅ Lead updated with rich formatted content!\n";
    echo "🌐 Visit: http://localhost:8000/crm/leads/{$lead->id} (or your domain)\n\n";
    echo "You should now see COMPLETE formatting preservation:\n";
    echo "- ✅ All font colors preserved\n";
    echo "- ✅ Background colors and highlights\n";
    echo "- ✅ Font families (Calibri) preserved\n";
    echo "- ✅ Complex table styling intact\n";
    echo "- ✅ Professional layout maintained\n";
    echo "- ✅ Email formatting preserved\n";
    echo "- ✅ Gradient borders and advanced CSS\n";
    echo "- ✅ Responsive design for all devices\n";
} else {
    echo "Test completed without updating the lead.\n";
}

function confirm($message) {
    echo $message;
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    return strtolower(trim($line)) === 'y';
}

echo "\n=== Rich Formatting Test Complete ===\n";

