Dear {{ $contact->first_name ?? 'Valued Customer' }},

IMPORTANT BUSINESS COMMUNICATION

{!! $content !!}

Best regards,
{{ config('app.name') }} Team
Business Communication Department

---
This is an important business communication from {{ config('app.name') }} 
regarding medical equipment and laboratory solutions relevant to your business operations.

{{ config('app.name') }}
{{ config('app.url') }}

To update your communication preferences: {{ $unsubscribe_url }} 