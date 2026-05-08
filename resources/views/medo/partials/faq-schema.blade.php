@if(isset($faqs) && $faqs->count() > 0)
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    @foreach($faqs as $faq)
    {
      "@type": "Question",
      "name": "{{ addslashes($faq->question) }}",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "{{ addslashes(strip_tags($faq->answer)) }}"
      }
    }@if(!$loop->last),@endif
    @endforeach
  ]
}
</script>
@endif
