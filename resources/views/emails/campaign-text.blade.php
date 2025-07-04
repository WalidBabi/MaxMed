Dear {{ $contact->first_name ?? 'Valued Customer' }},

{!! $content !!}

Best regards,
{{ config('app.name') }} Team

---
Business Communication
{{ config('app.name') }}
{{ config('app.url') }}

To update your communication preferences: {{ $unsubscribe_url }} 