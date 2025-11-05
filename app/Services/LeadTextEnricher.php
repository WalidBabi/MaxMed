<?php

namespace App\Services;

class LeadTextEnricher
{
    private LeadTextParser $parser;
    private LeadLLMClient $llm;

    public function __construct(LeadTextParser $parser, LeadLLMClient $llm)
    {
        $this->parser = $parser;
        $this->llm = $llm;
    }

    public function extract(string $text): array
    {
        $regex = $this->parser->parse($text);
        if (!$this->llm->enabled()) {
            // Default to new_inquiry when LLM is disabled to preserve current behavior
            $regex['category'] = $regex['category'] ?? 'new_inquiry';
            return $regex;
        }

        $llm = $this->llm->extract($text);
        if (empty($llm)) {
            return $regex;
        }

        return $this->merge($regex, $llm);
    }

    private function merge(array $regex, array $llm): array
    {
        $result = $regex;

        $result['first_name'] = $result['first_name'] ?? ($llm['first_name'] ?? null);
        $result['last_name'] = $result['last_name'] ?? ($llm['last_name'] ?? null);
        $result['email'] = $result['email'] ?? ($llm['email'] ?? null);
        $result['company_name'] = $result['company_name'] ?? ($llm['company_name'] ?? null);

        // phones: union preserving order (regex first, then llm)
        $phones = array_values(array_unique(array_filter(array_merge(
            $result['phones'] ?? [],
            $llm['phones'] ?? []
        ))));
        $result['phones'] = $phones;

        // extras from LLM
        if (isset($llm['intent'])) {
            $result['intent'] = $llm['intent'];
        }
        if (isset($llm['category'])) {
            $result['category'] = $llm['category'];
        }
        if (isset($llm['urgency'])) {
            $result['urgency'] = $llm['urgency'];
        }
        if (isset($llm['products_interested']) && is_array($llm['products_interested'])) {
            $result['products_interested'] = $llm['products_interested'];
        }
        if (isset($llm['notes'])) {
            $result['notes_extracted'] = $llm['notes'];
        }

        // convenience fields
        $result['mobile'] = $result['mobile'] ?? ($phones[0] ?? null);
        $result['phone'] = $result['phone'] ?? ($phones[1] ?? null);

        // Derive category from intent if not provided
        if (!isset($result['category'])) {
            $intent = $result['intent'] ?? ($llm['intent'] ?? null);
            $result['category'] = $this->deriveCategoryFromIntent($intent);
        }

        return $result;
    }

    private function deriveCategoryFromIntent(?string $intent): string
    {
        if (!$intent) {
            return 'other';
        }
        $intent = strtolower($intent);
        if (in_array($intent, ['quote_request', 'general_inquiry'], true)) {
            return 'new_inquiry';
        }
        if ($intent === 'support_request') {
            return 'support';
        }
        if ($intent === 'vendor_offer') {
            return 'vendor_offer';
        }
        if ($intent === 'job_application') {
            return 'job_application';
        }
        if ($intent === 'spam') {
            return 'spam';
        }
        return 'other';
    }
}



