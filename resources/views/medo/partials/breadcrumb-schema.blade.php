<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    @foreach($items as $index => $item)
    {
      "@type": "ListItem",
      "position": {{ $index + 1 }},
      "name": "{{ $item['label'] }}"@if($item['url']),
      "item": "{{ $item['url'] }}"@endif
    }@if(!$loop->last),@endif
    @endforeach
  ]
}
</script>
