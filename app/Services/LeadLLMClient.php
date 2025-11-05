<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LeadLLMClient
{
    public function enabled(): bool
    {
        return filter_var(env('LEAD_LLM_ENABLED', false), FILTER_VALIDATE_BOOL);
    }

    public function extract(string $text): array
    {
        if (!$this->enabled()) {
            return [];
        }

        $baseUrl = rtrim(env('LEAD_LLM_BASE_URL', ''), '/');
        $model = env('LEAD_LLM_MODEL', 'llama3.2:3b');
        $apiKey = env('LEAD_LLM_API_KEY');
        $timeout = (int) env('LEAD_LLM_TIMEOUT_SECONDS', 12);

        if ($baseUrl === '') {
            return [];
        }

        $headers = [
            'Content-Type' => 'application/json',
        ];
        if (!empty($apiKey)) {
            $headers['Authorization'] = 'Bearer ' . $apiKey;
        }

        $schema = [
            'type' => 'object',
            'properties' => [
                'first_name' => ['type' => ['string', 'null']],
                'last_name' => ['type' => ['string', 'null']],
                'email' => ['type' => ['string', 'null']],
                'phones' => ['type' => 'array', 'items' => ['type' => 'string']],
                'company_name' => ['type' => ['string', 'null']],
                'intent' => ['type' => ['string', 'null']],
                'category' => ['type' => ['string', 'null']],
                'urgency' => ['type' => ['string', 'null']],
                'products_interested' => ['type' => 'array', 'items' => ['type' => 'string']],
                'notes' => ['type' => ['string', 'null']],
            ],
            'required' => [],
            'additionalProperties' => false,
        ];

        $system = 'You are a data extraction service for a CRM. Extract structured fields and classify the email category. Return ONLY valid JSON, no extra text.';

        $user = "Extract information from this text and return JSON with these keys:\n" .
            "first_name, last_name, email, phones (array of strings), company_name, intent (one of: quote_request, general_inquiry, support_request, spam, vendor_offer, job_application, other), category (one of: new_inquiry, support, vendor_offer, job_application, spam, internal, other), urgency (low|medium|high), products_interested (array of strings), notes.\n\n" .
            "Classification rule: Map quote_request or general_inquiry to category=new_inquiry. If unclear, use other.\n\n" .
            "Text:\n" . $text;

        try {
            // OpenAI-compatible chat endpoint
            $payload = [
                'model' => $model,
                'temperature' => (float) env('LEAD_LLM_TEMPERATURE', 0.1),
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
            ];
            
            // Only add response_format if supported (LM Studio might not support it)
            // Most models work better with just the prompt asking for JSON
            $resp = Http::withHeaders($headers)
                ->timeout($timeout)
                ->post($baseUrl . '/v1/chat/completions', $payload);

            if (!$resp->ok()) {
                return [];
            }

            $data = $resp->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            if (!is_string($content) || $content === '') {
                return [];
            }

            // Clean up content - remove markdown code blocks if present
            $content = trim($content);
            if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $content, $matches)) {
                $content = $matches[1];
            }
            
            $parsed = json_decode($content, true);
            return is_array($parsed) ? $parsed : [];
        } catch (\Throwable $e) {
            \Log::warning('LLM extraction failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return [];
        }
    }
}


