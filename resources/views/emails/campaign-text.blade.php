Dear {{ $contact->first_name ?? 'Valued Customer' }},

IMPORTANT BUSINESS COMMUNICATION
MaxMed Healthcare Supplies - Business Update

{!! $content !!}

Best regards,
{{ config('app.name') }} Team
Business Communication Department
Healthcare Supplies Division

---
BUSINESS COMMUNICATION NOTICE:
This is an important business communication from {{ config('app.name') }} 
regarding medical equipment and laboratory solutions relevant to your business operations.
This communication is sent to business contacts and is not promotional marketing material.

{{ config('app.name') }}
Healthcare Supplies & Medical Equipment
{{ config('app.url') }}

For business inquiries: {{ config('mail.campaign_from.address') }}
To update your communication preferences: {{ $unsubscribe_url }} 