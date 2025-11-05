<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Webhook\InboundLeadWebhookController;
use Illuminate\Http\Request;

class TestLeadEmailParser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:lead-email-parser 
                            {--url= : Base URL of the application (defaults to APP_URL from config)}
                            {--from=John Doe <john.doe@example.com> : Email sender}
                            {--subject= : Email subject (optional)}
                            {--body= : Email body (optional)}
                            {--internal : Test directly without HTTP request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to the CRM lead addition parser webhook to test LLM extraction';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $useInternal = $this->option('internal');
        $baseUrl = $this->option('url') ? rtrim($this->option('url'), '/') : config('app.url');
        $webhookUrl = $baseUrl . '/webhooks/outlook';
        
        $from = $this->option('from');
        $subject = $this->option('subject');
        $body = $this->option('body');
        
        // Use default test data if not provided
        if (empty($subject)) {
            $subject = 'Inquiry about Medical Equipment - Urgent Quote Request';
        }
        
        if (empty($body)) {
            $body = <<<EMAIL
Hello,

My name is Sarah Johnson and I'm contacting you from MedCare Hospital in Dubai. 
I'm interested in getting a quote for medical equipment.

Contact Information:
- Email: sarah.johnson@medcarehospital.ae
- Phone: +971 50 123 4567
- Mobile: 050-123-4567
- Company: MedCare Hospital

I need urgent pricing information for the following products:
1. Ventilators - we need 5 units ASAP
2. Patient monitors
3. Ultrasound machines

This is a high priority request as we need to make a purchasing decision within the next week. 
Please contact me at your earliest convenience.

Best regards,
Sarah Johnson
Procurement Manager
MedCare Hospital
+971 50 123 4567
EMAIL;
        }

        $this->info('🧪 Testing CRM Lead Email Parser with LLM');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();
        $this->info("📧 Webhook URL: {$webhookUrl}");
        $this->info("👤 From: {$from}");
        $this->info("📝 Subject: {$subject}");
        $this->newLine();
        $this->info("📄 Email Body:");
        $this->line($body);
        $this->newLine();
        
        $payload = [
            'from' => $from,
            'subject' => $subject,
            'body' => $body,
        ];

        try {
            if ($useInternal) {
                $this->info('📤 Testing internally (direct controller call)...');
                $this->newLine();
                
                // Test directly using the controller
                $controller = app(InboundLeadWebhookController::class);
                $request = Request::create('/webhooks/outlook', 'POST', $payload);
                
                $response = $controller->outlook($request);
                
                // Handle JsonResponse
                if (method_exists($response, 'getData')) {
                    $responseData = $response->getData(true);
                } elseif (method_exists($response, 'getContent')) {
                    $content = $response->getContent();
                    $responseData = json_decode($content, true) ?? [];
                } else {
                    $responseData = ['ok' => true];
                }
                
                $this->info("✅ Controller executed successfully");
                $this->newLine();
            } else {
                $this->info('📤 Sending POST request to webhook...');
                $this->info("   URL: {$webhookUrl}");
                $this->newLine();
                
                $response = Http::timeout(30)
                    ->post($webhookUrl, $payload);
                
                $statusCode = $response->status();
                
                if (!$response->successful()) {
                    $this->error("❌ Webhook returned error (Status: {$statusCode})");
                    $this->error('Response: ' . $response->body());
                    $this->newLine();
                    $this->warn('💡 Tip: Try using --internal flag to test directly without HTTP request');
                    $this->warn('   Example: php artisan test:lead-email-parser --internal');
                    return 1;
                }
                
                $responseData = $response->json();
                $this->info("✅ Webhook responded successfully (Status: {$statusCode})");
                $this->newLine();
            }
            
                if (isset($responseData['parsed'])) {
                $this->info('📊 LLM Parsed Data:');
                $this->newLine();
                
                $parsed = $responseData['parsed'];
                $this->table(
                    ['Field', 'Value'],
                    [
                            ['Category', $parsed['category'] ?? ($responseData['category'] ?? 'N/A')],
                        ['First Name', $parsed['first_name'] ?? 'N/A'],
                        ['Last Name', $parsed['last_name'] ?? 'N/A'],
                        ['Email', $parsed['email'] ?? 'N/A'],
                        ['Phone/Mobile', $parsed['mobile'] ?? ($parsed['phones'][0] ?? 'N/A')],
                        ['All Phones', !empty($parsed['phones']) ? implode(', ', $parsed['phones']) : 'N/A'],
                        ['Company Name', $parsed['company_name'] ?? 'N/A'],
                        ['Intent', $parsed['intent'] ?? 'N/A'],
                        ['Urgency', $parsed['urgency'] ?? 'N/A'],
                        ['Products Interested', !empty($parsed['products_interested']) ? implode(', ', $parsed['products_interested']) : 'N/A'],
                        ['Notes', !empty($parsed['notes_extracted']) ? substr($parsed['notes_extracted'], 0, 100) . '...' : 'N/A'],
                    ]
                );
            }
            
            $this->newLine();
            $this->info('📋 Full Response:');
            $this->line(json_encode($responseData, JSON_PRETTY_PRINT));
            
        } catch (\Exception $e) {
            $this->error('❌ Error:');
            $this->error($e->getMessage());
            $this->newLine();
            if (!$useInternal) {
                $this->warn('💡 Tip: Try using --internal flag to test directly');
            }
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        $this->newLine();
        $this->info('✅ Test completed!');
        $this->info('💡 Check the CRM leads table to see if the lead was created.');
        
        return 0;
    }
}

