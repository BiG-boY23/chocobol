<x-mail::message>
# Registration Update

Hello {{ $registration->full_name }},

We have reviewed your online vehicle registration. Unfortunately, your application could not be verified at this time.

**Reason for Rejection:**
{{ $registration->rejection_reason }}

Please review the reason above and submit a new registration with the correct documents.

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
