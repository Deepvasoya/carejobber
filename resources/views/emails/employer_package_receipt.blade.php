<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Receipt') }}</title>
</head>
<body style="font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.5; color: #1e293b; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 1.25rem; margin: 0 0 16px;">{{ __('Thank you for your payment') }}</h1>
    <p style="margin: 0 0 8px;">{{ __('Hi :name,', ['name' => $company->name]) }}</p>
    <p style="margin: 0 0 16px;">{{ __('This email confirms your package purchase.') }}</p>

    <table style="width: 100%; border-collapse: collapse; margin: 16px 0; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <tr style="background: #f8fafc;">
            <th style="text-align: left; padding: 10px 14px; font-weight: 600;">{{ __('Package') }}</th>
            <td style="padding: 10px 14px;">{{ $package->package_title }}</td>
        </tr>
        @if($listPrice !== null && abs($listPrice - $amountPaid) > 0.009)
        <tr>
            <th style="text-align: left; padding: 10px 14px; font-weight: 600;">{{ __('Regular price') }}</th>
            <td style="padding: 10px 14px;">{{ $currencyCode }} {{ number_format($listPrice, 2) }}</td>
        </tr>
        @endif
        <tr>
            <th style="text-align: left; padding: 10px 14px; font-weight: 600;">{{ __('Amount paid') }}</th>
            <td style="padding: 10px 14px;"><strong>{{ $currencyCode }} {{ number_format($amountPaid, 2) }}</strong></td>
        </tr>
        <tr style="background: #f8fafc;">
            <th style="text-align: left; padding: 10px 14px; font-weight: 600;">{{ __('Reference') }}</th>
            <td style="padding: 10px 14px; font-size: 0.875rem; word-break: break-all;">{{ $transactionId }}</td>
        </tr>
    </table>

    <p style="margin: 24px 0 0; font-size: 0.875rem; color: #64748b;">{{ __('If you have questions, reply to this email or contact support.') }}</p>
</body>
</html>
