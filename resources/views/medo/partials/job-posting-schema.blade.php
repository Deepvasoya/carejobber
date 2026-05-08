@if(isset($jobs) && $jobs->count() > 0)
@foreach($jobs as $job)
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "JobPosting",
  "title": "{{ addslashes($job->title) }}",
  "description": "{{ addslashes(strip_tags($job->description)) }}",
  "datePosted": "{{ ($job->posted_at ?? $job->created_at)->toIso8601String() }}",
  "validThrough": "{{ $job->expires_at->toIso8601String() }}",
  "employmentType": "{{ strtoupper(str_replace('_', '', $job->employment_type ?? 'FULL_TIME')) }}",
  "hiringOrganization": {
    "@type": "Organization",
    "name": "{{ addslashes($job->employer?->name ?? $job->medoEmployer?->name ?? 'Healthcare Employer') }}"
  },
  "jobLocation": {
    "@type": "Place",
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "{{ $job->city?->name ?? $job->medoCity?->name }}",
      "addressRegion": "{{ $job->province?->code ?? $job->medoProvince?->code }}",
      "addressCountry": "CA"
    }
  }@if($job->wage_min || $job->wage_max),
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "CAD",
    "value": {
      "@type": "QuantitativeValue"@if($job->wage_min),
      "minValue": {{ $job->wage_min }}@endif
@if($job->wage_max),
      "maxValue": {{ $job->wage_max }}@endif
,
      "unitText": "{{ strtoupper($job->wage_period ?? 'HOUR') }}"
    }
  }@endif

}
</script>
@endforeach
@endif
