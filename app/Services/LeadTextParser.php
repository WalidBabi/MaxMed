<?php

namespace App\Services;

class LeadTextParser
{
	public function parse(string $text): array
	{
		$normalized = $this->normalizeWhitespace($text);
		$emails = $this->extractEmails($normalized);
		$phones = $this->extractPhones($normalized);
		$name = $this->extractName($normalized);
		$company = $this->extractCompany($normalized, $emails[0] ?? null);

		[$firstName, $lastName] = $this->splitName($name);

		return [
			'first_name' => $firstName,
			'last_name' => $lastName,
			'email' => $emails[0] ?? null,
			'mobile' => $phones[0] ?? null,
			'phone' => $phones[1] ?? null,
			'company_name' => $company,
			'emails' => $emails,
			'phones' => $phones,
		];
	}

	private function normalizeWhitespace(string $text): string
	{
		$text = preg_replace("/\r\n|\r/", "\n", $text);
		// collapse 3+ newlines to two to keep signature blocks but reduce noise
		$text = preg_replace("/\n{3,}/", "\n\n", $text);
		return $text;
	}

	private function extractEmails(string $text): array
	{
		$emails = [];
		if (preg_match_all('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text, $m)) {
			$emails = array_values(array_unique($m[0]));
		}
		return $emails;
	}

	private function extractPhones(string $text): array
	{
		// Match common international and local formats, keep order of appearance
		$pattern = '/(?:(?:\+|00)\s?\d{1,3}[\s-]?)?(?:\(?\d{2,4}\)?[\s-]?)?\d{3,4}[\s-]?\d{3,4}/';
		$phones = [];
		if (preg_match_all($pattern, $text, $m)) {
			foreach ($m[0] as $raw) {
				$normalized = $this->normalizePhone($raw);
				if ($normalized !== null) {
					$phones[] = $normalized;
				}
			}
		}
		// unique while preserving order
		$phones = array_values(array_unique($phones));
		return $phones;
	}

	private function normalizePhone(string $raw): ?string
	{
		// Remove non-digits except leading plus
		$raw = trim($raw);
		$hasPlus = str_starts_with($raw, '+');
		$digits = preg_replace('/\D+/', '', $raw);
		if ($digits === null) {
			return null;
		}
		// Basic length sanity (8..15 digits)
		$len = strlen($digits);
		if ($len < 8 || $len > 15) {
			return null;
		}
		return $hasPlus ? '+' . $digits : $digits;
	}

	private function extractName(string $text): ?string
	{
		// Look for common signature closings followed by a name
		$lines = array_values(array_filter(array_map('trim', explode("\n", $text)), fn($l) => $l !== ''));
		$signatureMarkers = ['regards', 'best regards', 'thanks', 'thank you', 'sincerely', 'cheers'];
		for ($i = 0; $i < count($lines); $i++) {
			$line = strtolower($lines[$i]);
			foreach ($signatureMarkers as $m) {
				if (str_starts_with($line, $m)) {
					$next = $lines[$i + 1] ?? '';
					$name = $this->cleanPersonName($next);
					if ($name) return $name;
				}
			}
		}

		// Patterns: "I'm John Doe", "My name is John Doe"
		if (preg_match("/(?:i'm|i am|my name is)\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,2})/i", $text, $m)) {
			return $this->cleanPersonName($m[1]);
		}

		// Single capitalized full line heuristics (avoid company words)
		foreach ($lines as $line) {
			if (preg_match('/^[A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,2}$/', $line)) {
				return $this->cleanPersonName($line);
			}
		}

		return null;
	}

	private function cleanPersonName(string $name): ?string
	{
		$name = trim($name);
		$name = preg_replace('/[,|].*$/', '', $name); // cut after commas/pipes
		$name = preg_replace('/\s+/', ' ', $name);
		// Filter out obvious non-names
		if ($name === '' || preg_match('/\d/', $name)) return null;
		return $name;
	}

	private function extractCompany(string $text, ?string $email): ?string
	{
		// Explicit labels
		if (preg_match('/(?:company|organisation|organization)\s*[:\-]\s*(.+)/i', $text, $m)) {
			$val = trim($m[1]);
			$val = preg_split('/\r?\n/', $val)[0] ?? $val;
			return $this->cleanCompany($val);
		}

		// From signature block line after name
		$lines = array_map('trim', explode("\n", $text));
		for ($i = 0; $i < count($lines) - 1; $i++) {
			if ($this->cleanPersonName($lines[$i])) {
				$candidate = trim($lines[$i + 1]);
				if ($this->looksLikeCompany($candidate)) {
					return $this->cleanCompany($candidate);
				}
			}
		}

		// Derive from email domain
		if ($email && str_contains($email, '@')) {
			[, $domain] = explode('@', strtolower($email), 2);
			$domain = preg_replace('/^www\./', '', $domain);
			$domainParts = explode('.', $domain);
			if (count($domainParts) >= 2) {
				$label = $domainParts[count($domainParts) - 2];
				if (!in_array($label, ['gmail', 'yahoo', 'outlook', 'hotmail', 'icloud', 'live', 'proton', 'aol'])) {
					return $this->cleanCompany($label);
				}
			}
		}

		return null;
	}

	private function looksLikeCompany(string $line): bool
	{
		if ($line === '') return false;
		if (preg_match('/@|\d/', $line)) return false; // likely email/phone, not company
		// Contains words like LLC, LTD, Inc, Company, Trading, etc.
		return (bool) preg_match('/\b(llc|ltd|inc|co\.?|company|trading|industries|corp|gmbh|pte|sarl)\b/i', $line);
	}

	private function cleanCompany(string $name): string
	{
		$name = trim($name);
		$name = preg_replace('/\s+/', ' ', $name);
		return $name;
	}

	private function splitName(?string $fullName): array
	{
		if (!$fullName) return [null, null];
		$parts = preg_split('/\s+/', trim($fullName));
		if (!$parts || count($parts) === 0) return [null, null];
		$first = array_shift($parts);
		$last = count($parts) ? implode(' ', $parts) : null;
		return [$first, $last];
	}
}


