<x-mail::message>
# Registration Verified

Hello {{ $registration->full_name }},

Great news! Your online vehicle registration for the SmartGate system has been **verified** by our office.

**Vehicle Details:**
- **Brand:** {{ $registration->make_brand }}
- **Plate Number:** {{ $registration->plate_number }}

To complete your registration and receive your physical RFID tag, please proceed to the **University Office/Registrar** with your original documents.

Our staff will assist you in assigning the RFID tag to your vehicle.

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
