<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CrmLead;
use App\Services\LeadTextParser;
use App\Services\LeadTextEnricher;

class InboundLeadWebhookController extends Controller
{
	private LeadTextParser $parser;
	private LeadTextEnricher $enricher;

	public function __construct(LeadTextParser $parser, LeadTextEnricher $enricher)
	{
		$this->parser = $parser;
		$this->enricher = $enricher;
	}

	public function whatsapp(Request $request)
	{
		// Verification (GET)
		if ($request->isMethod('get') && $request->has('hub_challenge')) {
			if ($request->query('hub_verify_token') === env('META_VERIFY_TOKEN')) {
				return response($request->query('hub_challenge'), 200)
					->header('Content-Type', 'text/plain');
			}
			return response('', 403);
		}

		$payload = $request->all();
		$message = data_get($payload, 'entry.0.changes.0.value.messages.0');
		if (!$message) {
			return response()->json(['ok' => true]);
		}

		$phone = data_get($message, 'from');
		$body = data_get($message, 'text.body', '');

		$parsed = $this->enricher->extract($body);
		$category = $parsed['category'] ?? 'new_inquiry';
		$firstName = $parsed['first_name'] ?? null;
		$lastName = $parsed['last_name'] ?? null;
		$email = $parsed['email'] ?? null;
		$mobile = $parsed['mobile'] ?? ($parsed['phones'][0] ?? null);
		$company = $parsed['company_name'] ?? null;

		$assignedId = (int) env('DEFAULT_LEAD_ASSIGNEE_ID', 1);

		if ($category !== 'new_inquiry') {
			Log::info('Inbound WhatsApp message not categorized as new_inquiry; skipping lead create', [
				'category' => $category,
			]);
			return response()->json(['ok' => true, 'category' => $category, 'skipped' => true, 'parsed' => $parsed]);
		}

		CrmLead::firstOrCreate(
			[
				'source' => 'whatsapp',
				'email' => $email,
				'mobile' => $mobile ?? $phone,
			],
			[
				'first_name' => $firstName ?? 'WhatsApp',
				'last_name' => $lastName ?? 'Contact',
				'phone' => $phone,
				'company_name' => $company ?? 'Not Specified',
				'priority' => 'medium',
				'notes' => $body,
				'status' => 'new_inquiry',
				'assigned_to' => $assignedId,
			]
		);

		return response()->json(['ok' => true, 'parsed' => $parsed]);
	}

	public function outlook(Request $request)
	{
		// Subscription validation will send validationToken as query param
		if ($request->isMethod('get') && $request->has('validationToken')) {
			return response($request->query('validationToken'), 200)
				->header('Content-Type', 'text/plain');
		}

		$notifications = (array) $request->input('value', []);
		if (empty($notifications)) {
			// Fallback: accept direct payload for testing
			$subject = (string) $request->input('subject', '');
			$body = (string) $request->input('body', $request->input('bodyPreview', ''));
			$from = (string) $request->input('from', '');
			return $this->ingestEmailLike($from, $subject, $body);
		}

		// In production, you should fetch the message via Graph using the resource id in each notification.
		foreach ($notifications as $note) {
			$from = data_get($note, 'resourceData.from.emailAddress.address');
			$subject = (string) data_get($note, 'resourceData.subject', '');
			$preview = (string) data_get($note, 'resourceData.bodyPreview', '');
			$this->ingestEmailLike($from, $subject, $preview);
		}

		return response()->json(['ok' => true]);
	}

	public function parsePreview(Request $request)
	{
		$text = (string) $request->input('text', '');
		$regex = $this->parser->parse($text);
		$enriched = $this->enricher->extract($text);
		return response()->json(['regex' => $regex, 'enriched' => $enriched]);
	}

	private function ingestEmailLike(?string $from, string $subject, string $body)
	{
		$email = $this->extractEmailFromFromHeader($from);
		$displayName = $this->extractNameFromFromHeader($from);
		$parsed = $this->enricher->extract(trim($subject . "\n\n" . $body));
		$category = $parsed['category'] ?? 'new_inquiry';

		$firstName = $parsed['first_name'] ?? null;
		$lastName = $parsed['last_name'] ?? null;
		if (!$firstName && $displayName) {
			[$firstName, $lastName] = $this->splitName($displayName);
		}

		$assignedId = (int) env('DEFAULT_LEAD_ASSIGNEE_ID', 1);

		if ($category !== 'new_inquiry') {
			Log::info('Inbound email not categorized as new_inquiry; skipping lead create', [
				'category' => $category,
				'from' => $from,
				'subject' => $subject,
			]);
			return response()->json([
				'ok' => true,
				'category' => $category,
				'skipped' => true,
				'parsed' => $parsed,
			]);
		}

		CrmLead::firstOrCreate(
			[
				'source' => 'email',
				'email' => $parsed['email'] ?? $email,
			],
			[
				'first_name' => $firstName ?? 'Email',
				'last_name' => $lastName ?? 'Contact',
				'phone' => $parsed['mobile'] ?? ($parsed['phones'][0] ?? null),
				'company_name' => $parsed['company_name'] ?? 'Not Specified',
				'priority' => 'medium',
				'notes' => trim($subject . "\n\n" . $body),
				'status' => 'new_inquiry',
				'assigned_to' => $assignedId,
			]
		);

		return response()->json(['ok' => true, 'parsed' => $parsed]);
	}

	private function extractEmailFromFromHeader(?string $from): ?string
	{
		if (!$from) return null;
		if (preg_match('/<([^>]+@[^>]+)>/', $from, $m)) {
			return $m[1];
		}
		if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $from, $m)) {
			return $m[0];
		}
		return null;
	}

	private function extractNameFromFromHeader(?string $from): ?string
	{
		if (!$from) return null;
		// Pattern: Display Name <email@domain>
		if (preg_match('/^\"?([^<\"]+)\"?\s*<[^>]+>$/', $from, $m)) {
			return trim($m[1]);
		}
		// If only name without angle brackets
		if (!str_contains($from, '@') && !str_contains($from, '<')) {
			return trim($from);
		}
		return null;
	}

	private function splitName(?string $fullName): array
	{
		if (!$fullName) return [null, null];
		$parts = preg_split('/\s+/', trim($fullName));
		$first = array_shift($parts);
		$last = count($parts) ? implode(' ', $parts) : null;
		return [$first, $last];
	}
}


