Dear {{ $application->first_name }} {{ $application->last_name }},

Congratulations again on your successful application!

To complete your admission process, please pay the admission fee:

AMOUNT: {{ number_format($admissionFee) }} {{ $currencyCode }}

PAYMENT DETAILS:
- Bank Name: Stanbic Bank Uganda
- Account Name: JBI University
- Account Number: 9030012345678
- Payment Reference: {{ $application->application_number }}

IMPORTANT: Use your application number ({{ $application->application_number }}) as the payment reference.

After making payment, upload your payment proof here:
{{ route('applications.upload-payment', $application->application_number) }}

For assistance:
Email: info@jbiuniversity.com
Phone: +256-123-456-789

Best regards,
JBI University Finance Office

---
© {{ date('Y') }} JBI University. All rights reserved.
